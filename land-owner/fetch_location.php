<?php
include_once '../config/db.php';

if (isset($_POST['type'], $_POST['parentName'])) {
    $type = $_POST['type'];
    $parent = $_POST['parentName'];

    switch ($type) {
        case 'district':
            $stmt = $pdo->prepare("SELECT * FROM districts WHERE region_name = ?");
            $stmt->execute([$parent]);
            break;
        case 'ward':
            $stmt = $pdo->prepare("SELECT * FROM wards WHERE district_name = ?");
            $stmt->execute([$parent]);
            break;
        case 'village':
            $stmt = $pdo->prepare("SELECT * FROM villages WHERE ward_name = ?");
            $stmt->execute([$parent]);
            break;
        default:
            $stmt = null;
    }

    if ($stmt) {
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
?>
