<?php
session_start();
include_once '../config/db.php';  // Database connection
require '../config/mail.php';  // Email configuration (PHPMailer)

// Check if user is logged in as a government official
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'government_official') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle registration approval or rejection
if (isset($_GET['action']) && isset($_GET['land_id'])) {
    $land_id = $_GET['land_id'];
    $action = $_GET['action'];

    // Get landowner details
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

    $email = $land['email'];
    $first_name = $land['first_name'];
    $second_name = $land['second_name'];
    $last_name = $land['last_name'];
    $title_no = $land['land_title_no'];
    $status = ($action == 'approve') ? 'approved' : 'rejected';

    // Update land status
    $stmt = $pdo->prepare("UPDATE land_parcels SET registration_status = ? WHERE land_id = ?");
    $stmt->execute([$status, $land_id]);

    // Insert into land_verifications table
    $stmt = $pdo->prepare("INSERT INTO land_verifications (requester_id, land_id, land_title_no, verification_status, verified_by, verified_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->execute([$_SESSION['user_id'], $land_id, $title_no, $status, $_SESSION['user_id']]);


    // Email Subject & Body
    $subject = "Land Registration Update - Title No: $title_no";
    $message = "Dear $first_name, <br><br>Your land registration (Title No: $title_no) has been <b>$status</b>.<br><br>Regards, <br>Land Registration Office.";

    // Send email
    sendEmail($email, $subject, $message);

    // Redirect back
    header("Location: verify_land.php?message=" . urlencode("Land registration updated, email sent."));
    exit;
}

// Fetch only pending land registrations
$stmt = $pdo->query("SELECT * FROM land_parcels WHERE registration_status = 'pending'");
$land_registrations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Land Registrations</title>
    <link rel="stylesheet" href="styleverify.css">
</head>
<body>
    <div class="container">
        <h2 class="form-title">Pending Land Registrations</h2>

        <?php if (isset($_GET['message'])): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($_GET['message']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (count($land_registrations) > 0): ?>
            <table class="land-table">
                <thead>
                    <tr>
                        <th>Land Title No</th>
                        <th>Land Size (sq.m)</th>
                        <th>Land Use</th>
                        <th>Region</th>
                        <th>District</th>
                        <th>Ward</th>
                        <th>Village</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Approve</th>
                        <th>Reject</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($land_registrations as $land): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($land['land_title_no']); ?></td>
                            <td><?php echo htmlspecialchars($land['land_size']); ?></td>
                            <td><?php echo htmlspecialchars($land['land_use']); ?></td>
                            <td><?php echo htmlspecialchars($land['region_name']); ?></td>
                            <td><?php echo htmlspecialchars($land['district_name']); ?></td>
                            <td><?php echo htmlspecialchars($land['ward_name']); ?></td>
                            <td><?php echo htmlspecialchars($land['village_name']); ?></td>
                            <td><?php echo htmlspecialchars($land['latitude']); ?></td>
                            <td><?php echo htmlspecialchars($land['longitude']); ?></td>
                            <td>
                                <a href="verify_land.php?action=approve&land_id=<?php echo $land['land_id']; ?>" class="btn-approve">Approve</a>
                            </td>
                            <td>
                                <a href="verify_land.php?action=reject&land_id=<?php echo $land['land_id']; ?>" class="btn-reject">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending land registrations at the moment.</p>
        <?php endif; ?>
        <div style="margin-top: 20px;">
        <button onclick="goBack()" class="btn-back">Back</button>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

    </div>
</body>
</html>
