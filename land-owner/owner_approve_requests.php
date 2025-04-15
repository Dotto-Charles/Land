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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../officials/styleofiicials.css"> <!-- External CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { margin-bottom: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        button { padding: 10px 15px; }
        .msg { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center text-white mt-3">Land System</h3>
        <ul class="nav flex-column mt-4">
            <li class="nav-item">
                <a href="owner_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="register_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Register Land</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-tasks"></i> search Land</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-chart-line"></i> Purchase Land</a>
            </li>
            
            <li class="nav-item">
            <a href="owner_transfer_history.php" class="nav-link"><i class="fas fa-chart-line"></i> Transfer History</a>
            </li>
            <li class="nav-item">
                <a href="../auth/logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Land Owner</h4>
                <div class="ms-auto d-flex align-items-center">
                <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>

                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>

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
