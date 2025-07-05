<?php
session_start();
include_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #222235, #ffffff);
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 960px;
            margin-top: 40px;
            margin-bottom: 40px;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .form-section-title {
            color: #0e4a7b;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 15px;
        }
        .password-strength {
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2 class="text-center text-primary">User Registration</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="register_process.php" method="POST" enctype="multipart/form-data" id="registrationForm">
        <div class="row">
            <h5 class="form-section-title">Personal Information</h5>

            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name:</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="second_name" class="form-label">Second Name:</label>
                <input type="text" name="second_name" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="sex" class="form-label">Sex:</label>
                <select name="sex" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="national_id" class="form-label">National ID:</label>
                <input type="text" name="national_id" id="national_id" class="form-control" maxlength="10" required>
                <div class="invalid-feedback">National ID must be exactly 10 digits.</div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="phone_number" class="form-label">Phone Number:</label>
                <input type="text" name="phone_number" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="role" class="form-label">Role:</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Select Role --</option>
                    <option value="landowner">Landowner</option>
                    <option value="buyer">Buyer</option>
                    <option value="surveyor">Surveyor</option>
                    <option value="lawyer">Lawyer</option>
                    <option value="government_official">Government Official</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="picture" class="form-label">Profile Picture:</label>
                <input type="file" name="picture" class="form-control" accept="image/*" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
                <div id="passwordStrength" class="password-strength mt-1"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="confirm_password" class="form-label">Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </div>
        </div>
    </form>
</div>

<footer>
    &copy; <?= date('Y'); ?> Online Land Registration and Verification System.
</footer>

<!-- Bootstrap JS & password validation -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const nationalIdInput = document.getElementById('national_id');
    nationalIdInput.addEventListener('input', () => {
        const isValid = /^\d{10}$/.test(nationalIdInput.value);
        if (!isValid) {
            nationalIdInput.classList.add('is-invalid');
        } else {
            nationalIdInput.classList.remove('is-invalid');
        }
    });

    const passwordInput = document.getElementById('password');
    const strengthDisplay = document.getElementById('passwordStrength');

    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        let strength = 'Weak';
        let color = 'red';

        if (val.length >= 8) {
            if (/[A-Z]/.test(val) && /[0-9]/.test(val) && /[\W]/.test(val)) {
                strength = 'Strong';
                color = 'green';
            } else if ((/[A-Z]/.test(val) && /[0-9]/.test(val)) || (/[A-Z]/.test(val) && /[\W]/.test(val))) {
                strength = 'Medium';
                color = 'orange';
            }
        }

        strengthDisplay.textContent = strength;
        strengthDisplay.style.color = color;
    });
</script>

</body>
</html>
