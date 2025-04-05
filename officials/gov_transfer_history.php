<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all approved land transfers
$stmt = $pdo->prepare("
    SELECT p.*, u.fullname AS buyer_name, l.land_title_no, l.land_size, l.price, o.fullname AS previous_owner
    FROM payments p
    JOIN land_parcels l ON p.land_id = l.land_id
    JOIN users u ON p.payer_id = u.user_id
    JOIN users o ON p.old_owner_id = o.user_id
    WHERE p.payment_status = 'paid' 
    AND l.owner_approval_status = 'approved' 
    AND l.gov_approval_status = 'approved'
    ORDER BY p.payment_id DESC
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
                <td><?= htmlspecialchars($t['previous_owner']) ?></td>
                <td><?= htmlspecialchars($t['buyer_name']) ?></td>
                <td><?= htmlspecialchars($t['transaction_id']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
