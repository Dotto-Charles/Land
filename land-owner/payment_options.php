<?php
include_once '../config/db.php';
session_start();

// Check if the control number is passed in the query string
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
    <title>Payment Options</title>
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
        .payment-options .btn.mobile {
    background: linear-gradient(135deg, #28a745, #218838);
}

.payment-options .btn.mobile:hover {
    background: linear-gradient(135deg, #218838, #1e7e34);
}

.payment-options .btn.bank {
    background: linear-gradient(135deg, #17a2b8, #117a8b);
}

.payment-options .btn.bank:hover {
    background: linear-gradient(135deg, #117a8b, #0f6674);
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
        
        <div class="container mt-4">
        <header>
            <h2>Payment Options</h2>
        </header>

    
            <h3>Land Title No: <?= $payment['land_id']; ?></h3>
            <p><strong>Price:</strong> <?= number_format($payment['amount'], 2); ?> TZS</p>
            <p><strong>Control Number:</strong> <?= $payment['transaction_id']; ?></p>

            <h4>Choose Payment Method:</h4>

            <!-- Payment Method Options -->
            <div class="payment-options">
                <form method="POST" action="process_mobile_payment.php">
    <input type="hidden" name="transaction_id" value="<?= $payment['transaction_id']; ?>">
    <button type="submit" class="btn mobile">Pay via Mobile (MPesa)</button>
</form>

<form method="POST" action="process_bank_payment.php">
    <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($payment['transaction_id']); ?>">
    <button type="submit" class="btn bank">Pay via Bank</button>
</form>


            </div>
        </div>
    </div>
  </div>  </div>  
  <!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
