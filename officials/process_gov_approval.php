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

    // Step 1: Get old owner ID
    $stmt = $pdo->prepare("SELECT owner_id, price FROM land_parcels WHERE land_id = ?");
    $stmt->execute([$land_id]);
    $land = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$land) {
        die("Land record not found.");
    }
    $price=$land['price'];
    $old_owner_id = $land['owner_id'];

    // Step 2: Get the matching transfer request
    $stmt = $pdo->prepare("SELECT transfer_id FROM land_transfers WHERE land_id = ? AND buyer_id = ? AND transfer_status = 'pending' LIMIT 1");
    $stmt->execute([$land_id, $new_owner_id]);
    $transfer = $stmt->fetch(PDO::FETCH_ASSOC);

    $transfer_id = $transfer ? $transfer['transfer_id'] : null;

    // Step 3: Update land owner & approval status
    $stmt = $pdo->prepare("UPDATE land_parcels SET owner_id = ?, gov_approval_status = 'approved', status='Not_sell' WHERE land_id = ?");
    $stmt->execute([$new_owner_id, $land_id]);

    // Step 4: Update payment record with approval info
    $stmt = $pdo->prepare("
        UPDATE payments 
        SET gov_approval = 'approved', old_owner_id = ?, transfer_id = ?
        WHERE land_id = ? AND payer_id = ? AND payment_status = 'paid'
    ");
    $stmt->execute([$old_owner_id, $transfer_id, $land_id, $new_owner_id]);

    // Step 5: Update transfer request as approved (optional)
    if ($transfer_id) {
        $stmt = $pdo->prepare("UPDATE land_transfers SET transfer_status = 'approved' WHERE transfer_id = ?");
        $stmt->execute([ $transfer_id]);
    }

    header("Location: gov_ownership_approval.php?status=approved");
    exit();
}
?>
