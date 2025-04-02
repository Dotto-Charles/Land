<?php
include_once '../config/db.php'; // Ensure this points to your correct db.php file

if (isset($_GET['region_name'])) {
    $region_name = $_GET['region_name'];
    $stmt = $pdo->prepare("SELECT * FROM districts WHERE region_name = ?");
    $stmt->execute([$region_name]);
    $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($districts);
}
?>
