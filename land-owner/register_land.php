<?php
session_start();
include_once '../config/db.php';

$regions = $pdo->query("SELECT * FROM regions")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner_id = $_SESSION['user_id'];
    $land_title_no = trim($_POST['land_title_no']);
    $land_size = $_POST['land_size'];
    $land_use = $_POST['land_use'];
    $region_name = $_POST['region_name'];
    $district_name = $_POST['district_name'];
    $ward_name = $_POST['ward_name'];
    $village_name = $_POST['village_name'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    try {
        // ✅ Step 1: Check if title number is assigned to the current user
        $checkOwnership = $pdo->prepare("SELECT * FROM land_title_requests 
                                         WHERE land_title_no = ? AND user_id = ? AND request_status = 'approved'");
        $checkOwnership->execute([$land_title_no, $owner_id]);

        if ($checkOwnership->rowCount() == 0) {
            echo "<script>
                    alert('Error: This land title number is not assigned to your account or not approved yet.');
                    window.history.back();
                  </script>";
            exit();
        }

        // ✅ Step 2: Check if title number has already been used for a registration
        $checkUsed = $pdo->prepare("SELECT * FROM land_parcels WHERE land_title_no = ?");
        $checkUsed->execute([$land_title_no]);

        if ($checkUsed->rowCount() > 0) {
            echo "<script>
                    alert('Error: This land title number has already been used for land registration.');
                    window.history.back();
                  </script>";
            exit();
        }

        // ✅ Step 3: All checks passed, register land
        $stmt = $pdo->prepare("INSERT INTO land_parcels 
            (owner_id, land_title_no, land_size, land_use, region_name, district_name, ward_name, village_name, latitude, longitude) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$owner_id, $land_title_no, $land_size, $land_use, $region_name, $district_name, $ward_name, $village_name, $latitude, $longitude]);

        echo "<script>
                alert('SUCCESSFUL INFORMATION SENT');
                setTimeout(function(){
                    window.location.href = 'owner_dashboard.php';
                }, 2000);
              </script>";
    } catch (PDOException $e) {
        echo "<script>alert('Database Error: " . $e->getMessage() . "');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Land</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
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
    <div class="container">
        <h2 class="form-title">Register Your Land</h2>
        <form method="POST" action="register_land.php">
            <!-- Land Title Number -->
            <div class="form-group">
                <label for="land_title_no">Land Title Number:</label>
                <input type="text" name="land_title_no" id="land_title_no" required>
                <a href="land_title_requests.php">Click here to request land title No</a>
            </div>

            <!-- Land Size -->
            <div class="form-group">
                <label for="land_size">Land Size (in sq.m):</label>
                <input type="number" name="land_size" id="land_size" required>
            </div>

            <!-- Land Use -->
            <div class="form-group">
                <label for="land_use">Land Use:</label>
                <select name="land_use" id="land_use" required>
                    <option value="Residential">Residential</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Agricultural">Agricultural</option>
                    <option value="Industrial">Industrial</option>
                </select>
            </div>

            <!-- Region Dropdown -->
            <div class="form-group">
                <label for="region">Region:</label>
                <select name="region_name" id="region" required>
                    <option value="">Select Region</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= $region['region_name'] ?>"><?= $region['region_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- District Dropdown -->
            <div class="form-group">
                <label for="district">District:</label>
                <select name="district_name" id="district" required disabled>
                    <option value="">Select District</option>
                </select>
            </div>

            <!-- Ward Dropdown -->
            <div class="form-group">
                <label for="ward">Ward:</label>
                <select name="ward_name" id="ward" required disabled>
                    <option value="">Select Ward</option>
                </select>
            </div>

            <!-- Village Dropdown -->
            <div class="form-group">
                <label for="village">Village:</label>
                <select name="village_name" id="village" required disabled>
                    <option value="">Select Village</option>
                </select>
            </div>

            <!-- Latitude -->
            <div class="form-group">
                <label for="latitude">Latitude:</label>
                <input type="number" name="latitude" id="latitude" step="0.00000001" required>
            </div>

            <!-- Longitude -->
            <div class="form-group">
                <label for="longitude">Longitude:</label>
                <input type="number" name="longitude" id="longitude" step="0.00000001" required>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn-submit">Register Land</button>
            </div>
        </form>
    </div>

    <script>
       $(document).ready(function() {
    // Load districts when region is selected
    $('#region').change(function() {
        var region_name = $(this).val();
        if (region_name) {
            $.ajax({
                url: '../ajax/get_districts.php',  // Adjusted path to 'get_districts.php'
                method: 'GET',
                data: { region_name: region_name },
                success: function(data) {
                    var districts = JSON.parse(data);
                    var districtOptions = '<option value="">Select District</option>';
                    districts.forEach(function(district) {
                        districtOptions += '<option value="' + district.district_name + '">' + district.district_name + '</option>';
                    });
                    $('#district').html(districtOptions).prop('disabled', false);
                    $('#ward').html('<option value="">Select Ward</option>').prop('disabled', true);
                    $('#village').html('<option value="">Select Village</option>').prop('disabled', true);
                }
            });
        } else {
            $('#district').html('<option value="">Select District</option>').prop('disabled', true);
            $('#ward').html('<option value="">Select Ward</option>').prop('disabled', true);
            $('#village').html('<option value="">Select Village</option>').prop('disabled', true);
        }
    });

    // Load wards when district is selected
    $('#district').change(function() {
        var district_name = $(this).val();
        if (district_name) {
            $.ajax({
                url: '../ajax/get_wards.php',  // Adjusted path to 'get_wards.php'
                method: 'GET',
                data: { district_name: district_name },
                success: function(data) {
                    var wards = JSON.parse(data);
                    var wardOptions = '<option value="">Select Ward</option>';
                    wards.forEach(function(ward) {
                        wardOptions += '<option value="' + ward.ward_name + '">' + ward.ward_name + '</option>';
                    });
                    $('#ward').html(wardOptions).prop('disabled', false);
                    $('#village').html('<option value="">Select Village</option>').prop('disabled', true);
                }
            });
        } else {
            $('#ward').html('<option value="">Select Ward</option>').prop('disabled', true);
            $('#village').html('<option value="">Select Village</option>').prop('disabled', true);
        }
    });

    // Load villages when ward is selected
    $('#ward').change(function() {
        var ward_name = $(this).val();
        if (ward_name) {
            $.ajax({
                url: '../ajax/get_villages.php',  // Adjusted path to 'get_villages.php'
                method: 'GET',
                data: { ward_name: ward_name },
                success: function(data) {
                    var villages = JSON.parse(data);
                    var villageOptions = '<option value="">Select Village</option>';
                    villages.forEach(function(village) {
                        villageOptions += '<option value="' + village.village_name + '">' + village.village_name + '</option>';
                    });
                    $('#village').html(villageOptions).prop('disabled', false);
                }
            });
        } else {
            $('#village').html('<option value="">Select Village</option>').prop('disabled', true);
        }
    });
});
    </script>
</body>
</html>
