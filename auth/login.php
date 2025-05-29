<?php
session_start();
include_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $national_id = trim($_POST['national_id']);
    $password = trim($_POST['password']);

    if (empty($national_id) || empty($password)) {
        $error = "Please enter both National ID and Password!";
    } else {
        try {
            $sql = "SELECT * FROM users WHERE national_id = :national_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['second_name'] = $row['second_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['sex'] = $row['sex'];
                    $_SESSION['national_id'] = $row['national_id'];
                    $_SESSION['role'] = $row['role'];

                    if ($_SESSION['role'] == 'government_official') {
                        header('Location: ../officials/officials_dashboard.php');
                    } elseif ($_SESSION['role'] == 'landowner') {
                        header("Location: ../land-owner/owner_dashboard.php");
                    } elseif ($_SESSION['role'] == 'buyer') {
                        header("Location: ../buyer/buyer_dashboard.php");
                    } else {
                        header('Location: ../surveyor/surveyor_dashboard.php');
                    }
                    exit();
                } else {
                    echo "<script>alert('Wrong password');</script>";
                }
            } else {
                echo "<script>alert('Wrong password or national ID');</script>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.csls"> <!-- Optional -->
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: #0e4a7b;
            color: white;
            padding: 15px 20px;
            text-align:center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        nav {
            background: #003366;
            display: flex;
            justify-content: right;
            gap: 20px;
            padding: 10px 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background:rgb(244, 244, 244);
            padding: 40px 10px;
        }

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .login-box h2 {
            margin-bottom: 10px;
            color: #0e4a7b;
            text-align:center;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .btn-login {
            background-color: #0e4a7b;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background-color:rgb(14, 241, 71);
        }

        .forgot-password {
            display: block;
            margin-top: 10px;
            text-align: right;
            font-size: 14px;
        }

        footer {
            background: #003366;
            color: white;
            text-align: center;
            padding: 15px 10px;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header>
        <h1>Online Land Registration and Verification System</h1>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="register.php">Register</a>
        <a href="#">Contact Us</a>
    
    </nav>

    <div class="login-container">
        <div class="login-box">
            <h2>Welcome Back!</h2>
            <p>Sign in to continue to Land System.</p>

            <?php if (isset($error)): ?>
                <div class="error-message" style="color:red;"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="national_id">National ID</label>
                <input type="text" name="national_id" id="national_id" required placeholder="Enter your National ID">

                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" required placeholder="Enter password">
                    <span class="toggle-password" onclick="togglePassword()">👁</span>
                </div>

                <div class="remember-me">
                    <input type="checkbox" name="remember"> Remember me
                </div>

                <button type="submit" class="btn-login">Log In</button>

                <a href="forgot_password.php" class="forgot-password">Forgot your password?</a>
            </form>
        </div>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> Online Land Registration and Verification System.
    </footer>

    <script>
        function togglePassword() {
            const pwd = document.getElementById("password");
            pwd.type = pwd.type === "password" ? "text" : "password";
        }
    </script>

</body>
</html>
