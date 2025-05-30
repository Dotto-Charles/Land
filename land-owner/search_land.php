<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Land</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="style.css">
    <style>
.back-btn {
    display: inline-block;
    padding: 8px 20px;
    background-color: #2e7d32; /* Dark green */
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 15px;
}

.back-btn:hover {
    background-color: #1b5e20; /* Darker green on hover */
}
</style>

</head>
<body>
    <div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-profile">
            <img src="../icons/profile.png" alt="Profile">
            <h5><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h5>
            <p><i class="fas fa-circle text-success"></i> Online</p>
        </div>
        <ul class="nav flex-column mt-2">
            <li class="nav-item">
                <a href="owner_dashboard.php" class="nav-link"><i class="fa fa-user"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="register_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Register Land</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a>
            </li>
            <li class="nav-item">
                <a href="view_requests.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Requested Lands</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-dollar-sign"></i> Purchase Land</a>
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
                <a href="my_lands.php" class="nav-link"><i class="fas fa-globe"></i> View Your Land</a>
            </li>
            
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Land Owner Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>
                    <!-- User Dropdown -->
<div class="dropdown">
    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle fa-2x text-primary"></i>
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
    <div class="container">
    <a href="javascript:history.back()" class="back-btn">Back</a>

        <h2>Search Land</h2>
        <form method="POST">
            <input type="text" name="land_title_no" placeholder="Enter Land Title Number" required>
            <button type="submit">Search</button>
        </form>

        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <?php if (isset($land) && $land) { ?>
    <div class="result">
        <h3>Land Information</h3>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr><th>Land Title No</th><td><?= htmlspecialchars($land['land_title_no']); ?></td></tr>
            <tr><th>Owner</th><td><?= htmlspecialchars($land['first_name'] . ' ' . $land['last_name']); ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($land['phone_number']); ?></td></tr>
            <tr><th>Land Size</th><td><?= htmlspecialchars($land['land_size']); ?> sqm</td></tr>
            <tr><th>Land Use</th><td><?= htmlspecialchars($land['land_use']); ?></td></tr>
            <tr><th>Latitude</th><td><?= htmlspecialchars($land['latitude']); ?></td></tr>
            <tr><th>Longitude</th><td><?= htmlspecialchars($land['longitude']); ?></td></tr>
            <tr><th>Region</th><td><?= htmlspecialchars($land['region_name']); ?></td></tr>
            <tr><th>District</th><td><?= htmlspecialchars($land['district_name']); ?></td></tr>
            <tr><th>Ward</th><td><?= htmlspecialchars($land['ward_name']); ?></td></tr>
            <tr><th>Village</th><td><?= htmlspecialchars($land['village_name']); ?></td></tr>
            <tr><th>Registration Status</th><td><?= htmlspecialchars($land['registration_status']); ?></td></tr>
        </table>
    </div>
<?php } ?>

    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
