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
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            background: linear-gradient(to right,rgb(34, 34, 53), #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 950px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #0e4a7b;
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

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-column {
            flex: 1;
            min-width: 300px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .error {
            color: red;
            text-align: center;
        }

        .button-container {
            width: 100%;
            margin-top: 10px;
        }

        button {
            background-color: #0e4a7b;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #095191;
        }

        .footer-link {
            text-align: center;
            margin-top: 10px;
        }

        footer {
            background: #003366;
            color: white;
            text-align: center;
            padding: 15px 10px;
        }

        @media screen and (max-width: 768px) {
            form {
                flex-direction: column;
            }

            .form-column {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="main-wrapper">
        <div class="container">
            <h2>User Registration</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>

           <form action="register_process.php" method="POST" enctype="multipart/form-data">
    <div style="width: 100%; margin-bottom: 30px; border: 1px solid #ccc; padding: 25px; border-radius: 8px; background-color: #f9f9f9;">
        <h3 style="color: #0e4a7b; margin-bottom: 20px;">Personal Information</h3>
        <div class="form-column">
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
                    <option value="">-- Select --</option>
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
        </div>

        <div class="form-column">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="landowner">Landowner</option>
                    <option value="buyer">Buyer</option>
                    <option value="surveyor">Surveyor</option>
                    <option value="lawyer">Lawyer</option>
                    <option value="government_official">Government Official</option>
                </select>
            </div>
            <div class="form-group">
                <label>Profile Picture:</label>
                <input type="file" name="picture" accept="image/*" required>
            </div> 
<div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>
            </div>

    
      <!--    <div class="form-group">
                <label>Profile Picture:</label>
                <input type="file" name="profile_photo" accept="image/*" required>
            </div>  -->
        </div>
        <div class="button-container">
        <button type="submit">Register</button>
    </div>
    </div>

    
</form>

        </div>
    </div>

    <footer>
        &copy; <?= date('Y'); ?> Online Land Registration and Verification System.
    </footer>

   
</body>
</html>
