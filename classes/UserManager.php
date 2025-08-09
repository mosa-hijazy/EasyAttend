<?php
require_once __DIR__ . '/../vendor/autoload.php';

class UserManager {
    private $database;

    public function __construct() {
        $this->database = require __DIR__ . '/../includes/firebase.php';
    }

    // ✅ إضافة مستخدم جديد باستخدام username كمفتاح رئيسي
    public function addUser(array $data) {
        if (!isset($data['username'])) {
            throw new Exception("Username is required.");
        }

        $username = $data['username'];
        return $this->database->getReference("users/$username")->set($data);
    }

    // ✅ تعديل بيانات مستخدم باستخدام username كمفتاح
    public function updateUser(string $username, array $data) {
        return $this->database->getReference("users/$username")->update($data);
    }

    // ✅ حذف مستخدم باستخدام username
    public function deleteUser(string $username) {
        return $this->database->getReference("users/$username")->remove();
    }

    // ✅ جلب بيانات مستخدم باستخدام username
    public function getUser(string $username) {
        $user = $this->database->getReference("users/$username")->getValue();
        return $user ?: null;
    }

    // ✅ جلب جميع المستخدمين
    public function getAllUsers() {
        $snapshot = $this->database->getReference("users")->getValue();
        $users = [];

        if (is_array($snapshot)) {
            foreach ($snapshot as $username => $user) {
                $users[$username] = $user;
            }
        }

        return $users;
    }

    // ✅ التأكد من التكرار
    public function isDuplicateUser(string $email, string $name): bool {
        $users = $this->getAllUsers();
        if (!$users) return false;

        foreach ($users as $user) {
            if (
                (isset($user['email']) && $user['email'] === $email) ||
                (isset($user['name']) && $user['name'] === $name)
            ) {
                return true;
            }
        }

        return false;
    }
}
