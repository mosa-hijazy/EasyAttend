<?php
require_once __DIR__ . '/../includes/firebase.php';

class LeaveManager {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    public function getAllLeaves() {
        return $this->database
            ->getReference("leave_requests")
            ->getValue() ?? [];
    }

    public function submitLeaveRequest($userId, $date, $type, $reason) {
        $ref = $this->database->getReference("leaves/$userId")->push();
        $ref->set([
            'date' => $date,
            'type' => $type,
            'reason' => $reason,
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i')
        ]);
    }


    public function getUserLeaves($userId) {
        return $this->database
            ->getReference("leaves/$userId")
            ->getValue() ?? [];
    }


 

   
    public function updateLeaveStatus($userId, $requestId, $status) {
        $this->database
            ->getReference("leaves/$userId/$requestId/status")
            ->set($status);
    }
}
