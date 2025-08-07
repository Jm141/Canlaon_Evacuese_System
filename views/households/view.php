<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Household Details</h2>
    <div class="btn-group">
        <a href="households.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Households
        </a>
        <a href="households.php?action=edit&id=<?= $household['id'] ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
    </div>
</div>

<div class="row">
    <!-- Household Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-home"></i> Household Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Control Number:</strong></div>
                    <div class="col-sm-8">
                        <span class="badge bg-primary fs-6"><?= htmlspecialchars($household['control_number']) ?></span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Household Head:</strong></div>
                    <div class="col-sm-8"><?= htmlspecialchars($household['household_head']) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Address:</strong></div>
                    <div class="col-sm-8"><?= nl2br(htmlspecialchars($household['address'])) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Barangay:</strong></div>
                    <div class="col-sm-8"><?= htmlspecialchars($household['barangay_name']) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Phone Number:</strong></div>
                    <div class="col-sm-8">
                        <a href="tel:<?= htmlspecialchars($household['phone_number']) ?>">
                            <?= htmlspecialchars($household['phone_number']) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Evacuation Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Evacuation Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Collection Point:</strong></div>
                    <div class="col-sm-8">
                        <?= $household['collection_point'] ? htmlspecialchars($household['collection_point']) : '<span class="text-muted">Not specified</span>' ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Evacuation Vehicle:</strong></div>
                    <div class="col-sm-8">
                        <?= $household['evacuation_vehicle'] ? htmlspecialchars($household['evacuation_vehicle']) : '<span class="text-muted">Not assigned</span>' ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Vehicle Driver:</strong></div>
                    <div class="col-sm-8">
                        <?= $household['vehicle_driver'] ? htmlspecialchars($household['vehicle_driver']) : '<span class="text-muted">Not assigned</span>' ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Evacuation Center:</strong></div>
                    <div class="col-sm-8">
                        <?php if ($household['assigned_evacuation_center']): ?>
                            <span class="badge bg-info"><?= htmlspecialchars($household['assigned_evacuation_center']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">Not assigned</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Household Members -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-users"></i> Household Members (<?= count($householdMembers) ?>)
        </h5>
        <a href="residents.php?action=add&household_id=<?= $household['id'] ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Member
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($householdMembers)): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No household members found</h6>
                <p class="text-muted">Add residents to this household to get started.</p>
                <a href="residents.php?action=add&household_id=<?= $household['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Member
                </a>
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
                            <th>Contact</th>
                            <th>Special Needs</th>
                            <th>ID Card</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($householdMembers as $member): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></strong>
                                    <?php if ($member['is_household_head']): ?>
                                        <span class="badge bg-success ms-1">Head</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $member['age'] ? $member['age'] : '<span class="text-muted">-</span>' ?></td>
                                <td><?= ucfirst($member['gender']) ?></td>
                                <td><?= ucfirst($member['civil_status']) ?></td>
                                <td>
                                    <?php if ($member['contact_number']): ?>
                                        <a href="tel:<?= htmlspecialchars($member['contact_number']) ?>">
                                            <?= htmlspecialchars($member['contact_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($member['has_special_needs']): ?>
                                        <span class="badge bg-warning">Yes</span>
                                    <?php else: ?>
                                        <span class="text-muted">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $hasActiveCard = false;
                                    foreach ($idCards as $card) {
                                        if ($card['resident_id'] == $member['id'] && $card['status'] == 'active') {
                                            $hasActiveCard = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($hasActiveCard): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="residents.php?action=view&id=<?= $member['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="residents.php?action=edit&id=<?= $member['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (!$hasActiveCard): ?>
                                            <a href="id-cards.php?action=generate&resident_id=<?= $member['id'] ?>" 
                                               class="btn btn-sm btn-outline-info" title="Generate ID Card">
                                                <i class="fas fa-id-card"></i>
                                            </a>
                                        <?php endif; ?>
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

<!-- ID Cards -->
<?php if (!empty($idCards)): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-id-card"></i> ID Cards (<?= count($idCards) ?>)
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Card Number</th>
                        <th>Resident</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($idCards as $card): ?>
                        <tr>
                            <td>
                                <span class="badge bg-primary"><?= htmlspecialchars($card['card_number']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($card['resident_name']) ?></td>
                            <td><?= date('M j, Y', strtotime($card['issue_date'])) ?></td>
                            <td>
                                <?php if ($card['expiry_date']): ?>
                                    <?= date('M j, Y', strtotime($card['expiry_date'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($card['status'] == 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php elseif ($card['status'] == 'expired'): ?>
                                    <span class="badge bg-danger">Expired</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= ucfirst($card['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="id-cards.php?action=view&id=<?= $card['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- System Information -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-info-circle"></i> System Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Created:</strong> <?= date('F j, Y g:i A', strtotime($household['created_at'])) ?></p>
                <p><strong>Last Updated:</strong> <?= date('F j, Y g:i A', strtotime($household['updated_at'])) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Created By:</strong> <?= htmlspecialchars($household['created_by_name'] ?? 'System') ?></p>
            </div>
        </div>
    </div>
</div> 