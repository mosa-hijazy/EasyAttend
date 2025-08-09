<?php
require_once __DIR__ . '/../vendor/autoload.php'; // المسار الصحيح لـ autoload

session_start();

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/oauth_client.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setRedirectUri('http://localhost/EasyAttend/credentials/oauth_callback.php');
$client->setAccessType('offline');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_Drive($client);

    // ✅ تحديد آخر ملف ZIP تم إنشاؤه داخل مجلد backup
    $backupFiles = glob(__DIR__ . '/backup/EasyAttend_Backup_*.zip');
    rsort($backupFiles); // ترتيب تنازلي حسب الاسم
    $filePath = $backupFiles[0] ?? null;

    if ($filePath && file_exists($filePath)) {
        $fileName = basename($filePath);

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [] // يمكنك إضافة ID مجلد إذا أردت رفعه داخل مجلد محدد
        ]);

        $content = file_get_contents($filePath);

        $uploadedFile = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/zip',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        echo "✅ تم رفع النسخة الاحتياطية بنجاح إلى Google Drive!<br>";
        echo "📁 اسم الملف: <b>$fileName</b><br>";
        echo "🆔 معرف الملف على درايف: <code>{$uploadedFile->id}</code>";
    } else {
        echo "❌ لا يوجد ملفات نسخ احتياطي!";
    }

} else {
    // إذا ما وافق المستخدم بعد على ربط جوجل
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}
