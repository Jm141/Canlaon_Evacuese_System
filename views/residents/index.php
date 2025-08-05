<?php
ob_start();
?>

<!-- Header with Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Residents</h1>
        <p class="text-muted">Manage resident information and records</p>
    </div>
    <div>
        <a href="residents.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Resident
        </a>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="residents.php" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= htmlspecialchars($filters['search']) ?>" 
                       placeholder="Name, household head, or control number">
            </div>
            <div class="col-md-2">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" id="gender" name="gender">
                    <option value="">All</option>
                    <option value="male" <?= $filters['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $filters['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="age_min" class="form-label">Min Age</label>
                <input type="number" class="form-control" id="age_min" name="age_min" 
                       value="<?= htmlspecialchars($filters['age_min']) ?>" min="0" max="120">
            </div>
            <div class="col-md-2">
                <label for="age_max" class="form-label">Max Age</label>
                <input type="number" class="form-control" id="age_max" name="age_max" 
                       value="<?= htmlspecialchars($filters['age_max']) ?>" min="0" max="120">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-grid gap-2 w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="residents.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Results Summary -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="text-muted">
            Showing <?= $pagination['offset'] + 1 ?> to 
            <?= min($pagination['offset'] + $pagination['items_per_page'], $pagination['total_items']) ?> 
            of <?= number_format($pagination['total_items']) ?> residents
        </span>
    </div>
    <div>
        <a href="residents.php?action=add" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Add New
        </a>
    </div>
</div>

<!-- Residents Table -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($residents)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Household</th>
                            <th>Contact</th>
                            <th>Special Needs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($residents as $resident): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-primary rounded-circle">
                                                <?= strtoupper(substr($resident['first_name'], 0, 1) . substr($resident['last_name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?></strong>
                                            <?php if ($resident['is_household_head']): ?>
                                                <span class="badge bg-primary ms-1">Head</span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($resident['control_number']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $birthDate = new DateTime($resident['date_of_birth']);
                                    $today = new DateTime();
                                    $age = $today->diff($birthDate)->y;
                                    echo $age;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $resident['gender'] === 'male' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($resident['gender']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($resident['household_head']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($resident['address']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($resident['contact_number'])): ?>
                                        <i class="fas fa-phone text-muted"></i> <?= htmlspecialchars($resident['contact_number']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not provided</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($resident['has_special_needs']): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-wheelchair"></i> Special Needs
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="residents.php?action=view&id=<?= $resident['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="residents.php?action=edit&id=<?= $resident['id'] ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteResident(<?= $resident['id'] ?>)" title="Delete">
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
                <nav aria-label="Residents pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['has_previous']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['previous_page'] ?>&search=<?= urlencode($filters['search']) ?>&gender=<?= urlencode($filters['gender']) ?>&age_min=<?= urlencode($filters['age_min']) ?>&age_max=<?= urlencode($filters['age_max']) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&gender=<?= urlencode($filters['gender']) ?>&age_min=<?= urlencode($filters['age_min']) ?>&age_max=<?= urlencode($filters['age_max']) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['next_page'] ?>&search=<?= urlencode($filters['search']) ?>&gender=<?= urlencode($filters['gender']) ?>&age_min=<?= urlencode($filters['age_min']) ?>&age_max=<?= urlencode($filters['age_max']) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No residents found</h5>
                <p class="text-muted">
                    <?php if (!empty($filters['search']) || !empty($filters['gender']) || !empty($filters['age_min']) || !empty($filters['age_max'])): ?>
                        Try adjusting your search criteria or 
                        <a href="residents.php">clear all filters</a>
                    <?php else: ?>
                        Get started by adding your first resident
                    <?php endif; ?>
                </p>
                <a href="residents.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Resident
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteResident(residentId) {
    if (confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
        fetch('residents.php?ajax=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'resident_id=' + residentId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the resident.');
        });
    }
}
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}
</style>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 