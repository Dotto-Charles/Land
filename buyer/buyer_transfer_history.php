<?php
include_once '../config/db.php';
session_start();

// Restrict access to buyers only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Fetch buyer's profile picture
$stmtPic = $pdo->prepare("SELECT picture FROM users WHERE user_id = ?");
$stmtPic->execute([$buyer_id]);
$picRow = $stmtPic->fetch(PDO::FETCH_ASSOC);
$pictureDataUrl = $picRow && $picRow['picture'] ? 'data:image/jpeg;base64,' . base64_encode($picRow['picture']) : '../auth/default.png';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f9fb;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            min-height: 100vh;
            color: white;
        }

        .sidebar a {
            color: #ddd;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }

        .sidebar-profile img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid #0d6efd;
        }

        .content {
            flex: 1;
            padding: 30px;
            background-color: #f7f9fb;
        }

        .navbar {
            border-bottom: 1px solid #ddd;
        }

        .profile-pic-dropdown {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: #007bff;
            color: #fff;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 12px;
            color: #333;
        }

        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table-hover tbody tr:hover {
            background-color: #eef7ff;
        }

        h2.mb-4 {
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <div class="sidebar-profile text-center">
            <img src="<?= $pictureDataUrl ?>" alt="Profile Picture">
            <h5><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h5>
            <p><i class="fas fa-circle text-success"></i> Online</p>
        </div>
        <ul class="nav flex-column mt-4">
            <li class="nav-item">
                <a href="buyer_dashboard.php" class="nav-link"><i class="fa fa-home me-2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-search me-2"></i> Search Land</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-envelope-open-text me-2"></i> Sell Land</a>
            </li>
            <li class="nav-item">
                <a href="buyer_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up me-2"></i> Approve Requests</a>
            </li>
            <li class="nav-item">
                <a href="buyer_transfer_history.php" class="nav-link active"><i class="fas fa-history me-2"></i> Transfer History</a>
            </li>
            <li class="nav-item">
                <a href="see_lands.php" class="nav-link"><i class="fas fa-map me-2"></i> Buy Land</a>
            </li>
            <li class="nav-item">
                <a href="my_lands.php" class="nav-link"><i class="fas fa-globe me-2"></i> View Your Land</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 mb-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Land Transfer History</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 text-primary fw-bold">
                        Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!
                    </span>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $pictureDataUrl ?>" alt="User" class="profile-pic-dropdown">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="../auth/profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="../auth/change_password.php"><i class="fas fa-key me-2"></i> Change Password</a></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Transfer History -->
        <h2 class="mb-4">My Transfer History</h2>

        <?php if (count($transfers) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
