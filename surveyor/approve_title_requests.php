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
    <style>
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

<div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <h4 class="navbar-brand">Surveyor Dashboard</h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <?= $_SESSION['first_name']; ?>!</span>
                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>
        <a href="javascript:history.back()" class="back-btn">Back</a>

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
    <p>No pending title requests.</p>
<?php endif; ?>
</div>
</body>
</html>
