<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">User Details</h2>
    <div>
        <a href="users.php?action=edit&id=<?= $user['id'] ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="users.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- User Information -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> User Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($user['full_name']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($user['username']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Role</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-<?= $user['role'] === 'main_admin' ? 'danger' : ($user['role'] === 'admin' ? 'warning' : 'info') ?>">
                                <?= ucwords(str_replace('_', ' ', $user['role'])) ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Last Login</label>
                        <p class="form-control-plaintext">
                            <?= $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Barangay Assignment -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt"></i> Barangay Assignment
                </h6>
            </div>
            <div class="card-body">
                <?php if ($user['barangay_name'] ?? null): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assigned Barangay</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-primary"><?= htmlspecialchars($user['barangay_name']) ?></span>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This user is not assigned to any specific barangay.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-clock"></i> Account Information
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Created Date</label>
                    <p class="form-control-plaintext"><?= date('F j, Y g:i A', strtotime($user['created_at'])) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="form-control-plaintext"><?= date('F j, Y g:i A', strtotime($user['updated_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-tools"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="users.php?action=edit&id=<?= $user['id'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-<?= $user['is_active'] ? 'warning' : 'success' ?>" 
                            onclick="toggleUserStatus(<?= $user['id'] ?>, <?= $user['is_active'] ? 0 : 1 ?>)">
                        <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i> 
                        <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?> User
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('user_id', userId);
        
        fetch('users.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = 'users.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user.');
        });
    }
}

function toggleUserStatus(userId, newStatus) {
    const action = newStatus ? 'activate' : 'deactivate';
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('is_active', newStatus);
        
        fetch('users.php?action=toggle-status', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the user status.');
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 