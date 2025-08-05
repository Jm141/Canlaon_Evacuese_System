<?php require_once 'views/layouts/main.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Add Household</h2>
    <a href="households.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Households
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="households.php?action=add">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Basic Information</h5>
                    
                    <div class="mb-3">
                        <label for="household_head" class="form-label">Household Head *</label>
                        <input type="text" class="form-control" id="household_head" name="household_head" 
                               value="<?= htmlspecialchars($_POST['household_head'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Complete Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="barangay_id" class="form-label">Barangay *</label>
                        <select class="form-select" id="barangay_id" name="barangay_id" required>
                            <option value="">Select Barangay</option>
                            <?php foreach ($barangays as $barangay): ?>
                                <option value="<?= $barangay['id'] ?>" 
                                        <?= ($_POST['barangay_id'] ?? $userBarangayId) == $barangay['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($barangay['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                               value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Evacuation Information</h5>
                    
                    <div class="mb-3">
                        <label for="collection_point" class="form-label">Collection Point</label>
                        <input type="text" class="form-control" id="collection_point" name="collection_point" 
                               value="<?= htmlspecialchars($_POST['collection_point'] ?? '') ?>" 
                               placeholder="Designated pickup location">
                    </div>
                    
                    <div class="mb-3">
                        <label for="evacuation_vehicle" class="form-label">Evacuation Vehicle</label>
                        <input type="text" class="form-control" id="evacuation_vehicle" name="evacuation_vehicle" 
                               value="<?= htmlspecialchars($_POST['evacuation_vehicle'] ?? '') ?>" 
                               placeholder="Vehicle type or number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="vehicle_driver" class="form-label">Vehicle Driver</label>
                        <input type="text" class="form-control" id="vehicle_driver" name="vehicle_driver" 
                               value="<?= htmlspecialchars($_POST['vehicle_driver'] ?? '') ?>" 
                               placeholder="Driver's name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="assigned_evacuation_center" class="form-label">Assigned Evacuation Center</label>
                        <input type="text" class="form-control" id="assigned_evacuation_center" name="assigned_evacuation_center" 
                               value="<?= htmlspecialchars($_POST['assigned_evacuation_center'] ?? '') ?>" 
                               placeholder="Evacuation center name">
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="households.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Household
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate control number when form is submitted
    document.querySelector('form').addEventListener('submit', function() {
        const barangaySelect = document.getElementById('barangay_id');
        const selectedOption = barangaySelect.options[barangaySelect.selectedIndex];
        
        if (selectedOption.value) {
            // Generate control number based on barangay code and timestamp
            const barangayCode = selectedOption.text.split(' ')[0]; // Get first word as code
            const timestamp = Date.now().toString().slice(-6); // Last 6 digits
            const controlNumber = barangayCode + '-' + timestamp;
            
            // You can add a hidden field for control number if needed
            // For now, it will be generated on the server side
        }
    });
});
</script> 