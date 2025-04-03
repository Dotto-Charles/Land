<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

require '../config/db.php'; // Ensure database connection

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['make_payment'])) {
    // Get the land details and price from the form
    $land_id = $_POST['land_id'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];

    // Remove expired control numbers (older than 3 hours)
    $stmt = $pdo->prepare("DELETE FROM payments WHERE payment_status = 'pending' AND payment_date < NOW() - INTERVAL 3 HOUR");
    $stmt->execute();

    // Check if the user already has a valid control number for this land
    $stmt = $pdo->prepare("SELECT transaction_id FROM payments 
                           WHERE payer_id = ? AND land_id = ? AND payment_status = 'pending' 
                           AND payment_date >= NOW() - INTERVAL 3 HOUR");
    $stmt->execute([$user_id, $land_id]);
    $existing_payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_payment) {
        // User already has a valid control number
        $transaction_id = $existing_payment['transaction_id'];
    } else {
        // Generate a new control number
        $transaction_id = generateUniqueControlNumber($pdo);

        // Insert payment record into the database
        $stmt = $pdo->prepare("INSERT INTO payments (payer_id, land_id, amount, transaction_id, payment_status, payment_date) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $land_id, $price, $transaction_id, 'pending']);
    }

    // Fetch buyer's email
    $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("User not found.");
    }

    $buyer_email = $user['email'];

    // Send Control Number to User's Email
    $subject = "Your Payment Control Number";
    $message = "Dear Buyer,\n\nYour control number for the land purchase is: $transaction_id.\n\nUse this number to complete your payment via mobile money or bank.\n\nThis control number is valid for 3 hours.\n\nThank you!\n\nLand System Team";
    $headers = "From: no-reply@land-system.com\r\n";
    $headers .= "Reply-To: support@land-system.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    mail($buyer_email, $subject, $message, $headers);

    // Redirect to payment options page
    header("Location: payment_options.php?transaction_id=" . urlencode($transaction_id));
    exit();
}

// Function to generate a unique 10-digit control number
function generateUniqueControlNumber($pdo) {
    do {
        $transaction_id = mt_rand(1000000000, 9999999999);

        // Check if the number already exists in the database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE transaction_id = ?");
        $stmt->execute([$transaction_id]);
        $exists = $stmt->fetchColumn();
    } while ($exists > 0); // Keep generating if the number already exists

    return $transaction_id;
}
?>
