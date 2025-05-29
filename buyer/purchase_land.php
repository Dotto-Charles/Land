<?php
session_start();
include_once '../config/db.php'; // Database connection

$message = "";
$land = null;

// Ensure user is logged in
if (!isset($_SESSION['user_id'])|| $_SESSION['role'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Check if land title number is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_land'])) {
    $land_title_no = trim($_POST['land_title_no']);

    // Fetch land details including all required fields
    // Ensure land exists, belongs to user, and is approved
$stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE land_title_no = ? AND owner_id = ? AND registration_status = 'Approved'");

    $stmt->execute([$land_title_no, $user_id]);
    $land = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$land) {
        $message = "This land either does not belong to you or has not yet been approved for transactions.";
    }
    
}

// Handle purchase transaction
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['purchase_land'])) {
    $land_id = $_POST['land_id'];
    $amount = $_POST['price'];
    $status = $_POST['status'];
    // Ensure land exists and belongs to the user before updating the amount
    $stmt = $pdo->prepare("UPDATE land_parcels SET price = ?, status=? WHERE land_id = ? AND owner_id = ?");
    $update = $stmt->execute([$amount,$status, $land_id, $user_id]);

    if ($update) {
        $message = "Amount updated successfully!";
    } else {
        $message = "Failed to update the amount. Try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Land</title>
    <link rel="stylesheet" href="../land-owner/stylepurchase.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../officials/styleofiicials.css"> <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="../land-owner/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <a href="my_lands.php" class="nav-link"><i class="fas fa-globe"></i> View Your Land</a>
            </li>
            
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
       <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <div class="container-fluid">
        <h4 class="navbar-brand">Register Land</h4>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 text-primary fw-bold">
                Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!
            </span>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $pictureDataUrl ?>" alt="User" class="profile-pic-dropdown">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

        <!--   <div class="content">  -->
            <form method="POST" action="">
                <label>Enter Land Title Number:</label>
                <input type="text" name="land_title_no" required>
                <button type="submit" name="search_land">Search</button>
            </form>

            <?php if ($message): ?>
    <p style="color: red;"> <?= $message; ?> </p>
     <?php endif; ?>


            <?php if (!empty($land)): ?>
                <h2>Land Details</h2>
                <table>
                    <tr><td><strong>Land ID:</strong></td><td><?= htmlspecialchars($land['land_id']); ?></td></tr>
                    <tr><td><strong>Title Number:</strong></td><td><?= htmlspecialchars($land['land_title_no']); ?></td></tr>
                    <tr><td><strong>Land Size:</strong></td><td><?= htmlspecialchars($land['land_size']); ?> Acres</td></tr>
                    <tr><td><strong>Land Use:</strong></td><td><?= htmlspecialchars($land['land_use']); ?></td></tr>
                    <tr><td><strong>Latitude:</strong></td><td><?= htmlspecialchars($land['latitude']); ?></td></tr>
                    <tr><td><strong>Longitude:</strong></td><td><?= htmlspecialchars($land['longitude']); ?></td></tr>
                    <tr><td><strong>Region:</strong></td><td><?= htmlspecialchars($land['region_name']); ?></td></tr>
                    <tr><td><strong>District:</strong></td><td><?= htmlspecialchars($land['district_name']); ?></td></tr>
                    <tr><td><strong>Ward:</strong></td><td><?= htmlspecialchars($land['ward_name']); ?></td></tr>
                    <tr><td><strong>Village:</strong></td><td><?= htmlspecialchars($land['village_name']); ?></td></tr>
                    <tr>
    <td><strong>Current Price:</strong></td>
    <td>
        <?= is_numeric($land['price']) ? number_format((float)$land['price'], 2) : '0.00'; ?> TZS
    </td>
</tr>

                </table>

                <h2>Set Purchase Price</h2>
                <form method="POST" action="">
                    <input type="hidden" name="land_id" value="<?= htmlspecialchars($land['land_id']); ?>">
                    <label>Change Status</label>
                    <select name="status"  required>
                        <option value="Not_sell">Not Sell</option>
                        <option value="Sell">Sell</option>
                    </select>
                    <label>Enter New Price (TZS):</label>
                    <input type="number" name="price" min="1" required>
                    <button type="submit" name="purchase_land">Set Price</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
