<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) && ($_SESSION['role'] !== 'buyer' || $_SESSION['role'] !== 'landowner')) {
    header("Location: ..auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch all land parcels with status 'sell' and approved
$stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE status = 'Sell' AND registration_status = 'Approved' AND owner_id != ?");
$stmt->execute([$user_id]);

$lands = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Lands for Sale</title>
    <link rel="stylesheet" href="../buyer/seelands.cssl">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
<!-- Inside your body -->
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-profile">
            <img src="../icons/profile.png" alt="Profile">
            <h5><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h5>
            <p><i class="fas fa-circle text-success"></i> Online</p>
        </div>
        <ul class="nav flex-column mt-2">
            <li class="nav-item">
                <a href="owner_dashboard.php" class="nav-link"><i class="fa fa-user"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="register_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Register Land</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a>
            </li>
            <li class="nav-item">
                <a href="view_requests.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Requested Lands</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-dollar-sign"></i> Purchase Land</a>
            </li>
            <li class="nav-item">
                <a href="owner_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up"></i> Approve Requests</a>
            </li>
            <li class="nav-item">
                <a href="owner_transfer_history.php" class="nav-link"><i class="fas fa-history"></i> Transfer History</a>
            </li>
            <li class="nav-item">
                <a href="see_lands.php" class="nav-link"><i class="fas fa-map"></i> Buy Land</a>
            </li>
            <li class="nav-item">
                <a href="my_lands.php" class="nav-link"><i class="fas fa-globe"></i> View Your Land</a>
            </li>
            
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Land Owner Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>
                    <!-- User Dropdown -->
<div class="dropdown">
    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle fa-2x text-primary"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
        <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
        <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
    </ul>
</div>

                </div>
            </div>
        </nav>

        <!-- Land List Content -->
        <div class="container mt-4">
            <h2 class="mb-4">Available Lands for Sale</h2>

            <?php if (count($lands) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Land Title No</th>
                                <th>Size (Acres)</th>
                                <th>Region</th>
                                <th>District</th>
                                <th>Ward</th>
                                <th>Street/Village</th>
                                <th>Price (TZS)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lands as $land): ?>
                                <tr>
                                    <td><?= $land['land_title_no']; ?></td>
                                    <td><?= $land['land_size']; ?></td>
                                    <td><?= $land['region_name']; ?></td>
                                    <td><?= $land['district_name']; ?></td>
                                    <td><?= $land['ward_name']; ?></td>
                                    <td><?= $land['village_name']; ?></td>
                                    <td><?= number_format($land['price'], 2); ?></td>
                                    <td>
                                        <a href="make_payment.php?land_id=<?= $land['land_id']; ?>" class="btn btn-primary btn-sm">Make Payment</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No lands available for purchase at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
