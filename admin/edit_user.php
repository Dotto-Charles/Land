<?php
include_once '../config/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='alert alert-warning'>User not found.</div>";
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=? WHERE user_id=?");
    $stmt->execute([$first_name, $last_name, $email, $role, $id]);

    // Redirect back to manage users
    echo "<script>window.location.href='manage_users.php';</script>";
    exit();
}
?>

<!-- Display edit form -->
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning text-dark">
        <h5>Edit User: <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
    </div>
    <div class="card-body">
        <form method="post">
            <div class="mb-3">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="owner" <?= $user['role'] === 'owner' ? 'selected' : '' ?>>Owner</option>
                    <option value="surveyor" <?= $user['role'] === 'surveyor' ? 'selected' : '' ?>>Surveyor</option>
                    <option value="official" <?= $user['role'] === 'official' ? 'selected' : '' ?>>Official</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
