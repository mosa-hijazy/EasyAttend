<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

// ===== 1. إعداد المجلدات =====
$timestamp = date('Y-m-d_H-i-s');
$zipName = "EasyAttend_Backup_$timestamp.zip";
$baseTmp = __DIR__ . "/../__full_backup_tmp__";
$firebaseFolder = "$baseTmp/firebase_data";
$projectFolder = "$baseTmp/project_files";
$zipFolder = __DIR__ . "/../backup";
$zipPath = "$zipFolder/$zipName";

@mkdir($firebaseFolder, 0777, true);
@mkdir($projectFolder, 0777, true);
@mkdir($zipFolder, 0777, true);

// ===== 2. تصدير بيانات Firebase =====
$firebase = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase_credentials.json')
    ->withDatabaseUri('https://attendance-project-2c210-default-rtdb.firebaseio.com/')
    ->createDatabase();

$paths = ['users', 'attendance', 'leave_requests'];
foreach ($paths as $path) {
    $data = $firebase->getReference($path)->getValue();
    file_put_contents("$firebaseFolder/$path.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
echo "✅ تم تصدير بيانات Firebase بنجاح<br>";

// ===== 3. نسخ ملفات المشروع =====
function recursiveCopy($src, $dst, $exclude = []) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') continue;
        $srcPath = "$src/$file";
        $dstPath = "$dst/$file";

        foreach ($exclude as $ex) {
            if (strpos($srcPath, $ex) !== false) continue 2;
        }

        if (is_dir($srcPath)) {
            recursiveCopy($srcPath, $dstPath, $exclude);
        } else {
            copy($srcPath, $dstPath);
        }
    }
    closedir($dir);
}

$sourcePath = __DIR__ . '/../';
$excludeFolders = ['vendor', 'node_modules', '__full_backup_tmp__', '.git', 'backup'];
recursiveCopy($sourcePath, $projectFolder, $excludeFolders);
echo "✅ تم نسخ ملفات المشروع<br>";

// ===== 4. إنشاء ملف ZIP =====
function zipFolder($source, $zipFile) {
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $source = str_replace('\\', '/', realpath($source));
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = str_replace('\\', '/', $file->getRealPath());
            $relativePath = substr($filePath, strlen($source) + 1);

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return true;
    }
    return false;
}

if (zipFolder($baseTmp, $zipPath)) {
    echo "✅ تم إنشاء النسخة الاحتياطية: $zipName<br>";
} else {
    echo "❌ فشل في إنشاء النسخة الاحتياطية<br>";
    exit;
}

// ===== 5. حذف المجلد المؤقت =====
function deleteFolder($folder) {
    if (!is_dir($folder)) return;
    foreach (scandir($folder) as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = "$folder/$file";
        is_dir($path) ? deleteFolder($path) : unlink($path);
    }
    rmdir($folder);
}
deleteFolder($baseTmp);

// ===== 6. رفع إلى Google Drive مع حفظ التوكن =====
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/oauth_client.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setRedirectUri('http://localhost/EasyAttend/credentials/oauth_callback.php');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$tokenPath = __DIR__ . '/token.json';
if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            unlink($tokenPath); // إعادة التحقق
            header('Location: ' . $client->createAuthUrl());
            exit;
        }
    }
} else {
    header('Location: ' . $client->createAuthUrl());
    exit;
}

// إذا لا يوجد توكن، اذهب لصفحة التوثيق
if (!$client->getAccessToken()) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit;
}

// بعد التأكد من التوكن، ارفع الملف
$service = new Google_Service_Drive($client);
$fileMetadata = new Google_Service_Drive_DriveFile([
    'name' => $zipName
]);
$content = file_get_contents($zipPath);

$uploadedFile = $service->files->create($fileMetadata, [
    'data' => $content,
    'mimeType' => 'application/zip',
    'uploadType' => 'multipart',
    'fields' => 'id'
]);

echo "✅ تم رفع النسخة الاحتياطية إلى Google Drive بنجاح! ID: " . $uploadedFile->id . "<br>";
