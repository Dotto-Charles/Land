
<?php
session_start();
include_once '../config/db.php';  // Ensure this file exists and is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $national_id = trim($_POST['national_id']);
    $password = trim($_POST['password']);

    if (empty($national_id) || empty($password)) {
        $error = "Please enter both National ID and Password!";
    } else {
        try {
            // Use a prepared statement to prevent SQL injection
            $sql = "SELECT * FROM users WHERE national_id = :national_id";
            $stmt = $pdo->prepare($sql);
            
            // Bind parameters
            $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR);

            // Execute the statement
            $stmt->execute();

            // Check if user exists
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify the password
                if (password_verify($password, $row['password'])) {
                    // Password is correct, start session and redirect
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['second_name'] = $row['second_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['sex'] = $row['sex'];
                    $_SESSION['national_id'] = $row['national_id'];
                    $_SESSION['role'] = $row['role'];

                    // Redirect based on user role
                    if ($_SESSION['role'] == 'government_official') {
                        header('Location: ../officials/officials_dashboard.php');
                    } else if ($_SESSION['role'] == 'landowner') {
                        header("Location: ../land-owner/owner_dashboard.php");
                    } else if ($_SESSION['role'] == 'buyer') {
                        header("Location: ../buyer/buyer_dashboard.php");
                    } 
                    else {
                        header('Location: ../surveyor/approve_title_requests.php'); // Redirect to the appropriate location
                    }
                    exit(); // Ensure script stops execution after redirection
                } else {
                    // Password is incorrect
                    echo "<script> alert('Wrong password');</script>";
                }
            } else {
                // User not found
                echo "<script> alert('Wrong password or national ID');</script>";
            }

        } catch (PDOException $e) {
            // Handle error if connection or query fails
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Welcome Back!</h2>
            <p>Sign in to continue to Land System.</p>

            <!-- Display any error messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
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

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
