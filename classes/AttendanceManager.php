<?php
require_once __DIR__ . '/../includes/firebase.php';

class AttendanceManager {
    private $database;
    private $dailyWorkingHours = 8;


    public function __construct() {
    $this->database = require __DIR__ . '/../includes/firebase.php';
}

    public function getDayAttendance($userId, $date) {
        $ref = $this->database->getReference("attendance/$userId/$date");
        return $ref->getValue() ?? [];
    }

    public function getAllAttendance() {
        $rawData = $this->database->getReference('attendance')->getValue() ?? [];
        $result = [];

        foreach ($rawData as $userId => $records) {
            foreach ($records as $date => $data) {
                $totalHours = 0;
                $extraHours = 0;
                $shifts = $data['shifts'] ?? [];

                foreach ($shifts as $shift) {
                    if (!empty($shift['check_in']) && !empty($shift['check_out'])) {
                        $checkIn = DateTime::createFromFormat('H:i', $shift['check_in']);
                        $checkOut = DateTime::createFromFormat('H:i', $shift['check_out']);

                        if ($checkOut <= $checkIn) {
                            $checkOut->modify('+1 day');
                        }

                        $interval = $checkOut->diff($checkIn);
                        $totalMinutes = ($interval->h * 60) + $interval->i;
                        $shiftHours = round($totalMinutes / 60, 2);

                        $totalHours += $shiftHours;
                        $extraHours += max(0, $shiftHours - $this->dailyWorkingHours);
                    }
                }

                $result[$userId][$date] = [
                    'status'       => $data['status'] ?? '-',
                    'check_in'     => $data['check_in'] ?? '-',  // (נשאר כגיבוי)
                    'check_out'    => $data['check_out'] ?? '-',
                    'total_hours'  => $totalHours,
                    'extra_hours'  => $extraHours,
                    'total_pay'    => $data['total_pay'] ?? '0',
                    'shifts'       => $shifts
                ];
            }
        }

        return $result;
    }

    public function getUserAttendance($userId) {
        $rawData = $this->database->getReference("attendance/$userId")->getValue() ?? [];
        $result = [];

        foreach ($rawData as $date => $data) {
            $result[$date] = [
                'status'    => $data['status'] ?? '-',
                'time'      => $data['time'] ?? '-',
                'check_in'  => $data['check_in'] ?? '-',
                'check_out' => $data['check_out'] ?? '-',
                'shifts'    => $data['shifts'] ?? []
            ];
        }

        return $result;
    }

    public function updateAttendanceDirect($userId, $date, $status) {
        $ref = $this->database->getReference("attendance/$userId/$date");
        $currentData = $ref->getValue();

        if (!$currentData) return false;

        $updated = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $ref->update($updated);
        return true;
    }

  public function updateShift($userId, $date, $shiftIndex, $checkIn, $checkOut, $status): void {
    $ref = $this->database->getReference("attendance/$userId/$date");
    $shiftPath = "shifts/$shiftIndex";

    // חישוב total_hours כמו קודם
    $totalHours = 0;
    if ($checkIn && $checkOut && $checkIn !== $checkOut) {
        $inTime  = strtotime($checkIn);
        $outTime = strtotime($checkOut);
        if ($outTime <= $inTime) {
            $outTime = strtotime($checkOut . ' +1 day');
        }
        $totalHours = round(($outTime - $inTime) / 3600, 2);
    }

    // עדכון המשמרת
    $ref->update([
        "$shiftPath/check_in"    => $checkIn ?: '',
        "$shiftPath/check_out"   => $checkOut ?: '',
        "$shiftPath/total_hours" => $totalHours,
        "$shiftPath/status"      => $status
    ]);

    // קח את כל המשמרות, וסכם extra_hours נכון:
    $shifts     = $ref->getChild('shifts')->getValue() ?? [];
    $extraHours = 0;
    foreach ($shifts as $shift) {
        if (isset($shift['status']) && $shift['status'] === 'present') {
            $h = floatval($shift['total_hours'] ?? 0);
            // רק אם עבר 8 שעות, נחשב עודף
            $extraHours += max(0, $h - $this->dailyWorkingHours);
        }
    }
    $ref->getChild('extra_hours')->set(round($extraHours, 2));
}


    public function getTodayShifts($userId) {
        $today = date('Y-m-d');
        $ref = $this->database->getReference("attendance/$userId/$today");
        $data = $ref->getValue();
        return $data['shifts'] ?? [];
    }

