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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = $_POST['first_name'];
     $second_name   = $_POST['second_name'];
    $last_name   = $_POST['last_name'];
    $email       = $_POST['email'];
    $phone_number       = $_POST['phone_number'];
    $national_id = $_POST['national_id'];

    // Profile picture update
    $picture = $user['picture']; // existing
    if (!empty($_FILES['picture']['tmp_name'])) {
        $picture = file_get_contents($_FILES['picture']['tmp_name']);
    }

    $stmt = $pdo->prepare("
        UPDATE users SET 
            first_name = ?, 
            second_name = ?,
            last_name = ?, 
            email = ?, 
            phone_number = ?, 
            national_id = ?, 
            picture = ?
        WHERE user_id = ?
    ");

    $stmt->execute([
        $first_name,
        $second_name,
        $last_name,
        $email,
        $phone_number,
        $national_id,
        $picture,
        $_SESSION['user_id']
    ]);

    $_SESSION['success'] = "Profile updated successfully.";
    header("Location: profile.php");
    exit();
}

$pictureDataUrl = $user['picture'] 
    ? 'data:image/jpeg;base64,' . base64_encode($user['picture']) 
    : '../assets/default-avatar.png';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0d6efd;
        }
        .card {
            border-radius: 15px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card mx-auto p-4 shadow" style="max-width: 600px;">
        <h4 class="text-center mb-4">Edit Your Profile</h4>
        <div class="text-center mb-3">
            <img src="<?= $pictureDataUrl ?>" class="profile-img" alt="Profile Picture">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" class="form-control" required>
                </div>
                <div class="col">
                    <label>second Name</label>
                    <input type="text" name="second_name" value="<?= htmlspecialchars($user['second_name']) ?>" class="form-control" >
                </div>
                 <div class="col">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>National ID</label>
                <input type="text" name="national_id" value="<?= htmlspecialchars($user['national_id']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Profile Picture (optional)</label>
                <input type="file" name="picture" class="form-control">
            </div>

            <div class="d-flex justify-content-between">
                <a href="profile.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
