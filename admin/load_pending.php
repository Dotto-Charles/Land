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
require '../config/db.php';

$sql = "SELECT id, owner_name, region, district, ward, village, land_size, status FROM lands WHERE status = 'pending' ORDER BY registration_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-warning">
                <tr>
                    <th>#</th>
                    <th>Owner</th>
                    <th>Region</th>
                    <th>District</th>
                    <th>Ward</th>
                    <th>Village</th>
                    <th>Size (Acres)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['owner_name']) ?></td>
                    <td><?= htmlspecialchars($row['region']) ?></td>
                    <td><?= htmlspecialchars($row['district']) ?></td>
                    <td><?= htmlspecialchars($row['ward']) ?></td>
                    <td><?= htmlspecialchars($row['village']) ?></td>
                    <td><?= htmlspecialchars($row['land_size']) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= ucfirst($row['status']) ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>No pending verifications found.</p>
<?php endif;

$conn->close();
?>
    </div>

    <!-- Bootstrap JS Bundle CDN (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
