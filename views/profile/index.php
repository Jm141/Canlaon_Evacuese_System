<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">My Profile</h2>
    <div class="btn-group">
        <a href="profile.php?action=edit" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
        <a href="profile.php?action=change_password" class="btn btn-info">
            <i class="fas fa-key"></i> Change Password
        </a>
    </div>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> Profile Information
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($profileData['full_name'], 0, 1)) ?>
                    </div>
                    <h4><?= htmlspecialchars($profileData['full_name']) ?></h4>
                    <span class="badge bg-<?= $profileData['role'] === 'main_admin' ? 'danger' : ($profileData['role'] === 'admin' ? 'warning' : 'info') ?>">
                        <?= ucwords(str_replace('_', ' ', $profileData['role'])) ?>
                    </span>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Username:</strong></div>
                    <div class="col-sm-8"><?= htmlspecialchars($profileData['username']) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Email:</strong></div>
                    <div class="col-sm-8">
                        <a href="mailto:<?= htmlspecialchars($profileData['email']) ?>">
                            <?= htmlspecialchars($profileData['email']) ?>
                        </a>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Full Name:</strong></div>
                    <div class="col-sm-8"><?= htmlspecialchars($profileData['full_name']) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Barangay:</strong></div>
                    <div class="col-sm-8">
                        <?= ($profileData['barangay_name'] ?? null) ? htmlspecialchars($profileData['barangay_name']) : '<span class="text-muted">All Barangays</span>' ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Status:</strong></div>
                    <div class="col-sm-8">
                        <?php if ($profileData['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Created:</strong></div>
                    <div class="col-sm-8"><?= date('F j, Y g:i A', strtotime($profileData['created_at'])) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Last Updated:</strong></div>
                    <div class="col-sm-8"><?= date('F j, Y g:i A', strtotime($profileData['updated_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history"></i> Recent Activity
                </h5>
                <a href="profile.php?action=activity_logs" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($activityLogs)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No recent activity</h6>
                        <p class="text-muted">Your activity will appear here as you use the system.</p>
                    </div>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($activityLogs as $log): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($log['action']) ?></strong>
                                        <small class="text-muted"><?= date('M j, g:i A', strtotime($log['created_at'])) ?></small>
                                    </div>
                                    <?php if ($log['table_name']): ?>
                                        <small class="text-muted">
                                            <?= ucfirst($log['table_name']) ?>
                                            <?php if ($log['record_id']): ?>
                                                #<?= $log['record_id'] ?>
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar"></i> Quick Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stats-card">
                            <div class="stats-number"><?= count($activityLogs) ?></div>
                            <div class="stats-label">Recent Activities</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stats-card">
                            <div class="stats-number">
                                <?= date('Y-m-d') === date('Y-m-d', strtotime($profileData['updated_at'])) ? 'Today' : date('M j', strtotime($profileData['updated_at'])) ?>
                            </div>
                            <div class="stats-label">Last Updated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--border-color);
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--accent-blue);
    border: 2px solid white;
    box-shadow: 0 0 0 2px var(--accent-blue);
}

.timeline-content {
    background: var(--bg-light);
    padding: 10px 15px;
    border-radius: 0.5rem;
    border-left: 3px solid var(--accent-blue);
}
</style> 