<?php
include_once '../config/db.php';
include_once '../config/mail.php'; // Make sure this file has sendMail($to, $subject, $body)

session_start();

if (!isset($_POST['transaction_id'])) {
    die("Invalid request. Control number is missing.");
}

$transaction_id = $_POST['transaction_id'];

// Fetch payment details
$stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_id = ?");
$stmt->execute([$transaction_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("Payment record not found.");
}

// Update payment status to "paid"
$stmt = $pdo->prepare("UPDATE payments SET payment_status = ?, payment_type = ? WHERE transaction_id = ?");
$stmt->execute(['paid', 'MOBILE', $transaction_id]);

// Fetch land owner info from the land table
$land_id = $payment['land_id'];
$stmt = $pdo->prepare("SELECT u.email, u.first_name FROM land_parcels lp JOIN users u ON lp.owner_id = u.user_id WHERE land_id = ?");
$stmt->execute([$land_id]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);

if ($seller) {
    $sellerEmail = $seller['email'];
    $sellerName = $seller['first_name'];

    $subject = "Payment Received - Please Verify";
    $body = "
        <p>Dear <strong>$sellerName</strong>,</p>
        <p>A buyer has completed a mobile payment for your land (Land ID: <strong>$land_id</strong>) with Control Number <strong>$transaction_id</strong>.</p>
        <p>Please log into your dashboard to review and verify the payment before approving ownership transfer.</p>
        <p><a href='http://yourdomain.com/login.php'>Click here to log in</a></p>
        <br>
        <p>Thank you,<br>Online Land Registration System</p>
    ";

    // Send the email using mail.php helper
    sendEmail($sellerEmail, $subject, $body);
}

header("Location: payment_confimation.php?transaction_id=" . $transaction_id);
exit();
?>
