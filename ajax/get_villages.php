<?php
include_once '../config/db.php'; // Ensure this points to your correct db.php file

if (isset($_GET['ward_name'])) {
    $ward_name = $_GET['ward_name'];
    $stmt = $pdo->prepare("SELECT * FROM villages WHERE ward_name = ?");
    $stmt->execute([$ward_name]);
    $villages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($villages);
}
?>
