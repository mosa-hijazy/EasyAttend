<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

// أنشئ نسخة من Firebase باستخدام بيانات الحساب
$factory = (new Factory)
->withServiceAccount(__DIR__.'/firebase_credentials.json')
    ->withDatabaseUri('https://attendance-project-2c210-default-rtdb.firebaseio.com/'); // ✔️ رابط Realtime Database

$database = $factory->createDatabase();

// اختبار بسيط
echo "✅ Firebase connected successfully!";
?>