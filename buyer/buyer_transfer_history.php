<?php
include_once '../config/db.php';
session_start();

// Restrict access to buyers only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Fetch transfer history for this buyer
$stmt = $pdo->prepare("
    SELECT p.*, CONCAT(u.first_name, ' ', u.last_name) AS seller_name, l.land_title_no, l.land_size, l.price
    FROM payments p
    JOIN land_parcels l ON p.land_id = l.land_id
    JOIN land_transfers t ON p.transfer_id = t.transfer_id
    JOIN users u ON t.seller_id = u.user_id
    WHERE p.payer_id = ? 
    AND p.payment_status = 'paid' 
    AND t.transfer_status = 'Approved'
    ORDER BY p.payment_id DESC
");

$stmt->execute([$buyer_id]);
$transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Land Transfer History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: #2c3e50;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h3 {
            font-weight: bold;
        }

        .nav-link {
            color: #fff;
            font-size: 18px;
            padding: 15px;
            display: block;
            transition: 0.3s;
        }

        .nav-link i {
            margin-right: 10px;
        }

        .nav-link:hover, .logout {
            background: #1a252f;
            color: #e74c3c;
        }

        .main-content {
            margin-left: 260px; /* Leave space for sidebar */
            padding: 30px;
        }

        .table th {
            background-color: #e9ecef;
        }

        .navbar {
            margin-left: 260px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center text-white mt-3">Land System</h3>
    <ul class="nav flex-column mt-4">
        <li class="nav-item">
            <a href="buyer_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a href="see_lands.php" class="nav-link"><i class="fas fa-check-circle"></i> See Available Land</a>
        </li>
        <li class="nav-item">
            <a href="buyer_transfer_history.php" class="nav-link active"><i class="fas fa-tasks"></i> Transfer History</a>
        </li>
        <li class="nav-item">
            <a href="my_lands.php" class="nav-link"><i class="fas fa-chart-line"></i> Your Lands</a>
        </li>
        <li class="nav-item">
            <a href="purchase_land.php" class="nav-link"><i class="fas fa-money-check-alt"></i> Purchase Land</a>
        </li>
        <li class="nav-item">
            <a href="../auth/logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <div class="container-fluid">
        <h4 class="navbar-brand">Buyer Panel</h4>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>
            <i class="fas fa-user-circle fa-2x text-primary"></i>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <h2 class="mb-4">My Transfer History</h2>

    <?php if (count($transfers) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Land Title No</th>
                        <th>Size (Acres)</th>
                        <th>Price (TZS)</th>
                        <th>Previous Owner</th>
                        <th>Control No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transfers as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['land_title_no']) ?></td>
                            <td><?= htmlspecialchars($t['land_size']) ?></td>
                            <td><?= number_format($t['amount']) ?></td>
                            <td><?= htmlspecialchars($t['seller_name']) ?></td>
                            <td><?= htmlspecialchars($t['transaction_id']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">You have no land transfers approved yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
