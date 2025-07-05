<?php
session_start();
include '../config/db.php';

// Delete handler
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);
    header("Location: view_message.php?deleted=1");
    exit;
}

// Search & Pagination
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$where = "";
$params = [];

if ($search !== '') {
    $where = "WHERE name LIKE :search OR email LIKE :search OR subject LIKE :search";
    $params[':search'] = "%$search%";
}

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM messages $where");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$totalPages = ceil($total / $limit);

$stmt = $pdo->prepare("SELECT * FROM messages $where ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Inbox</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f7fa;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .top-nav {
            text-align: right;
            margin-bottom: 10px;
        }

        .btn-home {
            display: inline-block;
            padding: 8px 16px;
            background: #17a2b8;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-home:hover {
            background: #138496;
        }

        .search-box {
            text-align: right;
            margin-bottom: 20px;
        }

        .search-box input {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-box button {
            padding: 8px 15px;
            background: #007bff;
            border: none;
            color: #fff;
            border-radius: 6px;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #0056b3;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h3 {
            margin: 0 0 5px;
            color: #007bff;
        }

        .card p {
            margin: 5px 0;
        }

        .card small {
            color: #888;
        }

        .actions {
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            margin-right: 10px;
        }

        .btn-reply {
            background: #28a745;
            color: #fff;
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
        }

        .btn-reply:hover {
            background: #218838;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .pagination {
            text-align: center;
            margin-top: 30px;
        }

        .pagination a,
        .pagination strong {
            display: inline-block;
            margin: 0 5px;
            padding: 8px 12px;
            background: #e9ecef;
            color: #333;
            border-radius: 6px;
            text-decoration: none;
        }

        .pagination strong {
            background: #007bff;
            color: #fff;
        }

        .status {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-nav">
        <a href="admin_dashboard.php" class="btn-home">🏠 Home</a>
    </div>

    <h1>Admin Message Inbox</h1>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="status">Message deleted successfully.</div>
    <?php endif; ?>

    <div class="search-box">
        <form method="get" action="view_message.php">
            <input type="text" name="search" placeholder="Search by name, email, subject" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="card">
                <h3><?= htmlspecialchars($msg['subject']) ?></h3>
                <p><strong>From:</strong> <?= htmlspecialchars($msg['name']) ?> | <?= htmlspecialchars($msg['email']) ?></p>
                <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                <small>Received on: <?= $msg['created_at'] ?? 'N/A' ?></small>

                <div class="actions">
                    <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re: <?= urlencode($msg['subject']) ?>" class="btn btn-reply">Reply</a>
                    <a href="?delete=<?= $msg['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1<?= $search ? '&search=' . urlencode($search) : '' ?>">First</a>
                <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">&laquo; Prev</a>
            <?php endif; ?>

            <strong>Page <?= $page ?> of <?= $totalPages ?></strong>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Next &raquo;</a>
                <a href="?page=<?= $totalPages ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Last</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>No messages found.</p>
    <?php endif; ?>
</div>

</body>
</html>
