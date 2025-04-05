<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Get all lands owned by this owner with pending approval and already paid
$stmt = $pdo->prepare("
    SELECT p.*, l.land_title_no, u.first_name AS buyer_name
    FROM payments p
    JOIN land_parcels l ON p.land_id = l.land_id
    JOIN users u ON p.payer_id = u.user_id
    WHERE l.owner_id = ? AND p.payment_status = 'paid' AND l.owner_approval_status = 'pending'
");
$stmt->execute([$owner_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Land Sales</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Land System</h2>
        <ul>
            <li><a href="owner_dashboard.php">Dashboard</a></li>
            <li><a href="owner_approval_requests.php">Approval Requests</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Land Sale Approval Requests</h2>
        <table border="1" cellpadding="10">
            <tr>
                <th>Buyer</th>
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
                        <form method="POST" action="process_owner_approval.php" style="display:inline;">
                            <input type="hidden" name="land_id" value="<?= $row['land_id'] ?>">
                            <button type="submit" name="approve" class="btn-approve">Approve</button>
                        </form>
                        <form method="POST" action="process_owner_approval.php" style="display:inline;">
                            <input type="hidden" name="land_id" value="<?= $row['land_id'] ?>">
                            <button type="submit" name="reject"  class="btn-reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
