<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) && ($_SESSION['role'] !== 'buyer' || $_SESSION['role'] !== 'landowner')) {
    header("Location: ..auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch all land parcels with status 'sell' and approved
$stmt = $pdo->prepare("SELECT * FROM land_parcels WHERE status = 'Sell' AND registration_status = 'Approved'");
$stmt->execute();
$lands = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Lands for Sale</title>
    <link rel="stylesheet" href="seelands.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Land SYSTEM</h2>
        <ul>
            <li><a href="buyer_dashboard.php">Dashboard</a></li>
            <li><a href="view_available_lands.php" class="active">View Available Lands</a></li>
            <li><a href="view_purchase_requests.php">View Purchase Requests</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Buyer Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name']; ?>!</span>
                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>
        <header>
            <h2>Available Lands for Sale</h2>
        </header>

        <div class="content">
            <?php if (count($lands) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Land Title No</th>
                            <th>Size (Acres)</th>
                            <th>Region</th>
                            <th>District</th>
                            <th>Ward</th>
                            <th>Stree/Village</th>
                            <th>Price (TZS)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lands as $land): ?>
                            <tr>
                                <td><?= $land['land_title_no']; ?></td>
                                <td><?= $land['land_size']; ?></td>
                                <td><?= $land['region_name']; ?></td>
                                <td><?= $land['district_name']; ?></td>
                                <td><?= $land['ward_name']; ?></td>
                                <td><?= $land['village_name']; ?></td>
                                <td><?= number_format($land['price'], 2); ?></td>
                                <td>
                                    <a href="make_payment.php?land_id=<?= $land['land_id']; ?>" class="btn">Make Payment</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No lands available for purchase at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
