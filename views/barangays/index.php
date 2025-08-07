<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Barangay Management</h2>
    <a href="barangays.php?action=add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Barangay
    </a>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="barangays.php" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= htmlspecialchars($filters['search']) ?>" 
                       placeholder="Search by name or code">
            </div>
            <div class="col-md-3">
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
                <a href="barangays.php" class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Barangays Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($barangays)): ?>
            <div class="text-center py-4">
                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No barangays found</h5>
                <p class="text-muted">Try adjusting your search criteria or add a new barangay.</p>
                <a href="barangays.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Barangay
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Residents</th>
                            <th>Households</th>
                            <th>Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barangays as $barangay): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($barangay['code']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($barangay['name']) ?></strong>
                                </td>
                                <td>
                                    <?= $barangay['description'] ? htmlspecialchars($barangay['description']) : '<span class="text-muted">No description</span>' ?>
                                </td>
                                <td>
                                    <?php if ($barangay['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= number_format($barangay['resident_count'] ?? 0) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= number_format($barangay['household_count'] ?? 0) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?= number_format($barangay['user_count'] ?? 0) ?></span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="barangays.php?action=view&id=<?= $barangay['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="barangays.php?action=edit&id=<?= $barangay['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-status" 
                                                data-id="<?= $barangay['id'] ?>" 
                                                data-name="<?= htmlspecialchars($barangay['name']) ?>"
                                                data-status="<?= $barangay['is_active'] ?>"
                                                title="<?= $barangay['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                            <i class="fas fa-<?= $barangay['is_active'] ? 'ban' : 'check' ?>"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                data-id="<?= $barangay['id'] ?>" 
                                                data-name="<?= htmlspecialchars($barangay['name']) ?>"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Barangays pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($filters['search']) ?>&status=<?= urlencode($filters['status']) ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&status=<?= urlencode($filters['status']) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($filters['search']) ?>&status=<?= urlencode($filters['status']) ?>">
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
                <p>Are you sure you want to delete the barangay "<strong id="deleteBarangayName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone. All residents, households, and users in this barangay will be affected.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="barangays.php?ajax=delete" style="display: inline;">
                    <input type="hidden" name="barangay_id" id="deleteBarangayId">
                    <button type="submit" class="btn btn-danger">Delete</button>
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
                <p>Are you sure you want to <span id="toggleStatusAction"></span> the barangay "<strong id="toggleStatusBarangayName"></strong>"?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="barangays.php?ajax=toggle_status" style="display: inline;">
                    <input type="hidden" name="barangay_id" id="toggleStatusBarangayId">
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
            
            document.getElementById('deleteBarangayId').value = id;
            document.getElementById('deleteBarangayName').textContent = name;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
    
    // Toggle status confirmation
    document.querySelectorAll('.btn-toggle-status').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const status = this.dataset.status;
            const action = status === '1' ? 'deactivate' : 'activate';
            
            document.getElementById('toggleStatusBarangayId').value = id;
            document.getElementById('toggleStatusBarangayName').textContent = name;
            document.getElementById('toggleStatusAction').textContent = action;
            
            new bootstrap.Modal(document.getElementById('toggleStatusModal')).show();
        });
    });
});
</script> 