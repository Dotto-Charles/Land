<?php
if (!isset($pdo)) {
    include '../config/db.php';
}
$pictureDataUrl = '../icons/default_profile.jpg';

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT picture FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['picture'])) {
            $base64Image = base64_encode($row['picture']);
            $pictureDataUrl = 'data:image/jpeg;base64,' . $base64Image;
        }
    } catch (PDOException $e) {
        $pictureDataUrl = '../icons/default_profile.jpg';
    }
}
?>
