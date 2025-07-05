<?php
session_start();
include_once '../config/db.php'; // Ensure this file sets up the $pdo (PDO connection)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name     = trim($_POST['first_name']);
    $second_name    = trim($_POST['second_name']);
    $last_name      = trim($_POST['last_name']);
    $sex            = $_POST['sex'];
    $national_id    = trim($_POST['national_id']);
    $phone_number   = trim($_POST['phone_number']);
    $email          = trim($_POST['email']);
    $role           = $_POST['role'];
    $picture        = file_get_contents($_FILES['picture']['tmp_name']);

    $password           = $_POST['password'];
    $confirm_password   = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check individually for duplicates
        $errors = [];

        $check_sql = "SELECT national_id, phone_number, email FROM users 
                      WHERE national_id = :national_id OR phone_number = :phone_number OR email = :email";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([
            ':national_id' => $national_id,
            ':phone_number' => $phone_number,
            ':email' => $email
        ]);

        $existing = $check_stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($existing as $row) {
            if ($row['national_id'] === $national_id) {
                $errors[] = "National ID already exists.";
            }
            if ($row['phone_number'] === $phone_number) {
                $errors[] = "Phone number already exists.";
            }
            if ($row['email'] === $email) {
                $errors[] = "Email already exists.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header("Location: register.php");
            exit();
        }

        // Insert new user
        $sql = "INSERT INTO users (first_name, second_name, last_name, sex, national_id, phone_number, email, picture, password, role)
                VALUES (:first_name, :second_name, :last_name, :sex, :national_id, :phone_number, :email, :picture, :password, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':second_name', $second_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':sex', $sex);
        $stmt->bindParam(':national_id', $national_id);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':picture', $picture, PDO::PARAM_LOB);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

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
