<?php
session_start();
include '../config/db.php';
require '../config/mail.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Store reset token
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
            $stmt->execute([
                ':email' => $email,
                ':token' => $token,
                ':expires' => $expires
            ]);

            $reset_link = "http://localhost/Land-project/auth/reset_password.php?token=$token";
            $subject = "Password Reset Request";
            $body = "Hello,\n\nYou requested a password reset. Click the link below to reset your password:\n\n$reset_link\n\nThis link will expire in 1 hour.";

            $mail = getMailer();
            if ($mail) {
                try {
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = nl2br($body);
                    $mail->AltBody = $body;

                    $mail->send();
                    $message = "<span style='color:green;'>Password reset link sent to your email.</span>";
                } catch (Exception $e) {
                    $message = "<span style='color:red;'>Mailer Error: {$mail->ErrorInfo}</span>";
                }
            } else {
                $message = "<span style='color:red;'>Failed to configure mailer.</span>";
            }
        } else {
            $message = "<span style='color:red;'>Email not found.</span>";
        }
    } else {
        $message = "<span style='color:red;'>Enter your email address.</span>";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .forgot-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .forgot-box h2 {
            text-align: center;
            color: #0e4a7b;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-submit {
            background-color: #0e4a7b;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #0bc95b;
        }
        .message {
            text-align: center;
            margin-top: 10px;
        }
        .back-link {
            text-align: center;
            display: block;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="forgot-box">
    <h2>Forgot Password</h2>
    <p>Enter your registered email address.</p>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Your Email" required>
        <button type="submit" class="btn-submit">Send Reset Link</button>
    </form>
    <a href="login.php" class="back-link">Back to Login</a>
</div>

</body>
</html>
