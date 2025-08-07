<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Edit Profile</h2>
    <a href="profile.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="profile.php?action=edit">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Basic Information</h5>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" 
                               value="<?= htmlspecialchars($profileData['username']) ?>" readonly>
                        <small class="form-text text-muted">Username cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? $profileData['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($_POST['full_name'] ?? $profileData['full_name']) ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Account Information</h5>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" 
                               value="<?= ucwords(str_replace('_', ' ', $profileData['role'])) ?>" readonly>
                        <small class="form-text text-muted">Role cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="barangay" class="form-label">Assigned Barangay</label>
                        <input type="text" class="form-control" id="barangay" 
                               value="<?= ($profileData['barangay_name'] ?? null) ? htmlspecialchars($profileData['barangay_name']) : 'All Barangays' ?>" readonly>
                        <small class="form-text text-muted">Barangay assignment cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status" 
                               value="<?= $profileData['is_active'] ? 'Active' : 'Inactive' ?>" readonly>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="profile.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-info-circle"></i> Profile Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Created:</strong> <?= date('F j, Y g:i A', strtotime($profileData['created_at'])) ?></p>
                <p><strong>Last Updated:</strong> <?= date('F j, Y g:i A', strtotime($profileData['updated_at'])) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Created By:</strong> <?= htmlspecialchars($profileData['created_by_name'] ?? 'System') ?></p>
            </div>
        </div>
    </div>
</div> 