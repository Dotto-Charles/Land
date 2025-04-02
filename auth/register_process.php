<?php
session_start();
include_once '../config/db.php'; // Ensure this file contains the PDO connection setup

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $second_name = trim($_POST['second_name']);
    $last_name = trim($_POST['last_name']);
    $sex = $_POST['sex'];
    $national_id = trim($_POST['national_id']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if national_id, phone_number, or email already exists
        $check_sql = "SELECT user_id FROM users WHERE national_id = :national_id OR phone_number = :phone_number OR email = :email";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([
            ':national_id' => $national_id,
            ':phone_number' => $phone_number,
            ':email' => $email
        ]);

        if ($check_stmt->rowCount() > 0) {
            $_SESSION['error'] = "National ID, Phone Number, or Email already exists!";
            header("Location: register.php");
            exit();
        }

        // Insert into database
        $sql = "INSERT INTO users (first_name, second_name, last_name, sex, national_id, phone_number, email, password, role)
                VALUES (:first_name, :second_name, :last_name, :sex, :national_id, :phone_number, :email, :password, :role)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name'   => $first_name,
            ':second_name'  => $second_name,
            ':last_name'    => $last_name,
            ':sex'          => $sex,
            ':national_id'  => $national_id,
            ':phone_number' => $phone_number,
            ':email'        => $email,
            ':password'     => $hashed_password,
            ':role'         => $role
        ]);

        $_SESSION['success'] = "Registration successful!";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>
