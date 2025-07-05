<?php
session_start();
include_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}
include '../auth/get_user_picture.php'; // Load $pictureDataUrl here
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

        $checkUsed = $pdo->prepare("SELECT * FROM land_parcels WHERE land_title_no = ?");
        $checkUsed->execute([$land_title_no]);

        if ($checkUsed->rowCount() > 0) {
            echo "<script>
                    alert('Error: This land title number has already been used for land registration.');
                    window.history.back();
                  </script>";
            exit();
        }

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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register Land</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../officials/styleofiicials.css">
    <link rel="stylesheet" href="style.css">
    <!-- Custom styles -->
    <style>
       
      
      
       
        
       
        .form-title {
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: #343a40;
        }
        .btn-submit {
            background-color: #28a745;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        label {
            font-weight: 600;
            color: #495057;
        }
        .btn-get-location {
            margin-top: 0.3rem;
        }
        .form-text {
            font-size: 0.9rem;
        }
        nav.navbar {
            background: white !important;
        }
        .location-dropdown {
    
    padding: 8px;
    border-radius: 6px;
    background-color: #f0fdf4;
    font-weight: bold;
}
 .profile-pic-dropdown {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
    
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column align-items-center p-3">
        <div class="sidebar-profile text-center mb-4">
            <img src="<?= $pictureDataUrl ?>" alt="Profile Picture" />
            <h5><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></h5>
            <p><i class="fas fa-circle"></i> Online</p>
        </div>

        <ul class="nav flex-column w-100">
            <li class="nav-item"><a href="owner_dashboard.php" class="nav-link"><i class="fa fa-home me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a href="register_land.php" class="nav-link active"><i class="fas fa-check-circle me-2"></i>Register Land</a></li>
            <li class="nav-item"><a href="search_land.php" class="nav-link"><i class="fas fa-search me-2"></i>Search Land</a></li>
            <li class="nav-item"><a href="view_requests.php" class="nav-link"><i class="fas fa-envelope-open-text me-2"></i>Requested Lands</a></li>
            <li class="nav-item"><a href="purchase_land.php" class="nav-link"><i class="fas fa-dollar-sign me-2"></i>Purchase Land</a></li>
            <li class="nav-item"><a href="owner_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up me-2"></i>Approve Requests</a></li>
            <li class="nav-item"><a href="owner_transfer_history.php" class="nav-link"><i class="fas fa-history me-2"></i>Transfer History</a></li>
            <li class="nav-item"><a href="see_lands.php" class="nav-link"><i class="fas fa-map me-2"></i>Buy Land</a></li>
            <li class="nav-item"><a href="my_lands.php" class="nav-link"><i class="fas fa-globe me-2"></i>View Your Land</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Land Owner Dashboard</h4>
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

        <div class="container">
            <h2 class="form-title">Register Your Land</h2>
            <form method="POST" action="register_land.php" class="needs-validation" novalidate>
                <!-- Land Title Number -->
                <div class="mb-3">
                    <label for="land_title_no" class="form-label">Land Title Number</label>
                    <input type="text" name="land_title_no" id="land_title_no" class="form-control" required />
                    <div class="form-text">
                        <a href="land_title_requests.php">Click here to request land title number</a>
                    </div>
                    <div class="invalid-feedback">
                        Please enter your land title number.
                    </div>
                </div>

                <!-- Land Size -->
                <div class="mb-3">
                    <label for="land_size" class="form-label">Land Size (in sq.m)</label>
                    <input type="number" name="land_size" id="land_size" class="form-control" min="1" required />
                    <div class="invalid-feedback">
                        Please enter a valid land size.
                    </div>
                </div>

                <!-- Land Use -->
                <div class="mb-3">
                   
                    <label for="land_use" >Land Use:</label>
                <select name="land_use" id="land_use" class="form-control"  required>
                    <option value="Residential">Residential</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Agricultural">Agricultural</option>
                    <option value="Industrial">Industrial</option>
                </select>
                    <div class="invalid-feedback">
                        Please specify the land use.
                    </div>
                </div>

                <!-- Location dropdowns -->
                 <div class="row mb-3">
                <label for="region" >Region:</label>
                <select id="region" name="region" class="location-dropdown" >
                    <option value="">Select Region</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= $region['region_name'] ?>"><?= $region['region_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            

            <!-- District Dropdown -->
            <div class="form-group">
                <label for="district" class="form-label">District:</label>
                <select id="district" name="district_name" class="location-dropdown"  required disabled>
                    <option value="" class="form-control">Select District</option>
                </select>
            </div>

            
            <!-- Ward Dropdown -->
            <div class="form-group">
                <label for="ward" class="form-label">Ward:</label>
             <select id="ward" name="ward_name" class="location-dropdown" required disabled>
                    <option value="">Select Ward</option>
                </select>
            </div>

            <!-- Village Dropdown -->
            <div class="form-group">
                <label for="village" class="form-label">Village:</label>
               <select id="village" name="village" class="location-dropdown"  required disabled>
                    <option value="">Select Village</option>
                </select>
            </div>

                </div>

                <!-- Latitude and Longitude -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control" readonly required />
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control" readonly required />
                    </div>
                </div>

                <button type="button" class="btn btn-outline-success btn-get-location" onclick="getLocation()">
                    <i class="fas fa-map-marker-alt me-2"></i>Get Current Location
                </button>

                <div class="mt-4">
                    <button type="submit" class="btn-submit">Submit Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Bootstrap form validation
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })();


    
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

    // Get Current Location and autofill lat/lon
    function getLocation() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (position) => {
                $('#latitude').val(position.coords.latitude.toFixed(6));
                $('#longitude').val(position.coords.longitude.toFixed(6));
            },
            (error) => {
                alert('Unable to retrieve your location. Please allow location access and try again.');
            }
        );
    }
</script>
</body>
</html>


