<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government_official') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['land_id']) && isset($_POST['new_owner_id'])) {
    $land_id = $_POST['land_id'];
    $new_owner_id = $_POST['new_owner_id'];

    // Step 1: Update land record to reflect new owner and mark gov approval as done
    $stmt = $pdo->prepare("UPDATE land_parcels SET owner_id = ?, gov_approval_status = 'approved' WHERE land_id = ?");
    $stmt->execute([$new_owner_id, $land_id]);

    // Step 2: Optionally, record history (you can add a transfer_log table)

    header("Location: gov_ownership_approval.php?status=approved");
    exit();
}
?>
