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

// Prepare user profile image (optional fallback if missing)
$pictureDataUrl = isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])
    ? $_SESSION['profile_picture']
    : '../assets/default-profile.png'; // fallback image
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Lands</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../officials/styleofiicials.css">
    <link rel="stylesheet" href="../land-owner/style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; }
        .main-content { padding: 30px; }
        .profile-pic-dropdown {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #ccc;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background-color: #007bff;
            color: white;
        }
        .sidebar-profile img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .table th, .table td {
            vertical-align: middle;
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
                <a href="buyer_dashboard.php" class="nav-link"><i class="fa fa-user"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Sell Land</a>
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
                <a href="my_lands.php" class="nav-link active"><i class="fas fa-globe"></i> View Your Land</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content flex-grow-1">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Registered Land Parcels</h4>
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

        <!-- Table Section -->
        <div class="main-content">
            <h2 class="mb-4">All Lands Owned by You</h2>

            <?php if (count($lands) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered shadow-sm rounded">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>Title No</th>
                                <th>Size (Acres)</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Region</th>
                                <th>District</th>
                                <th>Ward</th>
                                <th>Street/Village</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
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
                                    <td>
                                        <span class="badge bg-<?= 
                                            $land['registration_status'] === 'Approved' ? 'success' : 
                                            ($land['registration_status'] === 'Pending' ? 'warning' : 'secondary') ?>">
                                            <?= htmlspecialchars($land['registration_status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">You currently have no registered lands.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
