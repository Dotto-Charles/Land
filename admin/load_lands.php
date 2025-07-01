<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Land System Dashboard</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="mb-4 text-primary"><i class="fas fa-database"></i> Land Registration System</h3>




<?php
require_once '../config/db.php';

try {
    $stmt = $pdo->query("
        SELECT 
            lp.*, 
            u.first_name, 
            u.last_name, 
            u.email 
        FROM 
            land_parcels lp
        JOIN 
            users u ON lp.owner_id = u.user_id
        ORDER BY lp.registered_at DESC
    ");
    $lands = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error fetching land records: " . $e->getMessage() . "</div>";
    exit;
}
?>

<div class="card border-primary mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-map-marked-alt"></i> Registered Land Parcels
    </div>
    <div class="card-body table-responsive">
        <?php if (count($lands) > 0): ?>
        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Title No</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Size (m²)</th>
                    <th>Use</th>
                    <th>Region</th>
                    <th>District</th>
                    <th>Ward</th>
                    <th>Village</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Status</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lands as $index => $land): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($land['land_title_no']) ?></td>
                        <td><?= htmlspecialchars($land['first_name'] . ' ' . $land['last_name']) ?></td>
                        <td><?= htmlspecialchars($land['email']) ?></td>
                        <td><?= number_format($land['land_size'], 2) ?></td>
                        <td><span class="badge bg-info"><?= $land['land_use'] ?></span></td>
                        <td><?= htmlspecialchars($land['region_name']) ?></td>
                        <td><?= htmlspecialchars($land['district_name']) ?></td>
                        <td><?= htmlspecialchars($land['ward_name']) ?></td>
                        <td><?= htmlspecialchars($land['village_name']) ?></td>
                        <td><?= $land['latitude'] ?></td>
                        <td><?= $land['longitude'] ?></td>
                        <td>
                            <?php
                                $statusClass = match($land['registration_status']) {
                                    'Approved' => 'success',
                                    'Pending' => 'warning',
                                    'Rejected' => 'danger',
                                    default => 'secondary'
                                };
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $land['registration_status'] ?></span>
                        </td>
                        <td><?= date('Y-m-d H:i', strtotime($land['registered_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="text-muted">No registered land parcels found.</p>
        <?php endif; ?>
    </div>
</div>




    <!-- Bootstrap JS Bundle CDN (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
