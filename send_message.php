<?php
// Include DB connection file
include './config/db.php'; // This file should create and assign a PDO instance to $pdo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST["name"]));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit;
    }

    try {
        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);

        echo "<script>alert('Message sent successfully.'); window.location.href='contact.php';</script>";
    } catch (PDOException $e) {
        // Log error if needed
        echo "<script>alert('Failed to send message.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='contact_us.php';</script>";
}
?>
