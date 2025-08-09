<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Kreait\Firebase\Factory;

// ⏱ الوقت الحالي لتسمية النسخة
$date = date('Y-m-d_H-i-s');
$backupDir = __DIR__ . '/backup';
$projectSubDir = "$backupDir/project_files";
$firebaseSubDir = "$backupDir/firebase_data";
$zipFileName = "EasyAttend_Backup_$date.zip";
$zipPath = "$backupDir/$zipFileName";

// 🛠 1. إنشاء المجلدات الفرعية
@mkdir($projectSubDir, 0777, true);
@mkdir($firebaseSubDir, 0777, true);

// 🗃 2. نسخ كل ملفات المشروع (عدا backup و vendor...)
$projectRoot = realpath(__DIR__ . '/../');
$excluded = ['backup', 'vendor', '.', '..'];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($projectRoot, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    $path = $file->getRealPath();
    $relativePath = str_replace($projectRoot . DIRECTORY_SEPARATOR, '', $path);
    $topFolder = explode(DIRECTORY_SEPARATOR, $relativePath)[0];
    if (in_array($topFolder, $excluded)) continue;

    $destPath = "$projectSubDir/$relativePath";
    if ($file->isDir()) {
        @mkdir($destPath, 0777, true);
    } else {
        @mkdir(dirname($destPath), 0777, true);
        copy($path, $destPath);
    }
}

// 🔥 3. تصدير بيانات Firebase
$serviceAccount = __DIR__ . '/../credentials/firebase_credentials.json';
$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri('https://attendance-project-2c210-default-rtdb.firebaseio.com')->createDatabase();

$paths = ['users', 'attendance', 'leave_requests'];
foreach ($paths as $path) {
    $data = $firebase->getReference($path)->getValue();
    file_put_contents("$firebaseSubDir/$path.json", json_encode($data, JSON_PRETTY_PRINT));
}

// 📦 4. إنشاء ملف ZIP من كل شيء داخل backup/
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($backupDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if ($file->getRealPath() === $zipPath) continue; // لا تضف الملف الناتج نفسه
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($backupDir) + 1);
        $zip->addFile($filePath, $relativePath);
    }

    $zip->close();
    echo "✅ تم إنشاء النسخة الاحتياطية: $zipFileName";
} else {
    echo "❌ فشل في إنشاء النسخة.";
}