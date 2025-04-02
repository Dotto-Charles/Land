<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['make_payment'])) {
    // Get the land details and price from the form
    $land_id = $_POST['land_id'];
    $price = $_POST['price'];

    // Get the current user's ID
    $user_id = $_SESSION['user_id'];

    // Generate a unique 10-digit control number
    $transaction_id = generateUniqueControlNumber($pdo);

    // Fetch buyer's email from the database
    $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    $buyer_email = $user['email'];

    // Insert payment record into the database (pending payment)
    $stmt = $pdo->prepare("INSERT INTO payments (payer_id, land_id, amount, transaction_id, payment_status) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $land_id, $price, $transaction_id, 'pending']);

    // Send Control Number to the user's email
    $subject = "Your Payment Control Number";
    $message = "Dear Buyer,\n\nYour control number for the land purchase is: $transaction_id.\n\nUse this number to complete your payment via mobile money or bank.\n\nThank you!";
    $headers = "From: tittoc2@gmail.com";

    if (mail($buyer_email, $subject, $message, $headers)) {
        echo "Control number sent to your email.";
    } else {
        echo "Failed to send control number email.";
    }

    // Redirect to the payment options page
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
