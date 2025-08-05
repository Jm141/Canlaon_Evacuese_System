<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt"></i> Edit Barangay
                </h5>
                <a href="barangays.php?action=view&id=<?= $barangay['id'] ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Barangay
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="barangayForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Barangay Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle"></i> Barangay Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Barangay Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($barangay['name']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="number" class="form-label">Barangay Number *</label>
                            <input type="text" class="form-control" id="number" name="number" 
                                   value="<?= htmlspecialchars($barangay['number']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" <?= $barangay['is_active'] ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !$barangay['is_active'] ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="barangays.php?action=view&id=<?= $barangay['id'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Barangay
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
document.getElementById('barangayForm').addEventListener('submit', function(e) {
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
    
    // Validate barangay number format
    const number = document.getElementById('number').value;
    if (number && !/^\d+$/.test(number)) {
        document.getElementById('number').classList.add('is-invalid');
        alert('Barangay number must contain only numbers.');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields correctly.');
    }
});

// Real-time validation for barangay number
document.getElementById('number').addEventListener('input', function() {
    const value = this.value;
    if (value && !/^\d+$/.test(value)) {
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