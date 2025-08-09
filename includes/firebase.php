<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

try {
    $factory = (new Factory)
        ->withServiceAccount(__DIR__ . '/../firebase_credentials.json')
        ->withDatabaseUri('https://attendance-project-2c210-default-rtdb.firebaseio.com/');

    $database = $factory->createDatabase();

    return $database; // ✅ هذا السطر مهم جدًا
} catch (Exception $e) {
    die("❌ Firebase init error: " . $e->getMessage());
}
