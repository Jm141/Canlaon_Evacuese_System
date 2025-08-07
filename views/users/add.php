<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Add User</h2>
    <a href="users.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="users.php?action=add">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Basic Information</h5>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Account Settings</h5>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <?php if ($user['role'] === 'main_admin'): ?>
                                <option value="main_admin" <?= ($_POST['role'] ?? '') === 'main_admin' ? 'selected' : '' ?>>Main Administrator</option>
                            <?php endif; ?>
                            <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                            <option value="staff" <?= ($_POST['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="barangay_id" class="form-label">Assigned Barangay</label>
                        <select class="form-select" id="barangay_id" name="barangay_id">
                            <option value="">All Barangays</option>
                            <?php foreach ($barangays as $barangay): ?>
                                <option value="<?= $barangay['id'] ?>" 
                                        <?= ($_POST['barangay_id'] ?? '') == $barangay['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($barangay['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Leave empty for main admin or to assign to all barangays</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="form-text text-muted">Minimum 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="users.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    document.getElementById('password').addEventListener('input', function() {
        const confirmPassword = document.getElementById('confirm_password');
        if (confirmPassword.value) {
            if (this.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
    });
});
</script> 