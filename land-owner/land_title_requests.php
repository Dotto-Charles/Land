<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user has already submitted a pending request
    $check = $pdo->prepare("SELECT * FROM land_title_requests WHERE user_id = ? AND request_status = 'Pending'");
    $check->execute([$user_id]);

    if ($check->rowCount() > 0) {
        header("Location: land_title_requests.php?message=You already have a pending request.");
        exit;
    }

    // Create new request
    $stmt = $pdo->prepare("INSERT INTO land_title_requests (user_id) VALUES (?)");
    $stmt->execute([$user_id]);

    header("Location: land_title_requests.php?message=Title request submitted successfully.");
    exit;
}

// Fetch user's past requests
$requests = $pdo->prepare("SELECT * FROM land_title_requests WHERE user_id = ?");
$requests->execute([$user_id]);
$my_requests = $requests->fetchAll();
?>

<!-- HTML + Styling -->
<!DOCTYPE html>
<html>
<head>
    <title>Request Land Title Number</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../officials/styleofiicials.css"> <!-- External CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { margin-bottom: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        button { padding: 10px 15px; }
        .msg { color: green; margin-bottom: 10px; }
    </style>
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
                <span class="me-3">Welcome, <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>

                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>
<h2>Request a New Land Title Number</h2>

<?php if (isset($_GET['message'])): ?>
    <div class="msg"><?= htmlspecialchars($_GET['message']) ?></div>
<?php endif; ?>

<form method="POST">
    <p>Click below to request a land title number. A surveyor will review and assign a title if approved.</p>
    <button type="submit">Request Land Title Number</button>
</form>

<h3>Your Land Title Requests</h3>
<table>
    <thead>
        <tr>
            <th>Request ID</th>
            <th>Status</th>
            <th>Land Title Number</th>
            <th>Requested At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($my_requests as $req): ?>
            <tr>
                <td><?= $req['request_id'] ?></td>
                <td><?= $req['request_status'] ?></td>
                <td><?= $req['land_title_no'] ?? 'Pending' ?></td>
                <td><?= $req['requested_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
