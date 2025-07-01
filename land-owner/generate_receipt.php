<?php
include_once '../config/db.php';
session_start();

if (!isset($_GET['transaction_id'])) {
    die("Missing transaction ID.");
}

$transaction_id = $_GET['transaction_id'];

// Fetch payment info
$stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_id = ?");
$stmt->execute([$transaction_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("❌ Payment not found for transaction ID: " . htmlspecialchars($transaction_id));
}

// Fetch buyer info
$buyer = [];
if (!empty($payment['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$payment['user_id']]);
    $buyer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$buyer) {
        $buyer = [];
    }
}

// Fetch land info
$land = [];
if (!empty($payment['land_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE land_id = ?");
    $stmt->execute([$payment['land_id']]);
    $land = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$land) {
        $land = [];
    }
}

// Fetch seller info
$seller = [];
if (!empty($land['owner_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$land['owner_id']]);
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$seller) {
        $seller = [];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Land Payment Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        .receipt-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            background: #fff;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }

        .receipt-section {
            margin-bottom: 20px;
        }

        .receipt-section h4 {
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .receipt-section p {
            margin: 5px 0;
        }

        .btn-download {
            display: block;
            margin: 30px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-download:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="receipt-container" id="receipt">
    <h2>Land Payment Receipt</h2>

    <div class="receipt-section">
        <h4>Buyer Information</h4>
        <p><strong>Name:</strong> <?= isset($buyer['first_name']) ? htmlspecialchars($buyer['first_name'] . ' ' . $buyer['last_name']) : 'N/A'; ?></p>
        <p><strong>Email:</strong> <?= isset($buyer['email']) ? htmlspecialchars($buyer['email']) : 'N/A'; ?></p>
    </div>

    <div class="receipt-section">
        <h4>Seller Information</h4>
        <p><strong>Name:</strong> <?= isset($seller['first_name']) ? htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) : 'N/A'; ?></p>
        <p><strong>Email:</strong> <?= isset($seller['email']) ? htmlspecialchars($seller['email']) : 'N/A'; ?></p>
    </div>

    <div class="receipt-section">
        <h4>Land Details</h4>
        <p><strong>Location:</strong>
            <?= isset($land['region']) ? htmlspecialchars($land['region']) : 'N/A'; ?>,
            <?= isset($land['district']) ? htmlspecialchars($land['district']) : 'N/A'; ?>,
            <?= isset($land['village']) ? htmlspecialchars($land['village']) : 'N/A'; ?>
        </p>
        <p><strong>Size:</strong> <?= isset($land['land_size']) ? htmlspecialchars($land['land_size']) . " hectares" : 'N/A'; ?></p>
        <p><strong>Description:</strong> <?= isset($land['description']) ? htmlspecialchars($land['description']) : 'N/A'; ?></p>
    </div>

    <div class="receipt-section">
        <h4>Payment Details</h4>
        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($payment['transaction_id']); ?></p>
        <p><strong>Amount:</strong> <?= number_format($payment['amount'], 2); ?> TZS</p>
        <p><strong>Method:</strong> <?= isset($payment['payment_type']) ? htmlspecialchars($payment['payment_type']) : 'N/A'; ?></p>
        <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($payment['payment_status'])); ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($payment['payment_date']); ?></p>
    </div>
</div>

<button class="btn-download" onclick="downloadPDF()">Download PDF Receipt</button>

<script>
function downloadPDF() {
    const element = document.getElementById('receipt');
    html2pdf().from(element).save('Land_Receipt.pdf');
}
</script>

</body>
</html>
