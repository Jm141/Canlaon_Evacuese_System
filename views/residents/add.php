<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-plus"></i> Add New Resident
                </h5>
                <a href="residents.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Residents
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="residentForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Personal Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user"></i> Personal Information
                            </h6>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                   value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth *</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                   value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= ($_POST['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="civil_status" class="form-label">Civil Status *</label>
                            <select class="form-select" id="civil_status" name="civil_status" required>
                                <option value="">Select Status</option>
                                <option value="single" <?= ($_POST['civil_status'] ?? '') === 'single' ? 'selected' : '' ?>>Single</option>
                                <option value="married" <?= ($_POST['civil_status'] ?? '') === 'married' ? 'selected' : '' ?>>Married</option>
                                <option value="widowed" <?= ($_POST['civil_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>Widowed</option>
                                <option value="divorced" <?= ($_POST['civil_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Divorced</option>
                                <option value="separated" <?= ($_POST['civil_status'] ?? '') === 'separated' ? 'selected' : '' ?>>Separated</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality" 
                                   value="<?= htmlspecialchars($_POST['nationality'] ?? 'Filipino') ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" class="form-control" id="religion" name="religion" 
                                   value="<?= htmlspecialchars($_POST['religion'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" class="form-control" id="occupation" name="occupation" 
                                   value="<?= htmlspecialchars($_POST['occupation'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="educational_attainment" class="form-label">Educational Attainment</label>
                            <select class="form-select" id="educational_attainment" name="educational_attainment">
                                <option value="">Select Level</option>
                                <option value="Elementary" <?= ($_POST['educational_attainment'] ?? '') === 'Elementary' ? 'selected' : '' ?>>Elementary</option>
                                <option value="High School" <?= ($_POST['educational_attainment'] ?? '') === 'High School' ? 'selected' : '' ?>>High School</option>
                                <option value="Vocational" <?= ($_POST['educational_attainment'] ?? '') === 'Vocational' ? 'selected' : '' ?>>Vocational</option>
                                <option value="College" <?= ($_POST['educational_attainment'] ?? '') === 'College' ? 'selected' : '' ?>>College</option>
                                <option value="Post Graduate" <?= ($_POST['educational_attainment'] ?? '') === 'Post Graduate' ? 'selected' : '' ?>>Post Graduate</option>
                            </select>
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
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                   value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>" 
                                   placeholder="09XX XXX XXXX">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!-- Address Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-map-marker-alt"></i> Address Information
                            </h6>
                        </div>
                        
                        <?php if (!$userBarangayId && $barangays): ?>
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
                        <?php endif; ?>
                        
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Complete Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      placeholder="Enter complete address including street, barangay, city/municipality" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Evacuation Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-shield-alt"></i> Evacuation Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="collection_point" class="form-label">Collection Point</label>
                            <input type="text" class="form-control" id="collection_point" name="collection_point" 
                                   value="<?= htmlspecialchars($_POST['collection_point'] ?? '') ?>" 
                                   placeholder="Designated pickup location">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="assigned_evacuation_center" class="form-label">Assigned Evacuation Center</label>
                            <input type="text" class="form-control" id="assigned_evacuation_center" name="assigned_evacuation_center" 
                                   value="<?= htmlspecialchars($_POST['assigned_evacuation_center'] ?? '') ?>" 
                                   placeholder="Evacuation center name">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="evacuation_vehicle" class="form-label">Evacuation Vehicle</label>
                            <input type="text" class="form-control" id="evacuation_vehicle" name="evacuation_vehicle" 
                                   value="<?= htmlspecialchars($_POST['evacuation_vehicle'] ?? '') ?>" 
                                   placeholder="Vehicle type or number">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_driver" class="form-label">Vehicle Driver</label>
                            <input type="text" class="form-control" id="vehicle_driver" name="vehicle_driver" 
                                   value="<?= htmlspecialchars($_POST['vehicle_driver'] ?? '') ?>" 
                                   placeholder="Driver's name">
                        </div>
                    </div>
                    
                    <!-- Emergency Contact -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-exclamation-triangle"></i> Emergency Contact
                            </h6>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                                   value="<?= htmlspecialchars($_POST['emergency_contact_name'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="emergency_contact_number" class="form-label">Emergency Contact Number</label>
                            <input type="tel" class="form-control" id="emergency_contact_number" name="emergency_contact_number" 
                                   value="<?= htmlspecialchars($_POST['emergency_contact_number'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" 
                                   value="<?= htmlspecialchars($_POST['emergency_contact_relationship'] ?? '') ?>"
                                   placeholder="e.g., Spouse, Parent, Sibling">
                        </div>
                    </div>
                    
                    <!-- Special Needs -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-wheelchair"></i> Special Needs
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_special_needs" name="has_special_needs" value="1" 
                                       <?= ($_POST['has_special_needs'] ?? '') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="has_special_needs">
                                    Has Special Needs
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3" id="special_needs_description_group" style="display: none;">
                            <label for="special_needs_description" class="form-label">Special Needs Description</label>
                            <textarea class="form-control" id="special_needs_description" name="special_needs_description" rows="3" 
                                      placeholder="Describe the special needs or medical conditions..."><?= htmlspecialchars($_POST['special_needs_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="residents.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Resident
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
// Toggle special needs description field
document.getElementById('has_special_needs').addEventListener('change', function() {
    const descriptionGroup = document.getElementById('special_needs_description_group');
    if (this.checked) {
        descriptionGroup.style.display = 'block';
        document.getElementById('special_needs_description').required = true;
    } else {
        descriptionGroup.style.display = 'none';
        document.getElementById('special_needs_description').required = false;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const hasSpecialNeeds = document.getElementById('has_special_needs');
    if (hasSpecialNeeds.checked) {
        document.getElementById('special_needs_description_group').style.display = 'block';
        document.getElementById('special_needs_description').required = true;
    }
});

// Form validation
document.getElementById('residentForm').addEventListener('submit', function(e) {
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
    
    // Validate date of birth
    const dob = document.getElementById('date_of_birth').value;
    if (dob) {
        const birthDate = new Date(dob);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        
        if (age > 120 || age < 0) {
            document.getElementById('date_of_birth').classList.add('is-invalid');
            alert('Please enter a valid date of birth.');
            isValid = false;
        }
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

document.getElementById('emergency_contact_number').addEventListener('input', function() {
    formatPhoneNumber(this);
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