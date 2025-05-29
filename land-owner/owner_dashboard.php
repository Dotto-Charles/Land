<?php
session_start();
include '../config/db.php'; // This must define $pdo (PDO connection)

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$pictureDataUrl = '../icons/default_profile.jpg'; // Default image

try {
    $stmt = $pdo->prepare("SELECT picture FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['picture'])) {
        $base64Image = base64_encode($row['picture']);
        $pictureDataUrl = 'data:image/jpeg;base64,' . $base64Image;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../officials/styleofiicials.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-pic-dropdown {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
        }

        .sidebar-profile img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .icon-img {
            width: 60px;
            height: 60px;
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
            <li class="nav-item"><a href="owner_dashboard.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a></li>
            <li class="nav-item"><a href="register_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Register Land</a></li>
            <li class="nav-item"><a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a></li>
            <li class="nav-item"><a href="view_requests.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Requested Lands</a></li>
            <li class="nav-item"><a href="purchase_land.php" class="nav-link"><i class="fas fa-dollar-sign"></i> Sell Land</a></li>
            <li class="nav-item"><a href="owner_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up"></i> Approve Requests</a></li>
            <li class="nav-item"><a href="owner_transfer_history.php" class="nav-link"><i class="fas fa-history"></i> Transfer History</a></li>
            <li class="nav-item"><a href="see_lands.php" class="nav-link"><i class="fas fa-map"></i> Buy Land</a></li>
            <li class="nav-item"><a href="my_lands.php" class="nav-link"><i class="fas fa-globe"></i> View Your Land</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content flex-grow-1">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Land Owner Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $pictureDataUrl ?>" class="profile-pic-dropdown" alt="User">
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

        <!-- Dashboard Content -->
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="../icons/register.png" class="icon-img mb-2" alt="Register">
                            <h5><a href="register_land.php">Register Land</a></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="../icons/search.png" class="icon-img mb-2" alt="Search">
                            <h5><a href="search_land.php">Search Land</a></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="../icons/sell.png" class="icon-img mb-2" alt="Sell">
                            <h5><a href="purchase_land.php">Sell Land</a></h5>
                        </div>
                    </div>
                </div>
                <!-- Add more dashboard cards as needed -->
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
