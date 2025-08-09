<?php
require_once __DIR__ . '/classes/AttendanceManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $shiftIndex = isset($_POST['shift_index']) ? intval($_POST['shift_index']) : -1;

    if ($userId && $date && $shiftIndex >= 0) {
        $attendanceManager = new AttendanceManager();
        $ref = $attendanceManager->database->getReference("attendance/$userId/$date");
        $record = $ref->getValue();

        if ($record && isset($record['shifts'][$shiftIndex])) {
            $shifts = $record['shifts'];
            unset($shifts[$shiftIndex]);
            $shifts = array_values($shifts);
            $ref->update(['shifts' => $shifts]);
        }
    }
}

header("Location: attendance_dashboard.php");
exit;