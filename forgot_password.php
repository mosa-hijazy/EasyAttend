<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'classes/email.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

$database = require_once 'includes/firebase.php';
session_start();

$message = '';
$step = 'request';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['reset_code']);
    unset($_SESSION['reset_user']);
    unset($_SESSION['user_key']);
}

// إذا كان المستخدم قد وصل للخطوة الثانية (التحقق من الرمز)
if (isset($_SESSION['reset_code']) && isset($_SESSION['reset_user'])) {
    $step = 'verify';
}

// معالجة إرسال البريد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['email'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $usersSnapshot = $database->getReference("users")->getValue();
    $matchedUserKey = null;

    if ($usersSnapshot) {
        foreach ($usersSnapshot as $key => $user) {
            if (
                isset($user['username'], $user['email']) &&
                $user['username'] === $username &&
                $user['email'] === $email
            ) {
                $matchedUserKey = $key;
                break;
            }
        }
    }

    if ($matchedUserKey) {
        $code = rand(100000, 999999);
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_user'] = $matchedUserKey;

        $subject = "EasyAttend Password Reset Code";
        $body = "
            Hello $username,<br><br>
            Your reset code is: <strong>$code</strong><br><br>
            Please use it to reset your password.<br><br>
            Regards,<br>EasyAttend System
        ";

        sendEmail($email, $username, $subject, $body);

        $message = "✅ Reset code sent to your email.";
        $step = 'verify';
    } else {
        $message = "❌ Username and Email do not match.";
    }
}

// التحقق من الرمز
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $inputCode = $_POST['verification_code'];
    if ($_SESSION['reset_code'] == $inputCode) {
        header("Location: change_password.php");
        exit;
    } else {
        $message = "❌ Invalid verification code.";
        $step = 'verify';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Forgot Password</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($step === 'request'): ?>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Send Reset Code</button>
        </form>

    <?php elseif ($step === 'verify'): ?>
        <form method="POST">
            <div class="mb-3">
                <label>Enter the code sent to your email</label>
                <input type="number" name="verification_code" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Verify Code</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
