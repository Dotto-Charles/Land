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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleofiicials.css"> <!-- External CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        form { margin-bottom: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center text-white mt-3">Land SYSTEM</h3>
        <ul class="nav flex-column mt-4">
            <li class="nav-item">
                <a href="officials_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="verify_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Verify Land</a>
            </li>
            <li class="nav-item">
                <a href="gov_ownership_approval.php" class="nav-link"><i class="fas fa-tasks"></i> Manage Requests</a>
            </li>
            <li class="nav-item">
                <a href="gov_transfer_history.php" class="nav-link"><i class="fas fa-chart-line"></i> Land Transfer History</a>
            </li>
            <li class="nav-item">
                <a href="../land-owner/search_land.php" class="nav-link"><i class="fas fa-chart-line"></i> Search Land</a>
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
                <h4 class="navbar-brand">Officials Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name']; ?>!</span>
                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <div class="row">

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
            </div>
            </div>
        </div>

</body>
</html>
