<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Evacuation Center Details</h2>
    <div>
        <a href="evacuation-centers.php?action=edit&id=<?= $center['id'] ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="evacuation-centers.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Evacuation Center Information -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building"></i> Center Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Center Name</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($center['name']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Barangay</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($center['barangay_name']) ?></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($center['address']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Contact Person</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($center['contact_person'] ?? 'Not specified') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Contact Number</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($center['contact_number'] ?? 'Not specified') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Capacity</label>
                        <p class="form-control-plaintext"><?= number_format($center['capacity']) ?> persons</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Current Occupancy</label>
                        <p class="form-control-plaintext"><?= number_format($center['current_occupancy']) ?> persons</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Available Space</label>
                        <p class="form-control-plaintext">
                            <?= number_format($center['capacity'] - $center['current_occupancy']) ?> persons
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Occupancy Rate</label>
                        <p class="form-control-plaintext">
                            <?= $center['capacity'] > 0 ? round(($center['current_occupancy'] / $center['capacity']) * 100, 1) : 0 ?>%
                        </p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($center['description'] ?? 'No description available') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Capacity Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie"></i> Capacity Status
                </h6>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 25px;">
                    <?php 
                    $occupancyRate = $center['capacity'] > 0 ? ($center['current_occupancy'] / $center['capacity']) * 100 : 0;
                    $progressClass = $occupancyRate >= 90 ? 'bg-danger' : ($occupancyRate >= 70 ? 'bg-warning' : 'bg-success');
                    ?>
                    <div class="progress-bar <?= $progressClass ?>" 
                         style="width: <?= min($occupancyRate, 100) ?>%">
                        <?= round($occupancyRate, 1) ?>%
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-success mb-0"><?= number_format($center['capacity'] - $center['current_occupancy']) ?></h4>
                            <small class="text-muted">Available</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-primary mb-0"><?= number_format($center['current_occupancy']) ?></h4>
                        <small class="text-muted">Occupied</small>
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
                    <a href="evacuation-centers.php?action=edit&id=<?= $center['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit Center
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="exportHouseholds()">
                        <i class="fas fa-download"></i> Export Households
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="printDetails()">
                        <i class="fas fa-print"></i> Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Households -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-home"></i> Assigned Households 
            <span class="badge bg-primary ms-2"><?= count($center['assigned_households'] ?? []) ?></span>
        </h5>
        <div>
            <button type="button" class="btn btn-success btn-sm" onclick="autoAssignHouseholds()">
                <i class="fas fa-magic"></i> Auto Assign
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($center['assigned_households'])): ?>
            <div class="text-center py-4">
                <i class="fas fa-home fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No households assigned</h5>
                <p class="text-muted">No households have been assigned to this evacuation center yet.</p>
                <button type="button" class="btn btn-primary" onclick="autoAssignHouseholds()">
                    <i class="fas fa-magic"></i> Auto Assign Households
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Control Number</th>
                            <th>Household Head</th>
                            <th>Address</th>
                            <th>Members</th>
                            <th>Collection Point</th>
                            <th>Evacuation Vehicle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($center['assigned_households'] as $household): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($household['control_number']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($household['household_head']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($household['address']) ?></td>
                                <td>
                                    <?php
                                    // Get member count from residents table
                                    $residentModel = new Resident();
                                    $memberCount = count($residentModel->getHouseholdMembers($household['id']));
                                    ?>
                                    <span class="badge bg-info"><?= $memberCount ?> members</span>
                                </td>
                                <td><?= htmlspecialchars($household['collection_point'] ?? 'Not specified') ?></td>
                                <td>
                                    <?php if ($household['evacuation_vehicle']): ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($household['evacuation_vehicle']) ?></span>
                                        <?php if ($household['vehicle_driver']): ?>
                                            <br><small class="text-muted">Driver: <?= htmlspecialchars($household['vehicle_driver']) ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">No vehicle</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="households.php?action=view&id=<?= $household['id'] ?>" 
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-warning" 
                                                onclick="reassignHousehold(<?= $household['id'] ?>)" title="Reassign">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
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

<!-- Reassign Household Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reassign Household</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reassignForm">
                    <input type="hidden" id="householdId" name="household_id">
                    <div class="mb-3">
                        <label for="newCenterId" class="form-label">New Evacuation Center</label>
                        <select class="form-select" id="newCenterId" name="center_id" required>
                            <option value="">Select evacuation center...</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmReassign()">Reassign</button>
            </div>
        </div>
    </div>
</div>

<script>
function autoAssignHouseholds() {
    if (confirm('This will automatically assign unassigned households from this barangay to this evacuation center. Continue?')) {
        fetch('evacuation-centers.php?action=autoAssign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'barangay_id=<?= $center['barangay_id'] ?>'
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
            alert('An error occurred while auto-assigning households.');
        });
    }
}

function reassignHousehold(householdId) {
    document.getElementById('householdId').value = householdId;
    
    // Load available evacuation centers
    fetch('evacuation-centers.php?action=search&barangay_id=<?= $center['barangay_id'] ?>')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('newCenterId');
            select.innerHTML = '<option value="">Select evacuation center...</option>';
            
            data.forEach(center => {
                if (center.id != <?= $center['id'] ?>) {
                    const option = document.createElement('option');
                    option.value = center.id;
                    option.textContent = `${center.name} (${center.available_capacity} available)`;
                    select.appendChild(option);
                }
            });
            
            new bootstrap.Modal(document.getElementById('reassignModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading evacuation centers.');
        });
}

function confirmReassign() {
    const formData = new FormData(document.getElementById('reassignForm'));
    
    fetch('evacuation-centers.php?action=reassign', {
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
        alert('An error occurred while reassigning the household.');
    });
}

function exportHouseholds() {
    window.open(`evacuation-centers.php?action=export&id=<?= $center['id'] ?>`, '_blank');
}

function printDetails() {
    window.print();
}
</script>

<style>
@media print {
    .btn, .modal, .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 