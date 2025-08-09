<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/oauth_client.json');
$client->setRedirectUri('http://localhost/EasyAttend/credentials/oauth_callback.php');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

$tokenPath = __DIR__ . '/token.json';

// إذا Google أرسل كود المصادقة
if (isset($_GET['code'])) {
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // إذا فيه خطأ
    if (isset($accessToken['error'])) {
        exit("❌ خطأ في الحصول على التوكن: " . htmlspecialchars($accessToken['error']));
    }

    // حفظ التوكن في ملف
    file_put_contents($tokenPath, json_encode($accessToken));

    // حفظه في السيشن إذا تحب
    $_SESSION['access_token'] = $accessToken;

    echo "✅ تم حفظ التوكن بنجاح. <br>";
    echo "<a href='../backup_and_upload.php'>اضغط هنا للرجوع</a>";
    exit;
}

// إذا ما فيه كود
exit("❌ لم يتم العثور على كود المصادقة!");
