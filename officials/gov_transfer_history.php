<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government_official') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all approved land transfers
$stmt = $pdo->prepare("
    SELECT 
        lt.transfer_id,
        lt.transfer_date,
        lt.sale_price,
        l.land_title_no,
        l.land_size,
        b.first_name AS buyer_name,
        s.first_name AS seller_name,
        p.transaction_id,
        p.amount
    FROM land_transfers lt
    JOIN land_parcels l ON lt.land_id = l.land_id
    JOIN users b ON lt.buyer_id = b.user_id
    JOIN users s ON lt.seller_id = s.user_id
    LEFT JOIN payments p ON p.transfer_id = lt.transfer_id
    WHERE lt.transfer_status = 'Approved'
    ORDER BY lt.transfer_date DESC
");
$stmt->execute();
$transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transfer History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="sidebar">
    <h2>Government Panel</h2>
    <ul>
        <li><a href="gov_dashboard.php">Dashboard</a></li>
        <li><a href="gov_transfer_history.php">Transfer History</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h2>Completed Ownership Transfers</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>Land Title No</th>
            <th>Size (Acres)</th>
            <th>Price (TZS)</th>
            <th>Previous Owner</th>
            <th>New Owner (Buyer)</th>
            <th>Transaction ID</th>
        </tr>
        <?php foreach ($transfers as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['land_title_no']) ?></td>
                <td><?= $t['land_size'] ?></td>
                <td><?= number_format($t['amount']) ?></td>
                <td><?= htmlspecialchars($t['seller_name']) ?></td> <!-- Fixed: was 'previous_owner' -->
                <td><?= htmlspecialchars($t['buyer_name']) ?></td>
                <td><?= htmlspecialchars($t['transaction_id']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
