<?php
require_once __DIR__ . '/../includes/firebase.php';

class ChatManager {
    private $database;


    public function __construct() {
        global $database;
        $this->database = $database;
    }
    public function sendMessage($fromUserId, $toUserId, $message) {
        $chatId = $this->getChatId($fromUserId, $toUserId);
        $timestamp = date('Y-m-d H:i:s');

        $this->database->getReference("chats/$chatId")->push([
            'from' => $fromUserId,
            'to' => $toUserId,
            'message' => $message,
            'timestamp' => $timestamp
        ]);
    }

    public function getMessages($fromUserId, $toUserId) {
        $chatId = $this->getChatId($fromUserId, $toUserId);
        $messagesRef = $this->database->getReference("chats/$chatId");
        $messages = $messagesRef->getValue();

        if (!$messages) return [];

        usort($messages, function ($a, $b) {
            return strtotime($a['timestamp']) <=> strtotime($b['timestamp']);
        });

        return $messages;
    }

    private function getChatId($user1, $user2) {
        $ids = [$user1, $user2];
        sort($ids);
        return implode('_', $ids);
    }
}
