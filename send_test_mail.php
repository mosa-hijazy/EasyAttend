<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';
require __DIR__ . '/PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tajreebv@gmail.com'; 
    $mail->Password = 'rlmztgbtwprxvifd';    // סיסמת אפליקציה מדויקת - בלי רווחים!
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('tajreebv@gmail.com', 'EasyAttend System');
    $mail->addAddress('tajreebv@gmail.com', 'Test User'); // אפשר לשנות למייל אחר לבדיקה

    $mail->isHTML(true);
    $mail->Subject = 'מייל בדיקה ממערכת EasyAttend';
    $mail->Body    = '<h2>זה עובד! ✔️</h2><p>שלחת בהצלחה מייל מ־Gmail עם PHPMailer.</p>';

    $mail->send();
    echo '✔ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Mail error: {$mail->ErrorInfo}";
}
?>
