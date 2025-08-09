<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/PHPMailer.php';
require __DIR__ . '/../PHPMailer/SMTP.php';
require __DIR__ . '/../PHPMailer/Exception.php';

function sendLateEmail($to, $name, $date) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'salahdiab500@gmail.com';
        $mail->Password = 'cprs waej qiei fnaj';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('salahdiab500@gmail.com', 'EasyAttend System');
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Late Attendance Notice';
        $mail->Body = "Dear $name,<br><br>You checked in after 8:30 AM on <strong>$date</strong>.<br>This will be recorded as a late attendance.<br><br>Best regards,<br>EasyAttend System";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error (late): {$mail->ErrorInfo}");
        return false;
    }
}

function sendEmail($to, $name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'salahdiab500@gmail.com';
        $mail->Password = 'cprs waej qiei fnaj';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('salahdiab500@gmail.com', 'EasyAttend System');
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error (generic): {$mail->ErrorInfo}");
        return false;
    }
}

// ✅ هذه الدالة لازم تكون برا، مش داخل sendEmail
function sendPasswordChangedEmail($toEmail, $name) {
    $subject = "Your EasyAttend Password Was Changed";
    $body = "
        Hello $name,<br><br>
        This is a confirmation that your EasyAttend account password was successfully changed.<br><br>
        If you did not perform this action, please contact support immediately.<br><br>
        Regards,<br>EasyAttend Team
    ";

    sendEmail($toEmail, $name, $subject, $body);
}
