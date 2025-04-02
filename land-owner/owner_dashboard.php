<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landowner') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landowner Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Land System</h2>
            <ul>
                <li><a href="owner_dashboard.php">Dashboard</a></li>
                <li><a href="register_land.php">Register Land</a></li>
                <li><a href="search_land.php">Search Land</a></li>
                <li><a href="view_requests.php">View All Your Requested Land</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="content">
        <header>
    <div class="welcome-container">
        <h2 class="welcome">Welcome, <?php echo $_SESSION['first_name']; ?>!</h2>
        <p class="dashboard-text">Your Land Registration and Verification Dashboard</p>
    </div>
</header>


            <section class="dashboard-features">
                <div class="feature-card">
                    <a href="register_land.php">
                        <img src="../icons/register.png" alt="Register Land">
                        <h3>Register Land</h3>
                    </a>
                </div>
                
                <div class="feature-card">
                    <a href="search_land.php">
                        <img src="../icons/search.png" alt="Search Land">
                        <h3>Search Land</h3>
                    </a>
                </div>

                <div class="feature-card">
                    <a href="view_requests.php">
                        <img src="../icons/requests.png" alt="View Requests">
                        <h3>View All Your Requested Land</h3>
                    </a>
                </div>

                <div class="feature-card">
                    <a href="purchase_land.php">
                        <img src="../icons/sell.png" alt="Sell Land">
                        <h3>Sell Land</h3>
                    </a>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
