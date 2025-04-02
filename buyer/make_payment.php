<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ..auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch land details based on the land ID
if (isset($_GET['land_id'])) {
    $land_id = $_GET['land_id'];
    $stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE land_id = ?");
    $stmt->execute([$land_id]);
    $land = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$land) {
        die("Land not found.");
    }
} else {
    die("No land selected.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment for Land</title>
    <link rel="stylesheet" href="seelands.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Land SYSTEM</h2>
        <ul>
            <li><a href="buyer_dashboard.php">Dashboard</a></li>
            <li><a href="view_available_lands.php">View Available Lands</a></li>
            <li><a href="view_purchase_requests.php">View Purchase Requests</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Make Payment for Land</h2>
        </header>

        <div class="content">
            <h3>Land Title No: <?= $land['land_title_no']; ?></h3>
            <p><strong>Size:</strong> <?= $land['land_size']; ?> Acres</p>
            <p><strong>Price:</strong> <?= number_format($land['price'], 2); ?> TZS</p>

            <form method="POST" action="process_payment.php">
                <input type="hidden" name="land_id" value="<?= $land['land_id']; ?>">
                <input type="hidden" name="price" value="<?= $land['price']; ?>">
                
                <button type="submit" name="make_payment" class="btn">Make Payment</button>
            </form>
        </div>
    </div>

</body>
</html>
