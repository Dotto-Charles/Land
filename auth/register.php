<?php
session_start();
include_once '../config/db.php';  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="styleregister.css"> 
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        
        <form action="register_process.php" method="POST">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" required>
            </div>

            <div class="form-group">
                <label>Second Name:</label>
                <input type="text" name="second_name">
            </div>

            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" required>
            </div>

            <div class="form-group">
                <label>Sex:</label>
                <select name="sex" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="form-group">
                <label>National ID:</label>
                <input type="text" name="national_id" required>
            </div>

            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_number" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="landowner">Landowner</option>
                    <option value="buyer">Buyer</option>
                    <option value="surveyor">Surveyor</option>
                    <option value="lawyer">Lawyer</option>
                    <option value="government_official">Government Official</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit">Register</button>
            <p>Do you have an accout?</p> <a href="login.php">Log in</a>
        </form>
    </div>
</body>
</html>
