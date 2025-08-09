<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

// إنشاء الكائن مع ربط قاعدة البيانات
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_credentials.json')
    ->withDatabaseUri('https://attendance-project-2c210-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();

// المسارات اللي نريد نعمل لها نسخ احتياطي
$paths = ['users', 'attendance', 'leave_requests', 'chats'];
$backupDir = __DIR__ . '/backup';
if (!file_exists($backupDir)) mkdir($backupDir);

foreach ($paths as $path) {
    $data = $database->getReference($path)->getValue();
    file_put_contents($backupDir . '/' . $path . '.json', json_encode($data, JSON_PRETTY_PRINT));
}

echo "✅ تم تصدير البيانات من Firebase.";
