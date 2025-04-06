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
    <link rel="stylesheet" href="styleofiicials.css"> <!-- External CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
                <a href="manage_requests.php" class="nav-link"><i class="fas fa-tasks"></i> Manage Requests</a>
            </li>
            <li class="nav-item">
                <a href="gov_transfer_history.php" class="nav-link"><i class="fas fa-chart-line"></i> Land Transfer History</a>
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

</body>
</html>
