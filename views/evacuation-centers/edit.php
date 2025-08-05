<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building"></i> Edit Evacuation Center
                </h5>
                <a href="evacuation-centers.php?action=view&id=<?= $center['id'] ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Center
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="evacuationCenterForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Center Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($center['name']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="barangay_id" class="form-label">Barangay *</label>
                            <select class="form-select" id="barangay_id" name="barangay_id" required>
                                <option value="">Select Barangay</option>
                                <?php foreach ($barangays as $barangay): ?>
                                    <option value="<?= $barangay['id'] ?>" <?= ($center['barangay_id'] == $barangay['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($barangay['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Complete Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      placeholder="Enter complete address including street, barangay, city/municipality" required><?= htmlspecialchars($center['address']) ?></textarea>
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
                                   value="<?= htmlspecialchars($center['capacity']) ?>" min="1" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="current_occupancy" class="form-label">Current Occupancy</label>
                            <input type="number" class="form-control" id="current_occupancy" name="current_occupancy" 
                                   value="<?= htmlspecialchars($center['current_occupancy'] ?? 0) ?>" min="0">
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
                                   value="<?= htmlspecialchars($center['contact_person'] ?? '') ?>" 
                                   placeholder="Name of contact person">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                   value="<?= htmlspecialchars($center['contact_number'] ?? '') ?>" 
                                   placeholder="09XX XXX XXXX">
                        </div>
                    </div>
                    
                    <!-- Facility Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-tools"></i> Facility Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="facility_type" class="form-label">Facility Type</label>
                            <select class="form-select" id="facility_type" name="facility_type">
                                <option value="">Select Type</option>
                                <option value="school" <?= ($center['facility_type'] ?? '') === 'school' ? 'selected' : '' ?>>School</option>
                                <option value="church" <?= ($center['facility_type'] ?? '') === 'church' ? 'selected' : '' ?>>Church</option>
                                <option value="community_center" <?= ($center['facility_type'] ?? '') === 'community_center' ? 'selected' : '' ?>>Community Center</option>
                                <option value="gymnasium" <?= ($center['facility_type'] ?? '') === 'gymnasium' ? 'selected' : '' ?>>Gymnasium</option>
                                <option value="barangay_hall" <?= ($center['facility_type'] ?? '') === 'barangay_hall' ? 'selected' : '' ?>>Barangay Hall</option>
                                <option value="other" <?= ($center['facility_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" <?= $center['is_active'] ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !$center['is_active'] ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Additional information about the evacuation center..."><?= htmlspecialchars($center['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="evacuation-centers.php?action=view&id=<?= $center['id'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Center
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
    const capacity = parseInt(document.getElementById('capacity').value);
    const currentOccupancy = parseInt(document.getElementById('current_occupancy').value);
    
    if (capacity <= 0) {
        document.getElementById('capacity').classList.add('is-invalid');
        alert('Capacity must be greater than 0.');
        isValid = false;
    }
    
    if (currentOccupancy < 0) {
        document.getElementById('current_occupancy').classList.add('is-invalid');
        alert('Current occupancy cannot be negative.');
        isValid = false;
    }
    
    if (currentOccupancy > capacity) {
        document.getElementById('current_occupancy').classList.add('is-invalid');
        alert('Current occupancy cannot exceed maximum capacity.');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields correctly.');
    }
});

// Auto-format phone number
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

// Real-time validation for capacity
document.getElementById('capacity').addEventListener('input', function() {
    const capacity = parseInt(this.value);
    const currentOccupancy = parseInt(document.getElementById('current_occupancy').value);
    
    if (capacity <= 0) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
    
    if (currentOccupancy > capacity) {
        document.getElementById('current_occupancy').classList.add('is-invalid');
    } else {
        document.getElementById('current_occupancy').classList.remove('is-invalid');
    }
});

document.getElementById('current_occupancy').addEventListener('input', function() {
    const capacity = parseInt(document.getElementById('capacity').value);
    const currentOccupancy = parseInt(this.value);
    
    if (currentOccupancy < 0) {
        this.classList.add('is-invalid');
    } else if (currentOccupancy > capacity) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>

<style>
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