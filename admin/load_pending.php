<?php
session_start();
require '../config/db.php';

try {
    $stmt = $pdo->prepare("
        SELECT lp.land_id, 
               CONCAT(u.first_name, ' ', COALESCE(u.second_name, ''), ' ', u.last_name) AS owner_name, 
               lp.region_name, lp.district_name, lp.ward_name, lp.village_name, 
               lp.land_size, lp.registration_status, lp.registered_at
        FROM land_parcels lp
        JOIN users u ON lp.owner_id = u.user_id
        WHERE lp.registration_status = 'Pending'
        ORDER BY lp.registered_at DESC
    ");
    $stmt->execute();
    $lands = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Land Registrations</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f4f8;
            padding: 30px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h3 {
            margin-bottom: 20px;
        }
        .badge {
            font-size: 0.85rem;
        }
        .btn-home {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="btn btn-secondary btn-home"><i class="fas fa-home"></i> Home</a>
    <h3 class="text-primary"><i class="fas fa-file-alt"></i> Pending Land Registrations</h3>

    <?php if (!empty($lands)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Owner</th>
                        <th>Region</th>
                        <th>District</th>
                        <th>Ward</th>
                        <th>Village</th>
                        <th>Size (m²)</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; foreach ($lands as $row): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars(trim($row['owner_name'])) ?></td>
                        <td><?= htmlspecialchars($row['region_name']) ?></td>
<td><?= htmlspecialchars($row['district_name']) ?></td>
<td><?= htmlspecialchars($row['ward_name']) ?></td>
<td><?= htmlspecialchars($row['village_name']) ?></td>

                            <td><?= htmlspecialchars($row['land_size']) ?></td>
                            <td><span class="badge bg-warning text-dark"><?= $row['registration_status'] ?></span></td>
                            <td><?= date('Y-m-d H:i', strtotime($row['registered_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No pending land registration found.</div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>