<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government_official') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officials Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleofiicials.css">
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
                <a href="officials_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="verify_land.php" class="nav-link">
                    <i class="fas fa-map-marked-alt"></i> Verify Land
                </a>
            </li>
            <li class="nav-item">
                <a href="gov_ownership_approval.php" class="nav-link">
                    <i class="fas fa-tasks"></i> Manage Requests
                </a>
            </li>
            <li class="nav-item">
                <a href="gov_transfer_history.php" class="nav-link">
                    <i class="fas fa-history"></i> Transfer History
                </a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link">
                    <i class="fas fa-search-location"></i> Search Land
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Official Dashboard</h4>
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
                            <h5><a href="verify_land.php">Verify Land</a></h5>
                        </div>
                    </div>
                </div>

                <!-- Manage Requests -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="icons/requests.png" class="icon-img" alt="Manage Requests">
                            <h5><a href="gov_ownership_approval.php">Manage Requests</a></h5>
                        </div>
                    </div>
                </div>

                <!-- View Reports -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="icons/reports.png" class="icon-img" alt="View Reports">
                            <h5><a href="view_reports.php">View Reports</a></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
