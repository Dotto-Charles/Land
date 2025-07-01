<?php
include_once '../config/db.php';

session_start();
include '../auth/get_user_picture.php'; // Load $pictureDataUrl here
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Get all lands owned by this owner with pending approval and already paid
$stmt = $pdo->prepare("
    SELECT p.*, l.land_title_no, u.first_name AS buyer_name
    FROM payments p
    JOIN land_parcels l ON p.land_id = l.land_id
    JOIN users u ON p.payer_id = u.user_id
    WHERE l.owner_id = ? AND p.payment_status = 'paid' AND l.owner_approval_status = 'pending'
");
$stmt->execute([$owner_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Land Sales</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../land-owner/style.css"> <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="style.css">
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
        <div class="sidebar-profile">
            <img src="../icons/profile.png" alt="Profile">
            <h5><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h5>
            <p><i class="fas fa-circle text-success"></i> Online</p>
        </div>
        <ul class="nav flex-column mt-2">
            <li class="nav-item">
                <a href="buyer_dashboard.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a>
            </li>
        
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a>
            </li>
            <li class="nav-item">
                <a href="purchase_land.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Sell Land</a>
            </li>
            <li class="nav-item">
                <a href="buyer_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up"></i> Approve Requests</a>
            </li>
            <li class="nav-item">
                <a href="buyer_transfer_history.php" class="nav-link"><i class="fas fa-history"></i> Transfer History</a>
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
                    <li><a class="dropdown-item" href="../auth/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="../auth/change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

        <div class="main-content">
    <h2>Land Sale Approval Requests</h2>

    <?php if (count($requests) > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Buyer</th>
                <th>Land Title No</th>
                <th>Amount</th>
                <th>Control Number</th>
                <th>Action</th>
            </tr>
            <?php foreach ($requests as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['buyer_name']) ?></td>
                    <td><?= htmlspecialchars($row['land_title_no']) ?></td>
                    <td><?= number_format($row['amount'], 2) ?> TZS</td>
                    <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                    <td>
                        <form method="POST" action="process_owner_approval.php" style="display:inline;">
                            <input type="hidden" name="land_id" value="<?= $row['land_id'] ?>">
                            <button type="submit" name="approve" class="btn-approve">Approve</button>
                        </form>
                        <form method="POST" action="process_owner_approval.php" style="display:inline;">
                            <input type="hidden" name="land_id" value="<?= $row['land_id'] ?>">
                            <button type="submit" name="reject" class="btn-reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
        There are no pending approval requests at the moment.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
