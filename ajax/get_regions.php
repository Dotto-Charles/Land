<?php
include_once '../config/db.php';
$query = "SELECT * FROM regions";
$result = $db->query($query);
$regions = [];

while ($row = $result->fetch_assoc()) {
    $regions[] = $row;
}

echo json_encode($regions);
?>
