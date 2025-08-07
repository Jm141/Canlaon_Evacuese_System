<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">User Management</h2>
    <a href="users.php?action=add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="users.php" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= htmlspecialchars($filters['search']) ?>" 
                       placeholder="Search by name, username, or email">
            </div>
            <div class="col-md-2">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="">All Roles</option>
                    <option value="main_admin" <?= $filters['role'] === 'main_admin' ? 'selected' : '' ?>>Main Admin</option>
                    <option value="admin" <?= $filters['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= $filters['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="1" <?= $filters['status'] === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $filters['status'] === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <a href="users.php" class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No users found</h5>
                <p class="text-muted">Try adjusting your search criteria or add a new user.</p>
                <a href="users.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First User
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Barangay</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $userItem): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            <?= strtoupper(substr($userItem['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($userItem['full_name']) ?></strong>
                                            <?php if ($userItem['id'] == $user['id']): ?>
                                                <span class="badge bg-info ms-1">You</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($userItem['username']) ?></td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($userItem['email']) ?>">
                                        <?= htmlspecialchars($userItem['email']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $roleColors = [
                                        'main_admin' => 'danger',
                                        'admin' => 'warning',
                                        'staff' => 'info'
                                    ];
                                    $roleColor = $roleColors[$userItem['role']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $roleColor ?>">
                                        <?= ucwords(str_replace('_', ' ', $userItem['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= ($userItem['barangay_name'] ?? null) ? htmlspecialchars($userItem['barangay_name']) : '<span class="text-muted">All</span>' ?>
                                </td>
                                <td>
                                    <?php if ($userItem['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($userItem['last_login'] ?? null): ?>
                                        <?= date('M j, Y g:i A', strtotime($userItem['last_login'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="users.php?action=view&id=<?= $userItem['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($userItem['id'] != $user['id']): ?>
                                            <a href="users.php?action=edit&id=<?= $userItem['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info btn-reset-password" 
                                                    data-id="<?= $userItem['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($userItem['full_name']) ?>"
                                                    title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-status" 
                                                    data-id="<?= $userItem['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($userItem['full_name']) ?>"
                                                    data-status="<?= $userItem['is_active'] ?>"
                                                    title="<?= $userItem['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                <i class="fas fa-<?= $userItem['is_active'] ? 'ban' : 'check' ?>"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                    data-id="<?= $userItem['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($userItem['full_name']) ?>"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Users pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($filters['search']) ?>&role=<?= urlencode($filters['role']) ?>&status=<?= urlencode($filters['status']) ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&role=<?= urlencode($filters['role']) ?>&status=<?= urlencode($filters['status']) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($filters['search']) ?>&role=<?= urlencode($filters['role']) ?>&status=<?= urlencode($filters['status']) ?>">
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user "<strong id="deleteUserName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="users.php?ajax=delete" style="display: inline;">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset the password for "<strong id="resetPasswordUserName"></strong>"?</p>
                <p class="text-info"><small>A new password will be generated and displayed.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="users.php?ajax=reset_password" style="display: inline;">
                    <input type="hidden" name="user_id" id="resetPasswordUserId">
                    <button type="submit" class="btn btn-info">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <span id="toggleStatusAction"></span> the user "<strong id="toggleStatusUserName"></strong>"?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="users.php?ajax=toggle_status" style="display: inline;">
                    <input type="hidden" name="user_id" id="toggleStatusUserId">
                    <button type="submit" class="btn btn-secondary">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteUserName').textContent = name;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
    
    // Reset password confirmation
    document.querySelectorAll('.btn-reset-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            document.getElementById('resetPasswordUserId').value = id;
            document.getElementById('resetPasswordUserName').textContent = name;
            
            new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
        });
    });
    
    // Toggle status confirmation
    document.querySelectorAll('.btn-toggle-status').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const status = this.dataset.status;
            const action = status === '1' ? 'deactivate' : 'activate';
            
            document.getElementById('toggleStatusUserId').value = id;
            document.getElementById('toggleStatusUserName').textContent = name;
            document.getElementById('toggleStatusAction').textContent = action;
            
            new bootstrap.Modal(document.getElementById('toggleStatusModal')).show();
        });
    });
});
</script> 