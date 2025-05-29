<?php
session_start();
include_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$pictureDataUrl = $user['picture'] 
    ? 'data:image/jpeg;base64,' . base64_encode($user['picture']) 
    : '../assets/default-avatar.png';
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0d6efd;
        }

        .card {
            transition: 0.3s;
            border-radius: 20px;
        }

        .profile-info {
            display: none;
        }

        .card:hover {
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
        }

        .btn-show-info {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-show-info:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="card shadow mx-auto p-4" style="max-width: 600px;">
        <div class="text-center">
            <h3><i class="fas fa-user-circle me-2"></i>My Profile</h3>
            <p class="btn-show-info text-muted mt-2" onclick="toggleInfo()">Click to show profile details</p>
        </div>

        <div id="profileInfo" class="profile-info text-center mb-4">
            <h5 class="fw-bold"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
            <p><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($user['email']) ?></p>
            <p><i class="fas fa-phone me-2"></i><?= htmlspecialchars($user['phone_number']) ?></p>
            <p><i class="fas fa-id-card me-2"></i><?= htmlspecialchars($user['national_id']) ?></p>
            <p><i class="fas fa-user-tag me-2"></i><?= ucfirst($user['role']) ?></p>
            <p><i class="fas fa-calendar-alt me-2"></i>Joined: <?= date("F j, Y", strtotime($user['created_at'])) ?></p>
        </div>

        <div class="text-center">
            <img src="<?= $pictureDataUrl ?>" alt="Profile Picture" class="profile-img mb-3">
        </div>

        <div class="d-flex justify-content-center">
            <a href="edit_profile.php" class="btn btn-outline-primary me-2"><i class="fas fa-edit me-1"></i>Edit Profile</a>
            <a href="change_password.php" class="btn btn-outline-secondary"><i class="fas fa-key me-1"></i>Change Password</a>
        </div>
    </div>
</div>

<script>
    function toggleInfo() {
        const info = document.getElementById('profileInfo');
        info.style.display = info.style.display === 'none' || info.style.display === '' ? 'block' : 'none';
    }
</script>
</body>
</html>
