<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Evacuation Center Management</h2>
    <a href="evacuation-centers.php?action=add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Evacuation Center
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?= number_format($totalCenters) ?></h4>
                        <small>Total Centers</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0" id="totalCapacity">-</h4>
                        <small>Total Capacity</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0" id="totalOccupancy">-</h4>
                        <small>Total Occupancy</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0" id="totalAvailable">-</h4>
                        <small>Available Space</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                       placeholder="Search by name or address...">
            </div>
            <div class="col-md-4">
                <label for="barangay_id" class="form-label">Barangay</label>
                <select class="form-select" id="barangay_id" name="barangay_id">
                    <option value="">All Barangays</option>
                    <?php foreach ($barangays as $barangay): ?>
                        <option value="<?= $barangay['id'] ?>" 
                                <?= ($filters['barangay_id'] ?? '') == $barangay['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($barangay['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="evacuation-centers.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Auto Assignment Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-magic"></i> Auto Assignment
        </h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Automatically assign unassigned households to available evacuation centers based on capacity.</p>
        <div class="row">
            <div class="col-md-4">
                <select class="form-select" id="autoAssignBarangay">
                    <option value="">Select Barangay</option>
                    <?php foreach ($barangays as $barangay): ?>
                        <option value="<?= $barangay['id'] ?>">
                            <?= htmlspecialchars($barangay['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success" id="autoAssignBtn">
                    <i class="fas fa-magic"></i> Auto Assign Households
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Evacuation Centers Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Evacuation Centers</h5>
    </div>
    <div class="card-body">
        <?php if (empty($evacuationCenters)): ?>
            <div class="text-center py-4">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No evacuation centers found</h5>
                <p class="text-muted">Add your first evacuation center to get started.</p>
                <a href="evacuation-centers.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Evacuation Center
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Barangay</th>
                            <th>Address</th>
                            <th>Capacity</th>
                            <th>Occupancy</th>
                            <th>Available</th>
                            <th>Utilization</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($evacuationCenters as $center): ?>
                            <?php 
                            $utilization = $center['capacity'] > 0 ? ($center['current_occupancy'] / $center['capacity']) * 100 : 0;
                            $utilizationClass = $utilization >= 90 ? 'danger' : ($utilization >= 75 ? 'warning' : 'success');
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($center['name']) ?></strong>
                                    <?php if ($center['contact_person']): ?>
                                        <br><small class="text-muted">Contact: <?= htmlspecialchars($center['contact_person']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($center['barangay_name']) ?></td>
                                <td><?= htmlspecialchars($center['address']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= number_format($center['capacity']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?= number_format($center['current_occupancy']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $center['available_capacity'] > 0 ? 'success' : 'danger' ?>">
                                        <?= number_format($center['available_capacity']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-<?= $utilizationClass ?>" 
                                             style="width: <?= $utilization ?>%">
                                            <?= number_format($utilization, 1) ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="evacuation-centers.php?action=view&id=<?= $center['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="evacuation-centers.php?action=edit&id=<?= $center['id'] ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-center" 
                                                data-id="<?= $center['id'] ?>" 
                                                data-name="<?= htmlspecialchars($center['name']) ?>" 
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
                <nav aria-label="Evacuation centers pagination">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($filters['search'] ?? '') ?>&barangay_id=<?= $filters['barangay_id'] ?? '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate and display statistics
    calculateStatistics();
    
    // Auto assignment functionality
    document.getElementById('autoAssignBtn').addEventListener('click', function() {
        const barangayId = document.getElementById('autoAssignBarangay').value;
        if (!barangayId) {
            alert('Please select a barangay for auto assignment.');
            return;
        }
        
        if (confirm('This will automatically assign all unassigned households in the selected barangay to available evacuation centers. Continue?')) {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            fetch('evacuation-centers.php?ajax=auto_assign', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'barangay_id=' + barangayId
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
                alert('Error: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-magic"></i> Auto Assign Households';
            });
        }
    });
    
    // Delete evacuation center
    document.querySelectorAll('.delete-center').forEach(button => {
        button.addEventListener('click', function() {
            const centerId = this.dataset.id;
            const centerName = this.dataset.name;
            
            if (confirm(`Are you sure you want to delete the evacuation center "${centerName}"?`)) {
                fetch('evacuation-centers.php?ajax=delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'center_id=' + centerId
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
                    alert('Error: ' + error.message);
                });
            }
        });
    });
});

function calculateStatistics() {
    const centers = <?= json_encode($evacuationCenters) ?>;
    let totalCapacity = 0;
    let totalOccupancy = 0;
    
    centers.forEach(center => {
        totalCapacity += parseInt(center.capacity);
        totalOccupancy += parseInt(center.current_occupancy);
    });
    
    const totalAvailable = totalCapacity - totalOccupancy;
    
    document.getElementById('totalCapacity').textContent = totalCapacity.toLocaleString();
    document.getElementById('totalOccupancy').textContent = totalOccupancy.toLocaleString();
    document.getElementById('totalAvailable').textContent = totalAvailable.toLocaleString();
}
</script>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 