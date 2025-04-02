<?php
session_start();
include '../config/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

try {
    // Query to fetch all land requests and registration statuses for the logged-in user
    $sql = "SELECT lp.land_title_no, lp.registration_status, r.region_name, d.district_name, w.ward_name, v.village_name
            FROM land_parcels lp
            JOIN regions r ON lp.region_name = r.region_name
            JOIN districts d ON lp.district_name = d.district_name
            JOIN wards w ON lp.ward_name = w.ward_name
            JOIN villages v ON lp.village_name = v.village_name
            WHERE lp.owner_id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $lands = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Land Requests</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .status-pending {
            background-color: blue;
            color: white;
        }

        .status-registered {
            background-color: green;
            color: white;
        }

        .status-rejected {
            background-color: red;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        /* Back button styling */
        .back-btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            margin: 10px;
            display: inline-block;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back button -->
        <a href="owner_dashboard.php" class="back-btn">Back</a> <!-- Update 'previous_page.php' with the correct link -->

        <h2>My Land Requests</h2>

        <!-- Table displaying land title numbers and registration statuses -->
        <table>
            <thead>
                <tr>
                    <th>Land Title Number</th>
                    <th>Region</th>
                    <th>District</th>
                    <th>Ward</th>
                    <th>Village</th>
                    <th>Registration Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($lands) > 0): ?>
                    <?php foreach ($lands as $land): ?>
                        <tr>
                            <td><?= htmlspecialchars($land['land_title_no']); ?></td>
                            <td><?= htmlspecialchars($land['region_name']); ?></td>
                            <td><?= htmlspecialchars($land['district_name']); ?></td>
                            <td><?= htmlspecialchars($land['ward_name']); ?></td>
                            <td><?= htmlspecialchars($land['village_name']); ?></td>
                            <td class="status-<?= strtolower($land['registration_status']); ?>">
                                <?= htmlspecialchars($land['registration_status']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No land requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
