<?php
session_start();
include_once '../config/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'government_official') {
    header("Location: ../auth/login.php");
    exit;
}

// Email function
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tittocharles@gmail.com';
        $mail->Password = 'lagbaxrfcdgkndvm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tittocharles@gmail.com', 'Land Registration Office');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
    }
}

// Handle approval or rejection
if (isset($_GET['action'], $_GET['land_id'])) {
    $land_id = $_GET['land_id'];
    $action = $_GET['action'];
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    $stmt = $pdo->prepare("SELECT lp.land_title_no, u.email, u.first_name, u.second_name, u.last_name 
                           FROM land_parcels lp 
                           JOIN users u ON lp.owner_id = u.user_id 
                           WHERE lp.land_id = ?");
    $stmt->execute([$land_id]);
    $land = $stmt->fetch();

    if (!$land) {
        header("Location: verify_land.php?message=" . urlencode("Land not found!"));
        exit;
    }

    $stmt = $pdo->prepare("UPDATE land_parcels SET registration_status = ? WHERE land_id = ?");
    $stmt->execute([$status, $land_id]);

    $stmt = $pdo->prepare("INSERT INTO land_verifications 
        (requester_id, land_id, land_title_no, verification_status, verified_by, verified_at)
        VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['user_id'],
        $land_id,
        $land['land_title_no'],
        $status,
        $_SESSION['user_id']
    ]);

    $email = $land['email'];
    $full_name = $land['first_name'] . ' ' . $land['second_name'] . ' ' . $land['last_name'];
    $subject = "Land Registration Update - Title No: {$land['land_title_no']}";
    $message = "Dear $full_name,<br><br>Your land registration (Title No: {$land['land_title_no']}) has been <b>$status</b>.<br><br>Regards,<br>Land Registration Office.";

    sendEmail($email, $subject, $message);

    header("Location: verify_land.php?message=" . urlencode("Land registration $status and email sent."));
    exit;
}

// Fetch pending land registrations
$stmt = $pdo->query("SELECT lp.land_id, lp.latitude,lp.longitude, lp.land_size, lp.land_title_no, lp.registered_at, 
                            u.first_name, u.second_name, u.last_name 
                     FROM land_parcels lp
                     JOIN users u ON lp.owner_id = u.user_id
                     WHERE lp.registration_status = 'pending'");
$land_registrations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Land Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleofiicials.css"> <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="../land-owner/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; background-color: #fff; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background-color: #f0f0f0; }
        .btn { padding: 6px 10px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; }
        .approve { background-color: #4CAF50; color: white; }
        .reject { background-color: #f44336; color: white; }
        .message { padding: 10px; background-color: #dff0d8; border: 1px solid #d0e9c6; color: #3c763d; margin-bottom: 15px; }
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
                <a href="officials_dashboard.php" class="nav-link"><i class="fa fa-home"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="verify_land.php" class="nav-link"><i class="fas fa-check-circle"></i> Verify Land</a>
            </li>
            <li class="nav-item">
                <a href="gov_ownership_approval.php" class="nav-link"><i class="fas fa-search"></i> Manage Requests</a>
            </li>
            <li class="nav-item">
                <a href="gov_transfer_history.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Transfer History</a>
            </li>
            <li class="nav-item">
                <a href="search_land.php" class="nav-link"><i class="fas fa-dollar-sign"></i> Search Land</a>
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

 <div class="container mt-4">

<h2>Pending Land Registrations</h2>

<?php if (isset($_GET['message'])): ?>
    <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
<?php endif; ?>

<?php if (count($land_registrations) === 0): ?>
    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
        No pending land registrations at this time
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Owner Full Name</th>
            
                <th>Latitude</th>
                <th>Longtude</th>
                <th>Land Size (acres)</th>
                <th>Land Title Number</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($land_registrations as $land): ?>
            <tr>
                <td><?= htmlspecialchars($land['first_name'] . ' ' . $land['second_name'] . ' ' . $land['last_name']) ?></td>
             <td><?= htmlspecialchars($land['latitude']) ?></td>
                <td><?= htmlspecialchars($land['longitude']) ?></td>
                <td><?= htmlspecialchars($land['land_size']) ?></td>
                <td><?= htmlspecialchars($land['land_title_no']) ?></td>
                <td><?= htmlspecialchars($land['registered_at']) ?></td>
                <td>
                    <a class="btn approve" href="?action=approve&land_id=<?= $land['land_id'] ?>" onclick="return confirm('Are you sure to approve this land?');">Approve</a>
                    <a class="btn reject" href="?action=reject&land_id=<?= $land['land_id'] ?>" onclick="return confirm('Are you sure to reject this land?');">Reject</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
