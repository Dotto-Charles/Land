<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration for Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tittocharles@gmail.com'; // Replace with your Gmail address
        $mail->Password = 'lagbaxrfcdgkndvm';   // Replace with your generated App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('tittocharles@gmail.com', 'Land Registration Office');
        $mail->addAddress($to); // Recipient
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        error_log("Email sent successfully to $to");
    } catch (Exception $e) {
        echo "Email sending failed: " . $mail->ErrorInfo;

    }
}
?>