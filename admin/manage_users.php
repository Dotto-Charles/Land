<?php
require_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <?php
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $userId = (int)$_GET['id'];

    echo '<div class="mb-4">';
    if ($action === 'edit') {
        include 'edit_user.php';
    } elseif ($action === 'view') {
        include 'view_user.php';
    }
    echo '</div>';
}
?>

    <div class="card border-primary shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-users"></i> Manage Users</h4>
            <a href="add_user.php" class="btn btn-light btn-sm">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>
        <div class="card-body table-responsive">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error loading users: " . $e->getMessage() . "</div>";
                exit;
            }
            ?>

            <?php if (count($users) > 0): ?>
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= ucfirst($user['role'] ?? 'user') ?></span>
                            </td>
                            <td><?= date('Y-m-d H:i', strtotime($user['created_at'] ?? '')) ?></td>
                            <td>
 <!-- View Button -->
<a href="manage_users.php?action=view&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-info" title="View">
    <i class="fas fa-eye"></i>
</a>

<!-- Edit Button -->
<a href="manage_users.php?action=edit&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning" title="Edit">
    <i class="fas fa-edit"></i>
</a>


                                <a href="delete_user.php?id=<?= $user['user_id'] ?>" 
   class="btn btn-sm btn-danger" 
   onclick="return confirmDelete('<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>');" 
   title="Delete">
   <i class="fas fa-trash-alt"></i>
</a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="text-muted">No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmDelete(name) {
    return confirm("Are you sure you want to delete " + name + "?");
}
</script>


<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="userModalContent">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Loading...</p>
        </div>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function openUserModal(userId, type) {
    let actionText = type === 'edit' ? 'edit this user' : 'view this user\'s details';
    let confirmMessage = `Are you sure you want to ${actionText}?`;

    if (!confirm(confirmMessage)) return; // Kama hakubali, simamisha

    // Ikitokea hapa, user amekubali
    $('#userModal').modal('show');
    $('#userModalLabel').text(type === 'edit' ? 'Edit User' : 'User Details');

    // Show loading spinner
    $('#userModalContent').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Loading...</p>
        </div>
    `);

    // Load content via AJAX
    let url = (type === 'edit') ? 'edit_user.php' : 'view_user.php';
    $('#userModalContent').load(url + '?id=' + userId);
}
</script>


</body>
</html>
