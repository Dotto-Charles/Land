<?php
session_start();
include_once '../config/db.php';  // Ensure this path is correct based on your folder structure

// Fetch regions
$regions = $pdo->query("SELECT * FROM regions")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner_id = $_SESSION['user_id'];  // Assuming user ID is stored in session after login
    $land_title_no = $_POST['land_title_no'];
    $land_size = $_POST['land_size'];
    $land_use = $_POST['land_use'];
    $region_name = $_POST['region_name'];
    $district_name = $_POST['district_name'];
    $ward_name = $_POST['ward_name'];
    $village_name = $_POST['village_name'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    try {
        // Check for duplicate land title number
        $checkStmt = $pdo->prepare("SELECT * FROM land_parcels WHERE land_title_no = ?");
        $checkStmt->execute([$land_title_no]);
        
        if ($checkStmt->rowCount() > 0) {
            echo "<script>
                    alert('Error: A land title number already exists in the system.');
                    window.history.back();  // Takes user back to the form
                  </script>";
            exit();
        }
    
        // Insert land registration into the database
        $stmt = $pdo->prepare("INSERT INTO land_parcels (owner_id, land_title_no, land_size, land_use, region_name, district_name, ward_name, village_name, latitude, longitude)
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="form-title">Register Your Land</h2>
        <form method="POST" action="register_land.php">
            <!-- Land Title Number -->
            <div class="form-group">
                <label for="land_title_no">Land Title Number:</label>
                <input type="text" name="land_title_no" id="land_title_no" required>
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
