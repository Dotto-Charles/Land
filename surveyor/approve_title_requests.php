<?php
session_start();
include '../config/db.php';
require '../config/mail.php';  // For sending email

// Ensure only surveyors can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'surveyor') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle approval
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $request_id = $_GET['approve'];

    // Fetch request and user info
    $stmt = $pdo->prepare("SELECT r.*, u.email, u.first_name 
                           FROM land_title_requests r 
                           JOIN users u ON r.user_id = u.user_id 
                           WHERE r.request_id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if ($request) {
        // Generate unique title number
        $unique_title = 'LT-' . strtoupper(substr(uniqid(), 0, 8));

        // Update request with approval and title number
        $stmt = $pdo->prepare("UPDATE land_title_requests 
                               SET request_status = 'approved', land_title_no = ? 
                               WHERE request_id = ?");
        $stmt->execute([$unique_title, $request_id]);

        // Send title number via email
        $subject = "Your Land Title Number Has Been Generated";
        $message = "Dear {$request['first_name']},<br><br>Your request has been approved.<br>
                    Your Land Title Number is: <strong>$unique_title</strong>.<br><br>Regards,<br>Land Survey Office.";
        sendEmail($request['email'], $subject, $message);

        header("Location: approve_title_requests.php?message=Title number approved and sent to user.");
        exit;
    }
}

// Fetch pending requests
$stmt = $pdo->query("SELECT r.request_id, r.requested_at, u.first_name, u.last_name, u.email 
                     FROM land_title_requests r 
                     JOIN users u ON r.user_id = u.user_id 
                     WHERE r.request_status = 'pending'");
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Surveyor Approval Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../officials/styleofiicials.css">
    <link rel="stylesheet" href="../land-owner/style.css">
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
            hei
            .profile-pic-dropdown {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #007bff;
    }
ght: 60px;
        }
    
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        table { border-collapse: collapse; width: 100%; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; background-color: #f4f4f4; }
        .btn { background: green; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        h2 { margin-bottom: 20px; }
        .message { background: #c7f0c4; padding: 10px; margin-bottom: 20px; border-left: 5px solid green; }

        form { margin-bottom: 30px; }

        .back-btn {
    display: inline-block; padding: 8px 20px; background-color: #2e7d32; /* Dark green */ color: white; text-decoration: none;
    border: none; border-radius: 4px;font-weight: bold;text-align: center;box-shadow: 0 2px 4px rgba(0,0,0,0.1);margin-bottom: 15px;
}

.back-btn:hover { background-color: #1b5e20; /* Darker green on hover */}

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
            <li class="nav-item"><a href="surveyor_dashboard.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a></li>
            <li class="nav-item"><a href="approve_title_requests.php" class="nav-link"><i class="fas fa-check-circle"></i> Approve Title Number</a></li>
            <li class="nav-item"><a href="search_land.php" class="nav-link"><i class="fas fa-search"></i> Search Land</a></li>
          
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content flex-grow-1">
        <!-- Top Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <div class="container-fluid">
        <h4 class="navbar-brand">Surveyor Dashboard</h4>
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
 <!-- Dashboard Content -->
        <div class="container mt-4">



<h2>Pending Land Title Number Requests</h2>

<?php if (isset($_GET['message'])): ?>
    <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
<?php endif; ?>

<?php if (count($requests) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Requester Name</th>
                <th>Email</th>
                <th>Request Date</th>
                <th>Approve</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?></td>
                    <td><?= htmlspecialchars($req['email']) ?></td>
                    <td><?= htmlspecialchars($req['requested_at']) ?></td>
                    <td><a href="?approve=<?= $req['request_id'] ?>" class="btn">Approve & Send Title</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
        No pending title requests.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
</div>
 </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
