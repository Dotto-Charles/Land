<?php
include_once '../config/db.php';
session_start();

// Check if the transaction_id is set
if (!isset($_POST['transaction_id']) || empty($_POST['transaction_id'])) {
    die("Invalid request. Control number is missing.");
}

$transaction_id = $_POST['transaction_id'];

// Fetch payment record by control number
$stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_id = ?");
$stmt->execute([$transaction_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("Payment record not found.");
}

// Update the payment status to 'paid' and add the payment method (bank)
$stmt = $pdo->prepare("UPDATE payments SET payment_status = ?, payment_type = ? WHERE transaction_id = ?");
$stmt->execute(['paid', 'Bank', $transaction_id]);

// Redirect to a confirmation page
header("Location: payment_confirmation.php?control_number=" . urlencode($transaction_id));
exit();
