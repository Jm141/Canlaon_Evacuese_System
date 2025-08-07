<?php
// Start output buffering to capture content
ob_start();
require_once 'views/layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Generate ID Card</h2>
    <a href="id-cards.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to ID Cards
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="id-cards.php?action=generate">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title mb-3">Select Resident</h5>
                    
                    <div class="mb-3">
                        <label for="resident_id" class="form-label">Resident *</label>
                        <select class="form-select" id="resident_id" name="resident_id" required>
                            <option value="">Select a resident</option>
                            <?php foreach ($residents as $resident): ?>
                                <option value="<?= $resident['id'] ?>" 
                                        <?= $selectedResidentId == $resident['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?> 
                                    (<?= $resident['age'] ?> years old) - 
                                    <?= htmlspecialchars($resident['household_head']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Only residents without active ID cards are shown</small>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="id-cards.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-id-card"></i> Generate ID Card
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="card-title mb-3">ID Card Information</h5>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> ID Card Details</h6>
                        <ul class="mb-0">
                            <li><strong>Card Type:</strong> Kanlaon Evacuation Plan Bakwit Card</li>
                            <li><strong>Validity:</strong> <?= ID_CARD_VALIDITY_YEARS ?> years</li>
                            <li><strong>Features:</strong> Barcode, Unique Card Number, Emergency Information</li>
                            <li><strong>Print Format:</strong> A4 size, ready for printing</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Important Notes</h6>
                        <ul class="mb-0">
                            <li>Each resident can only have one active ID card at a time</li>
                            <li>ID cards are valid for <?= ID_CARD_VALIDITY_YEARS ?> years from issue date</li>
                            <li>Generated cards will include evacuation and emergency information</li>
                            <li>Cards can be printed immediately after generation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (empty($residents)): ?>
<div class="card mt-4">
    <div class="card-body text-center">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No Residents Available</h5>
        <p class="text-muted">All residents in your barangay already have active ID cards.</p>
        <a href="residents.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Resident
        </a>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when resident is selected
    document.getElementById('resident_id').addEventListener('change', function() {
        if (this.value) {
            // You can add confirmation here if needed
            // this.form.submit();
        }
    });
});
</script> 