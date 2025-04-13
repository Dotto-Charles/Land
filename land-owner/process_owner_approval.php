<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['land_id'])) {
    $land_id = $_POST['land_id'];
    $status = isset($_POST['approve']) ? 'approved' : 'rejected';

    // Update land approval status
    $stmt = $pdo->prepare("UPDATE land_parcels SET owner_approval_status = ? WHERE land_id = ?");
    $stmt->execute([$status, $land_id]);

    // Update owner_approval status in payments table
    $stmt = $pdo->prepare("UPDATE payments SET owner_approval = ? WHERE land_id = ? AND payment_status = 'paid'");
    $stmt->execute([$status, $land_id]);

    header("Location: owner_approve_requests.php?status=" . $status);
    exit();
}
?>
