<?php
include_once '../config/db.php'; // Ensure this points to your correct db.php file

if (isset($_GET['district_name'])) {
    $district_name = $_GET['district_name'];
    $stmt = $pdo->prepare("SELECT * FROM wards WHERE district_name = ?");
    $stmt->execute([$district_name]);
    $wards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($wards);
}
?>
