<?php
require_once __DIR__ . '/../includes/firebase.php';
class Attend {
    private $attendance;
    private $dates;

    public function __construct(array $attendance, array $dates) {
        $this->attendance = $attendance;
        $this->dates = $dates;
    }

    public function calculateTotalHours(): float {
    $total = 0;
    foreach ($this->dates as $date) {
        if (!empty($this->attendance[$date]['shifts'])) {
            foreach ($this->attendance[$date]['shifts'] as $shift) {
                $status = strtolower($shift['status'] ?? '');
                if ($status === 'present') {
                    $total += floatval($shift['total_hours'] ?? 0);
                }
            }
        }
    }
    return round($total, 2);
}

   public function calculateExtraHours(): float {
    $extra = 0;
    foreach ($this->dates as $date) {
        if (!empty($this->attendance[$date]['shifts'])) {
            foreach ($this->attendance[$date]['shifts'] as $shift) {
                $status = strtolower($shift['status'] ?? '');
                if ($status === 'present') {
                    $extra += floatval($shift['extra_hours'] ?? 0);
                }
            }
        }
    }
    return round($extra, 2);
}


    public function countPresentDays(): int {
        $count = 0;
        foreach ($this->dates as $date) {
            if (!empty($this->attendance[$date]['shifts'])) {
                foreach ($this->attendance[$date]['shifts'] as $shift) {
                    if (!empty($shift['check_in'])) {
                        $count++;
                        break;
                    }
                }
            }
        }
        return $count;
    }

    public function countAbsentDays(): int {
        return count($this->dates) - $this->countPresentDays();
    }
}
