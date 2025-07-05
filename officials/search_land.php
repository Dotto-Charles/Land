
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government_official') {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $land_title_no = trim($_POST['land_title_no']);

    if (!empty($land_title_no)) {
        try {
            $sql = "SELECT 
                        lp.land_title_no, lp.land_size, lp.land_use, lp.latitude, lp.longitude, lp.registration_status, 
                        u.first_name, u.last_name, u.phone_number,
                        r.region_name, d.district_name, w.ward_name, v.village_name
                    FROM land_parcels lp
                    JOIN users u ON lp.owner_id = u.user_id
                    JOIN regions r ON lp.region_name = r.region_name
                    JOIN districts d ON lp.district_name = d.district_name
                    JOIN wards w ON lp.ward_name = w.ward_name
                    JOIN villages v ON lp.village_name = v.village_name
                    WHERE lp.land_title_no = :land_title_no";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':land_title_no', $land_title_no, PDO::PARAM_STR);
            $stmt->execute();
            $land = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$land) {
                $error = "No land found with the given title number.";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        $error = "Please enter a land title number.";
    }
}

$pictureDataUrl = isset($_SESSION['profile_picture']) && $_SESSION['profile_picture'] != ''
    ? $_SESSION['profile_picture']
    : '../assets/default-profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Land</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS + FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        .sidebar {
            width: 250px;
            background: #343a40;
            min-height: 100vh;
            color: white;
            position: fixed;
            padding-top: 20px;
        }
        .sidebar-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0d6efd;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #0d6efd;
            color: white;
            border-radius: 5px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
            border-radius: 10px;
        }
        .profile-pic-dropdown {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #0d6efd;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-profile text-center p-3">
            <img src="<?= htmlspecialchars($pictureDataUrl) ?>" alt="Profile Picture" />
            <h5><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></h5>
            <p><i class="fas fa-circle text-success"></i> Online</p>
        </div>
        <ul class="nav flex-column">
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
    <div class="content w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 mb-4">
            <div class="container-fluid">
                <h4 class="navbar-brand mb-0">Search Land</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 text-primary fw-bold">
                        Welcome, <?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?>
                    </span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="<?= htmlspecialchars($pictureDataUrl) ?>" class="profile-pic-dropdown" />
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../auth/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="../auth/change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Search -->
        <div class="card shadow-sm p-4">
            <h4 class="text-primary mb-4"><i class="fas fa-search-location me-2"></i>Search Land by Title Number</h4>
            <form method="POST" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="land_title_no" class="form-control form-control-lg" placeholder="Enter Land Title Number" required>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-search"></i> Search</button>
                </div>
            </form>

            <?php if (isset($error)) : ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (isset($land) && $land) : ?>
                <div class="mt-4">
                    <h5 class="text-success"><i class="fas fa-map-marker-alt me-2"></i>Land Information</h5>
                    <div class="card border-success shadow-sm mt-3">
                        <div class="card-body p-3">
                            <table class="table table-bordered table-striped mb-0">
                                <tr><th>Land Title No</th><td><?= htmlspecialchars($land['land_title_no']) ?></td></tr>
                                <tr><th>Owner</th><td><?= htmlspecialchars($land['first_name'] . ' ' . $land['last_name']) ?></td></tr>
                                <tr><th>Phone</th><td><?= htmlspecialchars($land['phone_number']) ?></td></tr>
                                <tr><th>Land Size</th><td><?= htmlspecialchars($land['land_size']) ?> sqm</td></tr>
                                <tr><th>Land Use</th><td><span class="badge bg-info"><?= htmlspecialchars($land['land_use']) ?></span></td></tr>
                                <tr><th>Latitude</th><td><?= htmlspecialchars($land['latitude']) ?></td></tr>
                                <tr><th>Longitude</th><td><?= htmlspecialchars($land['longitude']) ?></td></tr>
                                <tr><th>Region</th><td><?= htmlspecialchars($land['region_name']) ?></td></tr>
                                <tr><th>District</th><td><?= htmlspecialchars($land['district_name']) ?></td></tr>
                                <tr><th>Ward</th><td><?= htmlspecialchars($land['ward_name']) ?></td></tr>
                                <tr><th>Village</th><td><?= htmlspecialchars($land['village_name']) ?></td></tr>
                                <tr><th>Registration Status</th>
                                    <td>
                                        <?php
                                            $badge = 'secondary';
                                            if ($land['registration_status'] === 'approved') $badge = 'success';
                                            else if ($land['registration_status'] === 'pending') $badge = 'warning';
                                            else if ($land['registration_status'] === 'rejected') $badge = 'danger';
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars(ucfirst($land['registration_status'])) ?></span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Leaflet Map -->
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS + Leaflet -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php if (isset($land) && $land && is_numeric($land['latitude']) && is_numeric($land['longitude'])) : ?>
<script>
    const map = L.map('map').setView([<?= $land['latitude'] ?>, <?= $land['longitude'] ?>], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([<?= $land['latitude'] ?>, <?= $land['longitude'] ?>])
        .addTo(map)
        .bindPopup("<strong><?= htmlspecialchars($land['land_title_no']) ?></strong><br><?= htmlspecialchars($land['land_use']) ?>")
        .openPopup();
</script>
<?php endif; ?>
</body>
</html>