    public function hasOpenShift($userId) {
        $shifts = $this->getTodayShifts($userId);
        foreach ($shifts as $shift) {
            if (empty($shift['check_out'])) {
                return true;
            }
        }
        return false;
    }
    public function clearShiftsOnly($userId, $date) {
        $ref = $this->database->getReference("attendance/{$userId}/{$date}/shifts");
        $ref->remove();
    }
    public function checkIn($userId) {
        $today = date('Y-m-d');
        $time = date('H:i');
        $ref = $this->database->getReference("attendance/$userId/$today");
        $record = $ref->getValue() ?? [];

        $shifts = $record['shifts'] ?? [];

        foreach ($shifts as $shift) {
            if (!isset($shift['check_out']) || $shift['check_out'] === '') {
                return false;
            }
        }

        

        $shifts[] = [
            'check_in' => $time,
            'check_in_date' => $today,
            'check_out' => '',
            'total_hours' => '',
            'status' => 'present'
        ];

        $ref->update(['shifts' => $shifts]);
        return true;
    }

   public function checkOut($userId) {
    $today = date('Y-m-d');
    $time = date('H:i');
    $ref = $this->database->getReference("attendance/$userId/$today");
    $record = $ref->getValue() ?? [];
    $shifts = $record['shifts'] ?? [];

    foreach (array_reverse($shifts, true) as $index => $shift) {
        if (empty($shift['check_out'])) {
            $checkIn = DateTime::createFromFormat('H:i', $shift['check_in']);
            $checkOut = DateTime::createFromFormat('H:i', $time);

            if ($checkOut <= $checkIn) {
                $checkOut->modify('+1 day');
            }

            $interval = $checkOut->diff($checkIn);
            $totalMinutes = ($interval->h * 60) + $interval->i;
            $totalHours = round($totalMinutes / 60, 2);

            $shift['check_out'] = $time;
            $shift['total_hours'] = $totalHours;
            $shifts[$index] = $shift;

            // 🔁 أعد جمع كل الساعات في اليوم بعد التعديل
            $totalDayHours = 0;
            foreach ($shifts as $s) {
                $totalDayHours += isset($s['total_hours']) ? floatval($s['total_hours']) : 0;
            }

            $extraHours = max(0, $totalDayHours - $this->dailyWorkingHours);

            // 🔄 تحديث البيانات كلها دفعة واحدة
            $ref->update([
                'shifts' => $shifts,
                'extra_hours' => $extraHours
            ]);

            return true;
        }
    }

    return false;
}
    public function deleteShifts($userId, $date)
{
    $path = "attendance/$userId/$date/shifts";
    $this->database->getReference($path)->remove();
}
 
public function sumMonthlyExtraHours($userId, $year, $month): float {
    $totalExtra = 0;
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    $allData = $this->database->getReference("attendance/$userId")->getValue() ?? [];

    foreach ($allData as $date => $record) {
        if (strpos($date, "$year-$month") !== 0) continue;

        $shifts = $record['shifts'] ?? [];
        $presentHours = 0;

        foreach ($shifts as $shift) {
            if (($shift['status'] ?? '') === 'present') {
                $presentHours += floatval($shift['total_hours'] ?? 0);
            }
        }

        if ($presentHours > 0) {
            $extra = max(0, $presentHours - $this->dailyWorkingHours);
            // تحديث extra_hours في الفيربيس
            $this->database->getReference("attendance/$userId/$date/extra_hours")->set(round($extra, 2));
            $totalExtra += $extra;
        }
    }

    return round($totalExtra, 2);
}




public function getAttendanceRecords($username, $year, $month, $startDay = 1): array {
    $records = [];

    // 📦 قراءة كل بيانات المستخدم دفعة واحدة
    $allData = $this->database->getReference("attendance/$username")->getValue() ?? [];

    foreach ($allData as $date => $record) {
        if (strpos($date, "$year-$month") === 0) {
            $day = intval(substr($date, 8, 2));
            if ($day >= $startDay && $day <= 31) {
                $records[$date] = $record;
            }
        }
    }

    return $records;
}
public function countStrictPresentDays(array $attendance): int {
    $count = 0;

    foreach ($attendance as $date => $record) {
        if (!empty($record['shifts'])) {
            foreach ($record['shifts'] as $shift) {
                if (($shift['status'] ?? '') === 'present') {
                    $count++;
                    break; // فقط أول شفت حاضر في اليوم
                }
            }
        }
    }

    return $count;
}


}
