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

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <div class="container-fluid">
        <h4 class="navbar-brand">Land Admin Panel</h4>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 text-primary fw-bold">
                <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>
            </span>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $pictureDataUrl ?>" alt="User" class="profile-pic-dropdown">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="change_password.php">Change Password</a></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
