<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../officials/styleofiicials.css"> <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="../land-owner/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
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
                <a href="buyer_dashboard.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a>
            </li>
        
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Sell Land</a>
            </li>
            <li class="nav-item">
                <a href="buyer_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up"></i> Approve Requests</a>
            </li>
            <li class="nav-item">
                <a href="buyer_transfer_history.php" class="nav-link"><i class="fas fa-history"></i> Transfer History</a>
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
        <h4 class="navbar-brand">Buyer Dashboard</h4>
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
                <!-- Verify Land -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="../icons/register.png" class="icon-img" alt="Verify Land">
                            <h5><a href="see_lands.php">See Lands</a></h5>
                        </div>
                    </div>
                </div>

                <!-- Manage Requests -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="icons/requests.png" class="icon-img" alt="Manage Requests">
                            <h5><a href="my_lands.php">My Lands</a></h5>
                        </div>
                    </div>
                </div>


                
                <!-- View Reports -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="icons/reports.png" class="icon-img" alt="View Reports">
                            <h5><a href="buyer_transfer_history.php">Transfer History</a></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
