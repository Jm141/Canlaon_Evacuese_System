<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus"></i> Add Evacuation Center
                </h5>
                <a href="evacuation-centers.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Evacuation Centers
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="evacuationCenterForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-building"></i> Basic Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Center Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required
                                   placeholder="e.g., Elementary School, Barangay Hall">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="barangay_id" class="form-label">Barangay *</label>
                            <select class="form-select" id="barangay_id" name="barangay_id" required>
                                <option value="">Select Barangay</option>
                                <?php foreach ($barangays as $barangay): ?>
                                    <option value="<?= $barangay['id'] ?>" 
                                            <?= ($_POST['barangay_id'] ?? '') == $barangay['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($barangay['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Complete Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required
                                      placeholder="Enter complete address including street, barangay, city/municipality"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Capacity Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-users"></i> Capacity Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Maximum Capacity *</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   value="<?= htmlspecialchars($_POST['capacity'] ?? '') ?>" required min="1"
                                   placeholder="Number of people this center can accommodate">
                            <small class="text-muted">This is the maximum number of people this evacuation center can safely accommodate.</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Capacity Guidelines</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><small>• Elementary School: 200-500 people</small></li>
                                        <li><small>• High School: 300-800 people</small></li>
                                        <li><small>• Barangay Hall: 50-200 people</small></li>
                                        <li><small>• Gymnasium: 500-1000 people</small></li>
                                        <li><small>• Church: 100-300 people</small></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-phone"></i> Contact Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                   value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>"
                                   placeholder="Name of person in charge">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                   value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>"
                                   placeholder="09XX XXX XXXX">
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle"></i> Additional Information
                            </h6>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       <?= ($_POST['is_active'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Center
                                </label>
                                <small class="text-muted d-block">Check if this evacuation center is currently available for use.</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="evacuation-centers.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Evacuation Center
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    document.getElementById('evacuationCenterForm').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Validate capacity
        const capacity = document.getElementById('capacity').value;
        if (capacity && (parseInt(capacity) <= 0)) {
            document.getElementById('capacity').classList.add('is-invalid');
            alert('Capacity must be greater than 0.');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields correctly.');
        }
    });
    
    // Auto-format phone numbers
    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = value.match(/(\d{0,4})(\d{0,3})(\d{0,3})(\d{0,4})/);
            input.value = !value[2] ? value[1] : !value[3] ? value[1] + ' ' + value[2] : 
                         !value[4] ? value[1] + ' ' + value[2] + ' ' + value[3] : 
                         value[1] + ' ' + value[2] + ' ' + value[3] + ' ' + value[4];
        }
    }
    
    document.getElementById('contact_number').addEventListener('input', function() {
        formatPhoneNumber(this);
    });
    
    // Capacity suggestions based on center name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value.toLowerCase();
        const capacityField = document.getElementById('capacity');
        
        if (name.includes('elementary') || name.includes('primary')) {
            capacityField.placeholder = 'Suggested: 200-500 people';
        } else if (name.includes('high school') || name.includes('secondary')) {
            capacityField.placeholder = 'Suggested: 300-800 people';
        } else if (name.includes('barangay') || name.includes('hall')) {
            capacityField.placeholder = 'Suggested: 50-200 people';
        } else if (name.includes('gym') || name.includes('gymnasium')) {
            capacityField.placeholder = 'Suggested: 500-1000 people';
        } else if (name.includes('church') || name.includes('chapel')) {
            capacityField.placeholder = 'Suggested: 100-300 people';
        } else {
            capacityField.placeholder = 'Number of people this center can accommodate';
        }
    });
});
</script>

<style>
.form-check-input:checked {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
}

.is-invalid {
    border-color: var(--danger-color) !important;
}

.is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25) !important;
}
</style>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 