<?php
require_once __DIR__ . '/classes/AttendanceManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $shiftIndex = isset($_POST['shift_index']) ? intval($_POST['shift_index']) : -1;
    $checkIn = $_POST['check_in'] ?? '';
    $checkOut = $_POST['check_out'] ?? '';

    if ($userId && $date && $shiftIndex >= 0 && $checkIn && $checkOut) {
        $attendanceManager = new AttendanceManager();
        $ref = $attendanceManager->database->getReference("attendance/{$userId}/{$date}");
        $record = $ref->getValue();

        if (!$record) {
            header("Location: attendance_dashboard.php?error=no_record");
            exit;
        }

        $shifts = $record['shifts'] ?? [];

        if (!isset($shifts[$shiftIndex])) {
            header("Location: attendance_dashboard.php?error=invalid_index");
            exit;
        }

        $inTime = strtotime("$date $checkIn");
        $outTime = strtotime("$date $checkOut");
        if ($outTime <= $inTime) {
            $outTime = strtotime("$date $checkOut +1 day");
        }

        $totalHours = round(($outTime - $inTime) / 3600, 2);
        $extraHours = max(0, $totalHours - 8);

        $shifts[$shiftIndex] = [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_hours' => $totalHours,
            'extra_hours' => $extraHours,
        ];

        $ref->update(['shifts' => $shifts]);
    }
}

header("Location: attendance_dashboard.php");
exit;