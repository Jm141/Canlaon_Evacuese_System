<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Households</h2>
    <a href="households.php?action=add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Household
    </a>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="households.php" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= htmlspecialchars($filters['search']) ?>" 
                       placeholder="Search by household head, address, or control number">
            </div>
            <?php if (isMainAdmin() && !empty($barangays)): ?>
            <div class="col-md-2">
                <label for="barangay_id" class="form-label">Barangay</label>
                <select class="form-select" id="barangay_id" name="barangay_id">
                    <option value="">All Barangays</option>
                    <?php foreach ($barangays as $barangay): ?>
                        <option value="<?= $barangay['id'] ?>" <?= ($_GET['barangay_id'] ?? '') == $barangay['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($barangay['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label for="evacuation_center" class="form-label">Evacuation Center</label>
                <select class="form-select" id="evacuation_center" name="evacuation_center">
                    <option value="">All Centers</option>
                    <?php foreach ($evacuationCenters as $center): ?>
                        <option value="<?= htmlspecialchars($center) ?>" 
                                <?= $filters['evacuation_center'] === $center ? 'selected' : '' ?>>
                            <?= htmlspecialchars($center) ?>
                        </option>
                    <?php endforeach; ?>
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
                <a href="households.php" class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Households Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($households)): ?>
            <div class="text-center py-4">
                <i class="fas fa-home fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No households found</h5>
                <p class="text-muted">Try adjusting your search criteria or add a new household.</p>
                <a href="households.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Household
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Control Number</th>
                            <th>Household Head</th>
                            <th>Address</th>
                            <th>Barangay</th>
                            <th>Evacuation Center</th>
                            <th>Phone</th>
                            <th>Members</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($households as $household): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($household['control_number']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($household['household_head']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($household['address']) ?></td>
                                <td><?= htmlspecialchars($household['barangay_name']) ?></td>
                                <td>
                                    <?php if ($household['assigned_evacuation_center']): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($household['assigned_evacuation_center']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($household['phone_number']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= $household['member_count'] ?? 0 ?> members</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="households.php?action=view&id=<?= $household['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="households.php?action=edit&id=<?= $household['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                data-id="<?= $household['id'] ?>" 
                                                data-name="<?= htmlspecialchars($household['household_head']) ?>"
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
                <nav aria-label="Households pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($filters['search']) ?>&evacuation_center=<?= urlencode($filters['evacuation_center']) ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&evacuation_center=<?= urlencode($filters['evacuation_center']) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($filters['search']) ?>&evacuation_center=<?= urlencode($filters['evacuation_center']) ?>">
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
                <p>Are you sure you want to delete the household "<strong id="deleteHouseholdName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone. All residents in this household will also be deleted.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="households.php?ajax=delete" style="display: inline;">
                    <input type="hidden" name="household_id" id="deleteHouseholdId">
                    <button type="submit" class="btn btn-danger">Delete</button>
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
            
            document.getElementById('deleteHouseholdId').value = id;
            document.getElementById('deleteHouseholdName').textContent = name;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script> 