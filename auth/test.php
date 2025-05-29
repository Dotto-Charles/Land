<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Make sure this points to your Composer vendor folder

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 2;                      // Set to 2 to enable verbose debug output
    $mail->isSMTP();                          
    $mail->Host       = 'smtp.gmail.com';     
    $mail->SMTPAuth   = true;                 
    $mail->Username   = 'tittocharles@gmail.com';  // Your Gmail address
    $mail->Password   = 'lagbaxrfcdgkndvm';        // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('tittocharles@gmail.com', 'Land Registration Office');
    $mail->addAddress('listonvenas99@gmail.com', 'Test Recipient'); // Replace with your test email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = 'This is a <b>test email</b> sent using PHPMailer with Gmail SMTP.';

    $mail->send();
    echo '✅ Test email sent successfully!';
} catch (Exception $e) {
    echo "❌ Test email failed. Error: {$mail->ErrorInfo}";
}
?>
