<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch land details
if (isset($_GET['land_id'])) {
    $land_id = $_GET['land_id'];
    $stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE land_id = ?");
    $stmt->execute([$land_id]);
    $land = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$land) {
        die("Land not found.");
    }
} else {
    die("No land selected.");
}

// Fetch profile picture
$pictureDataUrl = isset($_SESSION['picture']) ? $_SESSION['picture'] : '../images/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment for Land</title>
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

        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .content {
            background-color: #fff;
            padding: 20px;
        }

        .card-custom {
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
        }

        .btn-custom:hover {
            background-color: #0056b3;
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
        <ul class="nav flex-column px-3">
            <li class="nav-item"><a href="owner_dashboard.php" class="nav-link"><i class="fa fa-home me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a href="register_land.php" class="nav-link"><i class="fas fa-check-circle me-2"></i>Register Land</a></li>
            <li class="nav-item"><a href="search_land.php" class="nav-link"><i class="fas fa-search me-2"></i>Search Land</a></li>
            <li class="nav-item"><a href="view_requests.php" class="nav-link"><i class="fas fa-envelope-open-text me-2"></i>Requested Lands</a></li>
            <li class="nav-item"><a href="purchase_land.php" class="nav-link"><i class="fas fa-dollar-sign me-2"></i>Sell Land</a></li>
            <li class="nav-item"><a href="owner_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up me-2"></i>Approve Requests</a></li>
            <li class="nav-item"><a href="owner_transfer_history.php" class="nav-link"><i class="fas fa-history me-2"></i>Transfer History</a></li>
            <li class="nav-item"><a href="see_lands.php" class="nav-link"><i class="fas fa-map me-2"></i>Buy Land</a></li>
            <li class="nav-item"><a href="my_lands.php" class="nav-link"><i class="fas fa-globe me-2"></i>View Your Land</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand mb-0">Land Owner Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $pictureDataUrl ?>" class="profile-pic-dropdown" alt="User">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="../auth/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Payment Content -->
        <div class="container my-5">
            <div class="card card-custom p-4">
                <h3 class="mb-3 text-primary">Make Payment for Land</h3>
                <div class="mb-3"><strong>Land Title No:</strong> <?= $land['land_title_no']; ?></div>
                <div class="mb-3"><strong>Size:</strong> <?= $land['land_size']; ?> Acres</div>
                <div class="mb-3"><strong>Price:</strong> <?= number_format($land['price'], 2); ?> TZS</div>

                <form method="POST" action="process_payment.php">
                    <input type="hidden" name="land_id" value="<?= $land['land_id']; ?>">
                    <input type="hidden" name="price" value="<?= $land['price']; ?>">
                    <button type="submit" name="make_payment" class="btn btn-custom">Make Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
