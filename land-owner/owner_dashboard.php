<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landowner Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../officials/styleofiicials.css"> <!-- External CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <a href="view_requests.php" class="nav-link"><i class="fas fa-chart-line"></i> View all Your Requested Land</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-chart-line"></i> Purchase Land</a>
            </li>
            <li class="nav-item">
            <a href="owner_approve_requests.php" class="nav-link"><i class="fas fa-chart-line"></i> Approve Requests</a>
            </li>
            <li class="nav-item">
            <a href="owner_transfer_history.php" class="nav-link"><i class="fas fa-chart-line"></i> Transfer History</a>
            </li> <li class="nav-item">
            <a href="../buyer/see_lands.php" class="nav-link"><i class="fas fa-chart-line"></i> Buy Land</a>
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
                    <span class="me-3">Welcome, <?= $_SESSION['first_name']; ?>!</span>
                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>


            <section class="dashboard-features">
                <div class="feature-card">
                    <a href="register_land.php">
                        <img src="../icons/register.png" alt="Register Land">
                        <h3>Register Land</h3>
                    </a>
                </div>
                
                <div class="feature-card">
                    <a href="search_land.php">
                        <img src="../icons/search.png" alt="Search Land">
                        <h3>Search Land</h3>
                    </a>
                </div>

                <div class="feature-card">
                    <a href="view_requests.php">
                        <img src="../icons/requests.png" alt="View Requests">
                        <h3>View All Your Requested Land</h3>
                    </a>
                </div>

                <div class="feature-card">
                    <a href="purchase_land.php">
                        <img src="../icons/sell.png" alt="Sell Land">
                        <h3>Sell Land</h3>
                    </a>
                </div>
                
            </section>
        </main>
    </div>
</body>
</html>
