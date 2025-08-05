<?php require_once 'views/layouts/main.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Add Barangay</h2>
    <a href="barangays.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Barangays
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="barangays.php?action=add">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Barangay Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">Barangay Code *</label>
                        <input type="text" class="form-control" id="code" name="code" 
                               value="<?= htmlspecialchars($_POST['code'] ?? '') ?>" 
                               placeholder="e.g., BRGY001" required>
                        <small class="form-text text-muted">Unique code for the barangay (e.g., BRGY001, BRGY002)</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="Brief description of the barangay"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="barangays.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Barangay
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate code from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value.trim();
        const codeInput = document.getElementById('code');
        
        if (name && !codeInput.value) {
            // Generate code from name
            const code = 'BRGY' + name.replace(/[^A-Z0-9]/gi, '').toUpperCase().substring(0, 3);
            codeInput.value = code;
        }
    });
});
</script> 