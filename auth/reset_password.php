<?php
session_start();
include '../config/db.php';

$message = "";
$show_form = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $email = $row['email'];
        $show_form = true;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];

            if ($password === $confirm) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
                $stmt->execute([':password' => $hashed, ':email' => $email]);

                // Delete token
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
                $stmt->execute([':email' => $email]);

                $message = "<span style='color:green;'>Password reset successful. <a href='login.php'>Login now</a>.</span>";
                $show_form = false;
            } else {
                $message = "<span style='color:red;'>Passwords do not match.</span>";
            }
        }
    } else {
        $message = "<span style='color:red;'>Invalid or expired token.</span>";
    }
} else {
    $message = "<span style='color:red;'>No token provided.</span>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .reset-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .reset-box h2 {
            text-align: center;
            color: #0e4a7b;
        }
        input[type="password"] {
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
    </style>
</head>
<body>

<div class="reset-box">
    <h2>Reset Password</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($show_form): ?>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm" placeholder="Confirm Password" required>
            <button type="submit" class="btn-submit">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
