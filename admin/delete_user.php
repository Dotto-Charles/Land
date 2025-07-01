<?php
include_once '../config/db.php';
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->execute([$id]);

header("Location: manage_users.php");
exit();
?>