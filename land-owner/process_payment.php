<?php
include_once '../config/db.php';
require_once '../config/mail.php'; // Include mail script
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['make_payment'])) {
    $land_id = $_POST['land_id'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];
    $amount = $price;

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
        $transaction_id = $existing_payment['transaction_id'];
    } else {
        // Generate new control number
        $transaction_id = generateUniqueControlNumber($pdo);

        // ✅ Get the current (old) owner of the land
        $stmt = $pdo->prepare("SELECT owner_id FROM land_parcels WHERE land_id = ?");
        $stmt->execute([$land_id]);
        $land = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$land) {
            die("Land not found.");
        }

        $seller_id = $land['owner_id'];

        // ✅ Check if a transfer already exists in `land_transfers`
        $stmt = $pdo->prepare("SELECT transfer_id FROM land_transfers 
                               WHERE land_id = ? AND buyer_id = ? AND transfer_status = 'Pending'");
        $stmt->execute([$land_id, $user_id]);
        $transferRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transferRow) {
            // ✅ Insert transfer request into `land_transfers`
            $stmt = $pdo->prepare("INSERT INTO land_transfers (land_id, seller_id, buyer_id, sale_price, transfer_status, transfer_date) 
                                   VALUES (?, ?, ?, ?, 'Pending', NOW())");
            $stmt->execute([$land_id, $seller_id, $user_id, $price]);
            $transfer_id = $pdo->lastInsertId();
        } else {
            $transfer_id = $transferRow['transfer_id'];
        }

        // ✅ Insert payment record
        try {
            $stmt = $pdo->prepare("
                INSERT INTO payments (payer_id, land_id, amount, payment_type, transaction_id, payment_status, transfer_id, payment_date) 
                VALUES (?, ?, ?, 'Mobile', ?, 'pending', ?, NOW())
            ");
            $stmt->execute([$user_id, $land_id, $amount, $transaction_id, $transfer_id]);

            // ✅ Fetch buyer info for email
            $stmt = $pdo->prepare("SELECT first_name, email FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $buyer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($buyer) {
                $subject = "Land Payment Control Number Generated";
                $message = "Dear {$buyer['first_name']},<br><br>
                            Your payment request for the land (ID: $land_id) has been received.<br>
                            Your control number is: <strong>$transaction_id</strong>.<br>
                            Please use this number to complete your mobile payment within 3 hours.<br><br>
                            Regards,<br>Land Registry System.";
                
                // ✅ Send email
                sendEmail($buyer['email'], $subject, $message);
            }

        } catch (PDOException $e) {
            echo "Insert failed: " . $e->getMessage();
            exit();
        }
    }

    // ✅ Redirect to payment options page
    header("Location: payment_options.php?transaction_id=" . urlencode($transaction_id));
    exit();
}





// ✅ Function to generate a unique 10-digit control number
function generateUniqueControlNumber($pdo) {
    do {
        $transaction_id = mt_rand(1000000000, 9999999999);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE transaction_id = ?");
        $stmt->execute([$transaction_id]);
        $exists = $stmt->fetchColumn();
    } while ($exists > 0);
    return $transaction_id;
}
?>
