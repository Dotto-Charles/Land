<?php
include_once '../config/db.php';
session_start();

// Check if the control number is passed in the query string
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
    <title>Payment Options</title>
    <link rel="stylesheet" href="seelands.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Land SYSTEM</h2>
        <ul>
            <li><a href="buyer_dashboard.php">Dashboard</a></li>
            <li><a href="view_available_lands.php">View Available Lands</a></li>
            <li><a href="view_purchase_requests.php">View Purchase Requests</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Payment Options</h2>
        </header>

        <div class="content">
            <h3>Land Title No: <?= $payment['land_id']; ?></h3>
            <p><strong>Price:</strong> <?= number_format($payment['amount'], 2); ?> TZS</p>
            <p><strong>Control Number:</strong> <?= $payment['transaction_id']; ?></p>

            <h4>Choose Payment Method:</h4>

            <!-- Payment Method Options -->
            <div class="payment-options">
                <form method="POST" action="process_mobile_payment.php">
                    <input type="hidden" name="transaction_id" value="<?= $payment['transaction_id']; ?>">
                    <button type="submit" class="btn">Pay via Mobile (MPesa)</button>
                </form>

                <form method="POST" action="process_bank_payment.php">
                    <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($payment['transaction_id']); ?>">
                    <button type="submit" class="btn">Pay via Bank</button>
                </form>

            </div>
        </div>
    </div>

</body>
</html>
