<?php
include_once '../config/db.php';
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='alert alert-warning'>User not found.</div>";
    exit();
}
?>

<div class="container">
    <div class="text-center mb-3">
        <img src="../uploads/<?= htmlspecialchars($user['picture']) ?>" width="100" class="rounded-circle shadow">
    </div>
    <ul class="list-group">
        <li class="list-group-item"><strong>Full Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></li>
        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
        <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($user['phone_number']) ?></li>
        <li class="list-group-item"><strong>Role:</strong> <?= ucfirst(htmlspecialchars($user['role'])) ?></li>
        <li class="list-group-item"><strong>Registered At:</strong> <?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></li>
    </ul>
</div>
