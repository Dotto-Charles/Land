<?php
session_start();
include_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// Fetch summary data
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalLands = $pdo->query("SELECT COUNT(*) FROM land_parcels")->fetchColumn();
$pendingVerifications = $pdo->query("SELECT COUNT(*) FROM land_parcels WHERE registration_status = 'pending'")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Land System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #343a40;
            color: white;
            position: fixed;
        }
        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
        }
        .card-summary {
            border-left: 5px solid #007bff;
            
        }
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

<div class="sidebar">
    <div class="p-4 text-center">
    
        <h5 class="mt-2"><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] ?></h5>
        <p><i class="fas fa-user-shield text-success"></i> Admin</p>
    </div>
    <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="verify_lands.php"><i class="fas fa-check-circle"></i> Verify Lands</a>
    <a href="transfer_requests.php"><i class="fas fa-random"></i> Transfer Requests</a>
    <a href="system_logs.php"><i class="fas fa-file-alt"></i> System Logs</a>
    <a href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
    
</div>

<div class="content">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Admin Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $pictureDataUrl ?>" class="profile-pic-dropdown" alt="User">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="../auth/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="../auth/change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </nav>

    <div class="row g-4 mb-5">
        
        <div class="col-md-4">
   
        <div class="card text-white bg-primary shadow-sm clickable-card" data-target="users">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-users me-2"></i>Total Users </h5>
            <p class="display-6"><?= $totalUsers ?></p> 
        </div>  
    </div>
</div>
<div class="col-md-4">
    <div class="card text-white bg-success shadow-sm clickable-card" data-target="lands">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-map-marked-alt me-2"></i>Registered Lands</h5>
            <p class="display-6"><?= $totalLands ?></p>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card text-white bg-warning shadow-sm clickable-card" data-target="pending">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-hourglass-half me-2"></i>Pending Verifications</h5>
            <p class="display-6"><?= $pendingVerifications ?></p>
        </div>
    </div>
</div>

    </div>

<div id="card-details" class="mb-4" style="display: none;">
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <span id="card-title">Details</span>
        </div>
        <div class="card-body" id="card-content">
            <!-- Dynamic content will load here -->
        </div>
    </div>
</div>


    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.clickable-card').forEach(card => {
        card.addEventListener('click', () => {
            const target = card.dataset.target;
            const titleMap = {
                'users': 'List of All Users',
                'lands': 'Registered Lands',
                'pending': 'Pending Land Verifications'
            };
            document.getElementById('card-title').innerText = titleMap[target];
            document.getElementById('card-details').style.display = 'block';

            // Use AJAX to load content (or load via PHP include)
            fetch(`load_${target}.php`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('card-content').innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('card-content').innerHTML = "<p class='text-danger'>Failed to load data.</p>";
                });
        });
    });
</script>

</body>
</html>
