<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Resident Details</h2>
    <div>
        <a href="residents.php?action=edit&id=<?= $resident['id'] ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="residents.php?action=add-family-member&household_id=<?= $resident['household_id'] ?>" class="btn btn-success me-2">
            <i class="fas fa-user-plus"></i> Add Family Member
        </a>
        <a href="residents.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Resident Information -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> Personal Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <p class="form-control-plaintext">
                            <?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?>
                            <?php if ($resident['middle_name']): ?>
                                <?= htmlspecialchars($resident['middle_name']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date of Birth</label>
                        <p class="form-control-plaintext">
                            <?= date('F j, Y', strtotime($resident['date_of_birth'])) ?>
                            (<?= date_diff(date_create($resident['date_of_birth']), date_create('today'))->y ?> years old)
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Gender</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-<?= $resident['gender'] === 'male' ? 'primary' : 'pink' ?>">
                                <?= ucfirst($resident['gender']) ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Civil Status</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-secondary"><?= ucfirst($resident['civil_status']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nationality</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['nationality']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Religion</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['religion'] ?: 'Not specified') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Occupation</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['occupation'] ?: 'Not specified') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Educational Attainment</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['educational_attainment'] ?: 'Not specified') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Contact Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-phone"></i> Contact Information
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Contact Number</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($resident['contact_number'] ?: 'Not specified') ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($resident['email'] ?: 'Not specified') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Emergency Contact -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Emergency Contact
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Contact Name</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($resident['emergency_contact_name'] ?: 'Not specified') ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Contact Number</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($resident['emergency_contact_number'] ?: 'Not specified') ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Relationship</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($resident['emergency_contact_relationship'] ?: 'Not specified') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Special Needs -->
        <?php if ($resident['has_special_needs']): ?>
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-wheelchair"></i> Special Needs
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Special Needs:</strong><br>
                    <?= htmlspecialchars($resident['special_needs_description']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Household Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-home"></i> Household Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Control Number</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-secondary"><?= htmlspecialchars($resident['control_number']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Household Head</label>
                        <p class="form-control-plaintext">
                            <?= htmlspecialchars($resident['household_head']) ?>
                            <?php if ($resident['is_household_head']): ?>
                                <span class="badge bg-success ms-2">Head</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['address']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Barangay</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['barangay_name']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['household_phone'] ?: 'Not specified') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Evacuation Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shield-alt"></i> Evacuation Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Assigned Evacuation Center</label>
                        <p class="form-control-plaintext">
                            <?php if ($resident['assigned_evacuation_center']): ?>
                                <span class="badge bg-success"><?= htmlspecialchars($resident['assigned_evacuation_center']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning">Not assigned</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Collection Point</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['collection_point'] ?: 'Not specified') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Evacuation Vehicle</label>
                        <p class="form-control-plaintext">
                            <?php if ($resident['evacuation_vehicle']): ?>
                                <span class="badge bg-info"><?= htmlspecialchars($resident['evacuation_vehicle']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not specified</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vehicle Driver</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($resident['vehicle_driver'] ?: 'Not specified') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Household Members -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users"></i> Household Members 
                    <span class="badge bg-primary ms-2"><?= count($householdMembers) ?></span>
                </h5>
                <a href="residents.php?action=add-family-member&household_id=<?= $resident['household_id'] ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-user-plus"></i> Add Member
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($householdMembers)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No household members</h5>
                        <p class="text-muted">This resident is the only member of this household.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Civil Status</th>
                                    <th>Role</th>
                                    <th>Special Needs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($householdMembers as $member): ?>
                                    <tr class="<?= $member['id'] == $resident['id'] ? 'table-primary' : '' ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></strong>
                                            <?php if ($member['id'] == $resident['id']): ?>
                                                <span class="badge bg-primary ms-2">Current</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $age = date_diff(date_create($member['date_of_birth']), date_create('today'))->y;
                                            echo $age . ' years';
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $member['gender'] === 'male' ? 'primary' : 'pink' ?>">
                                                <?= ucfirst($member['gender']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= ucfirst($member['civil_status']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($member['is_household_head']): ?>
                                                <span class="badge bg-success">Head</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Member</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($member['has_special_needs']): ?>
                                                <span class="badge bg-warning">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="residents.php?action=view&id=<?= $member['id'] ?>" 
                                                   class="btn btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="residents.php?action=edit&id=<?= $member['id'] ?>" 
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

<!-- ID Card Information -->
<?php if ($idCard): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-id-card"></i> ID Card Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">ID Card Number</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-primary"><?= htmlspecialchars($idCard['card_number']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Issue Date</label>
                        <p class="form-control-plaintext"><?= date('F j, Y', strtotime($idCard['issue_date'])) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Expiry Date</label>
                        <p class="form-control-plaintext">
                            <?= date('F j, Y', strtotime($idCard['expiry_date'])) ?>
                            <?php 
                            $daysUntilExpiry = (strtotime($idCard['expiry_date']) - time()) / (60 * 60 * 24);
                            if ($daysUntilExpiry < 0): ?>
                                <span class="badge bg-danger ms-2">Expired</span>
                            <?php elseif ($daysUntilExpiry < 30): ?>
                                <span class="badge bg-warning ms-2">Expires Soon</span>
                            <?php else: ?>
                                <span class="badge bg-success ms-2">Valid</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-<?= $idCard['is_active'] ? 'success' : 'danger' ?>">
                                <?= $idCard['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="id-cards.php?action=view&id=<?= $idCard['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View ID Card
                    </a>
                    <a href="id-cards.php?action=print&id=<?= $idCard['id'] ?>" class="btn btn-success" target="_blank">
                        <i class="fas fa-print"></i> Print ID Card
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-tools"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="residents.php?action=edit&id=<?= $resident['id'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Resident
                    </a>
                    <a href="residents.php?action=add-family-member&household_id=<?= $resident['household_id'] ?>" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Add Family Member
                    </a>
                    <?php if (!$idCard): ?>
                    <a href="id-cards.php?action=generate&resident_id=<?= $resident['id'] ?>" class="btn btn-info">
                        <i class="fas fa-id-card"></i> Generate ID Card
                    </a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteResident(<?= $resident['id'] ?>)">
                        <i class="fas fa-trash"></i> Delete Resident
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteResident(residentId) {
    if (confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('resident_id', residentId);
        
        fetch('residents.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = 'residents.php';
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

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 