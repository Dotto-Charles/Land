<?php
require_once '../config/db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName   = trim($_POST['first_name']);
    $lastName    = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone_number']);
    $role        = $_POST['role'];
    $password    = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $picture = '';
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $picture = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['picture']['tmp_name'], '../uploads/' . $picture);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone_number, role, password, picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $phone, $role, $password, $picture]);
        $success = "User added successfully.";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow border-primary">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-user-plus"></i> Add New User</h4>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Select Role --</option>
                            <option value="admin">Admin</option>
                            <option value="surveyor">Surveyor</option>
                            <option value="landowner">Land Owner</option>
                            <option value="buyer">Buyer</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Picture (optional)</label>
                    <input type="file" name="picture" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="manage_users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
