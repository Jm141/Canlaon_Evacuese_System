<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Edit Household</h2>
    <a href="households.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Households
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="households.php?action=edit&id=<?= $household['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Basic Information</h5>
                    
                    <div class="mb-3">
                        <label for="household_head" class="form-label">Household Head *</label>
                        <input type="text" class="form-control" id="household_head" name="household_head" 
                               value="<?= htmlspecialchars($_POST['household_head'] ?? $household['household_head']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Complete Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($_POST['address'] ?? $household['address']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="barangay_id" class="form-label">Barangay *</label>
                        <select class="form-select" id="barangay_id" name="barangay_id" required>
                            <option value="">Select Barangay</option>
                            <?php foreach ($barangays as $barangay): ?>
                                <option value="<?= $barangay['id'] ?>" 
                                        <?= ($_POST['barangay_id'] ?? $household['barangay_id']) == $barangay['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($barangay['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                               value="<?= htmlspecialchars($_POST['phone_number'] ?? $household['phone_number']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="control_number" class="form-label">Control Number</label>
                        <input type="text" class="form-control" id="control_number" 
                               value="<?= htmlspecialchars($household['control_number']) ?>" readonly>
                        <small class="form-text text-muted">Control number is automatically generated and cannot be changed.</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Evacuation Information</h5>
                    
                    <div class="mb-3">
                        <label for="collection_point" class="form-label">Collection Point</label>
                        <input type="text" class="form-control" id="collection_point" name="collection_point" 
                               value="<?= htmlspecialchars($_POST['collection_point'] ?? $household['collection_point']) ?>" 
                               placeholder="Designated pickup location">
                    </div>
                    
                    <div class="mb-3">
                        <label for="evacuation_vehicle" class="form-label">Evacuation Vehicle</label>
                        <input type="text" class="form-control" id="evacuation_vehicle" name="evacuation_vehicle" 
                               value="<?= htmlspecialchars($_POST['evacuation_vehicle'] ?? $household['evacuation_vehicle']) ?>" 
                               placeholder="Vehicle type or number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="vehicle_driver" class="form-label">Vehicle Driver</label>
                        <input type="text" class="form-control" id="vehicle_driver" name="vehicle_driver" 
                               value="<?= htmlspecialchars($_POST['vehicle_driver'] ?? $household['vehicle_driver']) ?>" 
                               placeholder="Driver's name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="assigned_evacuation_center" class="form-label">Assigned Evacuation Center</label>
                        <input type="text" class="form-control" id="assigned_evacuation_center" name="assigned_evacuation_center" 
                               value="<?= htmlspecialchars($_POST['assigned_evacuation_center'] ?? $household['assigned_evacuation_center']) ?>" 
                               placeholder="Evacuation center name">
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="households.php" class="btn btn-secondary">Cancel</a>
                <a href="households.php?action=view&id=<?= $household['id'] ?>" class="btn btn-info">
                    <i class="fas fa-eye"></i> View
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Household
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Household Information</h5>
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