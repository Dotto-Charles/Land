<?php
include_once '../config/db.php';
session_start();

if (!isset($_GET['transaction_id'])) {
    die("Invalid request. Control number is missing.");
}

$transaction_id = $_GET['transaction_id'];

// Fetch payment record by control number
$stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_id = ?");
$stmt->execute([$transaction_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("Payment record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="seelands.css">
</head>
<body>

    <div class="content">
        <h3>Payment Confirmation</h3>
        <p>Your payment for the land has been successfully processed.</p>
        <p><strong>Control Number:</strong> <?= $payment['transaction_id']; ?></p>
        <p><strong>Amount Paid:</strong> <?= number_format($payment['amount'], 2); ?> TZS</p>
        <p><strong>Payment Method:</strong> <?= $payment['payment_type']; ?></p>
        <p><strong>Status:</strong> <?= ucfirst($payment['payment_status']); ?></p>
    </div>

</body>
</html>
