<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government_official') {
    header("Location: ../auth/login.php");
    exit();
}

// Get profile picture if available
$pictureDataUrl = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '../assets/default.png';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f7fa;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color:rgb(36, 49, 70);
            color: white;
            position: fixed;
        }

        .sidebar a {
            color: white;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #084298;
        }

        .sidebar-profile img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid white;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }

        .profile-pic-dropdown {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
        }

        .card-header {
            font-size: 1.2rem;
            font-weight: bold;
            background: linear-gradient(to right, #007bff, #0056b3);
            color: white;
        }

        .table thead th {
            vertical-align: middle;
            text-align: center;
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #eef5ff;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="sidebar-profile text-center p-4">
            <img src="<?= $pictureDataUrl ?>" alt="Profile Picture">
            <h5 class="mt-2"><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h5>
            <p><i class="fas fa-circle text-success"></i> Online</p>
        </div>
        <ul class="nav flex-column">
            <li><a href="officials_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="verify_land.php"><i class="fas fa-map-marked-alt me-2"></i> Verify Land</a></li>
            <li><a href="gov_ownership_approval.php"><i class="fas fa-tasks me-2"></i> Manage Requests</a></li>
            <li><a href="gov_transfer_history.php" class="bg-dark"><i class="fas fa-history me-2"></i> Transfer History</a></li>
            <li><a href="search_land.php"><i class="fas fa-search-location me-2"></i> Search Land</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand mb-0">Transfer History</h4>
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

        <!-- Table Card -->
        <div class="card shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Completed Ownership Transfers</span>
                <i class="fas fa-history"></i>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Land Title No</th>
                                <th>Size (Acres)</th>
                                <th>Price (TZS)</th>
                                <th>Previous Owner</th>
                                <th>New Owner (Buyer)</th>
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
                                    <td><?= htmlspecialchars($t['buyer_name']) ?></td>
                                    <td><?= htmlspecialchars($t['transaction_id']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
