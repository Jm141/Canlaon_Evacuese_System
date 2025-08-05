<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Barangay Details</h2>
    <div>
        <a href="barangays.php?action=edit&id=<?= $barangay['id'] ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="barangays.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Barangay Information -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt"></i> Barangay Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Barangay Name</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($barangay['name']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Barangay Number</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-secondary"><?= htmlspecialchars($barangay['number']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-<?= $barangay['is_active'] ? 'success' : 'danger' ?>">
                                <?= $barangay['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Created Date</label>
                        <p class="form-control-plaintext"><?= date('F j, Y g:i A', strtotime($barangay['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar"></i> Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-primary mb-1"><?= number_format($stats['total_households'] ?? 0) ?></h4>
                            <small class="text-muted">Households</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-1"><?= number_format($stats['total_residents'] ?? 0) ?></h4>
                        <small class="text-muted">Residents</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-warning mb-1"><?= number_format($stats['evacuation_centers'] ?? 0) ?></h4>
                            <small class="text-muted">Evacuation Centers</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info mb-1"><?= number_format($stats['assigned_users'] ?? 0) ?></h4>
                        <small class="text-muted">Assigned Users</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-tools"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="residents.php?barangay_id=<?= $barangay['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-users"></i> View Residents
                    </a>
                    <a href="households.php?barangay_id=<?= $barangay['id'] ?>" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-home"></i> View Households
                    </a>
                    <a href="evacuation-centers.php?barangay_id=<?= $barangay['id'] ?>" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-building"></i> View Evacuation Centers
                    </a>
                    <a href="users.php?barangay_id=<?= $barangay['id'] ?>" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-user-tie"></i> View Assigned Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history"></i> Recent Activity
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivity)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No recent activity</h5>
                        <p class="text-muted">No recent changes have been made to this barangay.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>User</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?= $activity['type'] === 'create' ? 'success' : ($activity['type'] === 'update' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($activity['type']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($activity['description']) ?></td>
                                        <td><?= htmlspecialchars($activity['user_name']) ?></td>
                                        <td><?= date('M j, Y g:i A', strtotime($activity['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Users -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-tie"></i> Assigned Users
                    <span class="badge bg-primary ms-2"><?= count($assignedUsers) ?></span>
                </h5>
                <a href="users.php?barangay_id=<?= $barangay['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($assignedUsers)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No assigned users</h5>
                        <p class="text-muted">No users are currently assigned to this barangay.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedUsers as $user): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $user['role'] === 'main_admin' ? 'danger' : ($user['role'] === 'admin' ? 'warning' : 'info') ?>">
                                                <?= ucwords(str_replace('_', ' ', $user['role'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="users.php?action=view&id=<?= $user['id'] ?>" 
                                                   class="btn btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="users.php?action=edit&id=<?= $user['id'] ?>" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Evacuation Centers -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building"></i> Evacuation Centers
                    <span class="badge bg-warning ms-2"><?= count($evacuationCenters) ?></span>
                </h5>
                <a href="evacuation-centers.php?barangay_id=<?= $barangay['id'] ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-eye"></i> View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($evacuationCenters)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No evacuation centers</h5>
                        <p class="text-muted">No evacuation centers are currently assigned to this barangay.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($evacuationCenters as $center): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= htmlspecialchars($center['name']) ?></h6>
                                        <p class="card-text text-muted small"><?= htmlspecialchars($center['address']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?= $center['is_active'] ? 'success' : 'danger' ?>">
                                                <?= $center['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                            <small class="text-muted">
                                                Capacity: <?= number_format($center['capacity']) ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm w-100">
                                            <a href="evacuation-centers.php?action=view&id=<?= $center['id'] ?>" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="evacuation-centers.php?action=edit&id=<?= $center['id'] ?>" 
                                               class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Add any JavaScript functionality here if needed
</script>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 