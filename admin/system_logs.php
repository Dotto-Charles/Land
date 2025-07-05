<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$logs = $pdo->query("SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 100")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Logs | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 1200px;
            margin-top: 50px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(to right, #007bff, #0056b3);
            color: white;
            padding: 25px;
            font-size: 1.4rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header .btn-home {
            background-color: white;
            color: #007bff;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            border-radius: 5px;
            transition: 0.3s;
        }

        .card-header .btn-home:hover {
            background-color: #e2e6ea;
            color: #0056b3;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table th, .table td {
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f1faff;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #777;
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header .btn-home {
                margin-top: 10px;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="card-header">
                🛡️ System Activity Logs
                <a href="admin_dashboard.php" class="btn btn-home">🏠 Home</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Action</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($log['created_at']) ?></td>
                                        <td><?= htmlspecialchars($log['user_name']) ?></td>
                                        <td><?= htmlspecialchars($log['role']) ?></td>
                                        <td><?= htmlspecialchars($log['action']) ?></td>
                                        <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No logs available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="footer">
            &copy; <?= date("Y") ?> Land Registration System | Admin Panel
        </div>
    </div>

</body>
</html>