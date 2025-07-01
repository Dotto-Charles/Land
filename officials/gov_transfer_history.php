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
        CONCAT(b.first_name, ' ', b.last_name) AS buyer_name,
        CONCAT(s.first_name, ' ', s.last_name) AS seller_name,
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleofiicials.css"> <!-- External CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="../land-owner/style.css">

    <style>
        form { margin-bottom: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
         .profile-pic-dropdown {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #007bff;
    }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-profile text-center p-3">
    <img src="<?= $pictureDataUrl ?>" alt="Profile Picture">
    <h5><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h5>
    <p><i class="fas fa-circle text-success"></i> Online</p>
        </div>
        <ul class="nav flex-column mt-2">
            <li class="nav-item">
                <a href="officials_dashboard.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="verify_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Verify Land</a>
            </li>
            <li class="nav-item">
                <a href="gov_ownership_approval.php" class="nav-link"><i class="fas fa-search"></i> Manage Requests</a>
            </li>
            <li class="nav-item">
                <a href="gov_transfer_history.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Transfer History</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-dollar-sign"></i> Search Land</a>
            </li>
           
            
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <div class="container-fluid">
        <h4 class="navbar-brand">Register Land</h4>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 text-primary fw-bold">
                Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!
            </span>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $pictureDataUrl ?>" alt="User" class="profile-pic-dropdown">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="../auth/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="../auth/change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>


<!-- Dashboard Content -->
        <div class="container mt-4">
            <div class="row">

        <div class="main-content">
    <h2>Completed Ownership Transfers</h2>
    <table border="1" cellpadding="6">
        <tr>
            <th>Land Title No</th>
            <th>Size (Acres)</th>
            <th>Price (TZS)</th>
            <th>Previous Owner</th>
            <th>New Owner (Buyer)</th>
            <th>Control No</th>
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
        </div>
        </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
