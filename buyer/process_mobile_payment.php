<?php
include_once '../config/db.php';
session_start();

if (!isset($_POST['transaction_id'])) {
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

// Update the payment status to 'paid' and set payment_type
$stmt = $pdo->prepare("UPDATE payments SET payment_status = ?, payment_type = ? WHERE transaction_id = ?");
$stmt->execute(['paid', 'MOBILE', $transaction_id]); // Change 'Registration' if needed

header("Location: payment_confirmation.php?transaction_id=" . $transaction_id);
exit();
?>
