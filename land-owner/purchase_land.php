<?php
session_start();
include_once '../config/db.php'; // Database connection

$message = "";
$land = null;

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Check if land title number is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_land'])) {
    $land_title_no = trim($_POST['land_title_no']);

    // Fetch land details including all required fields
    $stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE land_title_no = ? AND owner_id = ?");
    $stmt->execute([$land_title_no, $user_id]);
    $land = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$land) {
        $message = "Land not found! Please enter a valid title number.";
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
    <link rel="stylesheet" href="stylepurchase.css">
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
                <a href="register_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Register Land</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-tasks"></i> search Land</a>
            </li>
            <li class="nav-item">
                <a href="view_requests.php" class="nav-link"><i class="fas fa-chart-line"></i> View all Your Requested Land</a>
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

        <div class="content">
            <form method="POST" action="">
                <label>Enter Land Title Number:</label>
                <input type="text" name="land_title_no" required>
                <button type="submit" name="search_land">Search</button>
            </form>

            <?php if ($message): ?>
                <p style="color: green;"> <?= $message; ?> </p>
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
                    <label>Enter New Price (TZS):</label>
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
</body>
</html>
