<?php
session_start();
require_once '../config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all lands that belong to the logged-in user
$stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE owner_id = ?");
$stmt->execute([$user_id]);
$lands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Lands</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../officials/styleofiicials.css"> <!-- External CSS -->
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
                <h4 class="navbar-brand">Owner Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name']; ?>!</span>
                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>

<div class="main-content">
    <h2>All Lands Owned by You</h2>

    <?php if (count($lands) > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Title No</th>
                <th>Size (Acres)</th>
                <th>Latitude</th>
                <th>Longtude</th>
                <th>Region</th>
                <th>District</th>
                <th>Ward</th>
                <th>Street/Village</th>
                <th>Status</th>
            </tr>
            <?php foreach ($lands as $land): ?>
                <tr>
                    <td><?= htmlspecialchars($land['land_title_no']) ?></td>
                    <td><?= htmlspecialchars($land['land_size']) ?></td>
                    <td><?= htmlspecialchars($land['latitude']) ?></td>
                    <td><?= htmlspecialchars($land['longitude']) ?></td>
                    <td><?= htmlspecialchars($land['region_name']) ?></td>
                    <td><?= htmlspecialchars($land['district_name']) ?></td>
                    <td><?= htmlspecialchars($land['ward_name']) ?></td>
                    <td><?= htmlspecialchars($land['village_name']) ?></td>
                    <td><?= htmlspecialchars($land['registration_status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You currently have no registered lands.</p>
    <?php endif; ?>
</div>

</body>
</html>
