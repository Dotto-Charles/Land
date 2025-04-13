<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government_official') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all land parcels with owner-approved status and not yet approved by the government
$stmt = $pdo->prepare("
    SELECT p.*, u.first_name AS buyer_name, l.land_title_no, l.land_id, l.owner_id
    FROM payments p
    JOIN land_parcels l ON p.land_id = l.land_id
    JOIN users u ON p.payer_id = u.user_id
    WHERE p.payment_status = 'paid' 
    AND l.owner_approval_status = 'approved' 
    AND l.gov_approval_status = 'pending'
");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Government Approval</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Government Panel</h2>
        <ul>
            <li><a href="officials_dashboard.php">Dashboard</a></li>
            <li><a href="gov_approval_requests.php">Approval Requests</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Pending Land Ownership Transfers</h2>
        <table border="1" cellpadding="10">
            <tr>
                <th>Buyer Name</th>
                <th>Land Title No</th>
                <th>Amount</th>
                <th>Control Number</th>
                <th>Action</th>
            </tr>
            <?php foreach ($requests as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['buyer_name']) ?></td>
                    <td><?= htmlspecialchars($row['land_title_no']) ?></td>
                    <td><?= number_format($row['amount'], 2) ?> TZS</td>
                    <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                    <td>
                    <form method="POST" action="process_gov_approval.php">
    <input type="hidden" name="land_id" value="<?= $row['land_id'] ?>">
    <input type="hidden" name="new_owner_id" value="<?= $row['payer_id'] ?>">
    <button type="submit" name="approve">Finalize Transfer</button>
</form>

                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
