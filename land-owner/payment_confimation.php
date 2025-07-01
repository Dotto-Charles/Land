<?php
include_once '../config/db.php';
session_start();

if (!isset($_GET['transaction_id'])) {
    die("Invalid request. Control number is missing.");
}

$transaction_id = $_GET['transaction_id'];

// Fetch payment record by control number
$stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_id = ?");
$stmt->execute([$transaction_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("Payment record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="seelands.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../officials/styleofiicials.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-pic-dropdown {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
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
        .succe {
    background-color: #f8f9fa;
    border-left: 6px solid #28a745;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin-top: 40px;
    animation: fadeIn 0.6s ease-in-out;
}

.succe h3 {
    color: #28a745;
    font-size: 24px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.succe h3::before {
    content: "\f058"; /* FontAwesome check-circle */
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    margin-right: 10px;
    color: #28a745;
    font-size: 26px;
}

.succe p {
    font-size: 16px;
    color: #333;
    margin-bottom: 10px;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
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
            <li class="nav-item"><a href="owner_dashboard.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a></li>
            <li class="nav-item"><a href="register_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Register Land</a></li>
            <li class="nav-item"><a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a></li>
            <li class="nav-item"><a href="view_requests.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Requested Lands</a></li>
            <li class="nav-item"><a href="purchase_land.php" class="nav-link"><i class="fas fa-dollar-sign"></i> Sell Land</a></li>
            <li class="nav-item"><a href="owner_approve_requests.php" class="nav-link"><i class="fas fa-thumbs-up"></i> Approve Requests</a></li>
            <li class="nav-item"><a href="owner_transfer_history.php" class="nav-link"><i class="fas fa-history"></i> Transfer History</a></li>
            <li class="nav-item"><a href="see_lands.php" class="nav-link"><i class="fas fa-map"></i> Buy Land</a></li>
            <li class="nav-item"><a href="my_lands.php" class="nav-link"><i class="fas fa-globe"></i> View Your Land</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content flex-grow-1">
        <!-- Top Navbar -->
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
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="container mt-4">
    <div class="succe" id="receipt-content">
    <h3>Payment Confirmation</h3>
    <p>Your payment for the land has been successfully processed.</p>
    <p><strong>Control Number:</strong> <?= $payment['transaction_id']; ?></p>
    <p><strong>Amount Paid:</strong> <?= number_format($payment['amount'], 2); ?> TZS</p>
    <p><strong>Payment Method:</strong> <?= $payment['payment_type']; ?></p>
    <p><strong>Status:</strong> <?= ucfirst($payment['payment_status']); ?></p>

   <a href="generate_receipt.php?transaction_id=<?= $payment['transaction_id']; ?>" class="btn btn-primary mt-3">
    Download Receipt (PDF)
</a>

</div>

 </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


</body>
</html>
