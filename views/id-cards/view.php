<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-id-card"></i> ID Card Details
                </h5>
                <div>
                    <button onclick="window.print()" class="btn btn-primary btn-sm">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="id-cards.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- ID Card Design -->
                <div class="id-card-container" style="max-width: 800px; margin: 0 auto;">
                    <div class="id-card" style="
                        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
                        color: white;
                        padding: 2rem;
                        border-radius: 1rem;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                        position: relative;
                        overflow: hidden;
                    ">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h2 style="margin: 0; font-weight: 700; font-size: 1.5rem;">
                                KANLAON EVACUATION PLAN
                            </h2>
                            <h3 style="margin: 0.5rem 0 0 0; font-weight: 600; font-size: 1.25rem;">
                                BAKWIT CARD
                            </h3>
                        </div>

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Household Head:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?= htmlspecialchars($idCard['household_head']) ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Family Members:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?php
                                        $residentModel = new Resident();
                                        $members = $residentModel->getHouseholdMembers($idCard['household_id']);
                                        echo count($members);
                                        ?> persons
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Address:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?= htmlspecialchars($idCard['address']) ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Collection Point:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?= htmlspecialchars($idCard['collection_point'] ?? 'Not specified') ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Evacuation Vehicle:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?= htmlspecialchars($idCard['evacuation_vehicle'] ?? 'Not assigned') ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Vehicle Driver:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?= htmlspecialchars($idCard['vehicle_driver'] ?? 'Not assigned') ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Evacuation Center:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?= htmlspecialchars($idCard['assigned_evacuation_center'] ?? 'Not assigned') ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Phone Number:</strong>
                                    </div>
                                    <div class="col-8">
                                        <?= htmlspecialchars($idCard['household_phone'] ?? 'Not provided') ?>
                                    </div>
                                </div>

                                <?php if ($idCard['has_special_needs']): ?>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <strong>Special Needs:</strong>
                                    </div>
                                    <div class="col-8">
                                        <span class="badge bg-warning text-dark">
                                            <?= htmlspecialchars($idCard['special_needs_description']) ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-4">
                                <div class="text-center">
                                    <!-- Barcode -->
                                    <div class="mb-3">
                                        <img src="<?= $barcodeImage ?>" alt="Barcode" style="max-width: 100%; height: auto;">
                                    </div>

                                    <!-- Control Number -->
                                    <div class="mb-3">
                                        <strong>Control No:</strong><br>
                                        <span style="font-size: 1.1rem; font-weight: 600;">
                                            <?= htmlspecialchars($idCard['control_number']) ?>
                                        </span>
                                    </div>

                                    <!-- Card Number -->
                                    <div class="mb-3">
                                        <strong>Card No:</strong><br>
                                        <span style="font-size: 1.1rem; font-weight: 600;">
                                            <?= htmlspecialchars($idCard['card_number']) ?>
                                        </span>
                                    </div>

                                    <!-- Issue Date -->
                                    <div class="mb-3">
                                        <strong>Issue Date:</strong><br>
                                        <?= date('M d, Y', strtotime($idCard['issue_date'])) ?>
                                    </div>

                                    <!-- Expiry Date -->
                                    <div class="mb-3">
                                        <strong>Valid Until:</strong><br>
                                        <?= date('M d, Y', strtotime($idCard['expiry_date'])) ?>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <strong>Status:</strong><br>
                                        <span class="badge bg-<?= $idCard['status'] === 'active' ? 'success' : ($idCard['status'] === 'expired' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($idCard['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center mt-4 pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.3);">
                            <small style="opacity: 0.8;">
                                This card is valid for evacuation purposes only.<br>
                                Generated by: <?= htmlspecialchars($idCard['generated_by_name']) ?> | 
                                Barangay: <?= htmlspecialchars($idCard['barangay_name']) ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Card Information -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle"></i> Card Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Card Number:</strong></td>
                                        <td><?= htmlspecialchars($idCard['card_number']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Control Number:</strong></td>
                                        <td><?= htmlspecialchars($idCard['control_number']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Issue Date:</strong></td>
                                        <td><?= date('F d, Y', strtotime($idCard['issue_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Expiry Date:</strong></td>
                                        <td><?= date('F d, Y', strtotime($idCard['expiry_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-<?= $idCard['status'] === 'active' ? 'success' : ($idCard['status'] === 'expired' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($idCard['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Generated By:</strong></td>
                                        <td><?= htmlspecialchars($idCard['generated_by_name']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-user"></i> Resident Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td><?= htmlspecialchars($idCard['first_name'] . ' ' . $idCard['last_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date of Birth:</strong></td>
                                        <td><?= date('F d, Y', strtotime($idCard['date_of_birth'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gender:</strong></td>
                                        <td><?= ucfirst($idCard['gender']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Civil Status:</strong></td>
                                        <td><?= ucfirst($idCard['civil_status']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Contact Number:</strong></td>
                                        <td><?= htmlspecialchars($idCard['contact_number'] ?? 'Not provided') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Barangay:</strong></td>
                                        <td><?= htmlspecialchars($idCard['barangay_name']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-cogs"></i> Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex gap-2">
                                    <button onclick="window.print()" class="btn btn-primary">
                                        <i class="fas fa-print"></i> Print ID Card
                                    </button>
                                    <a href="id-cards.php?action=generate&resident_id=<?= $idCard['resident_id'] ?>" 
                                       class="btn btn-success">
                                        <i class="fas fa-plus"></i> Generate New Card
                                    </a>
                                    <?php if ($idCard['status'] === 'active'): ?>
                                    <button onclick="cancelCard(<?= $idCard['id'] ?>)" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Cancel Card
                                    </button>
                                    <?php endif; ?>
                                    <a href="residents.php?action=view&id=<?= $idCard['resident_id'] ?>" 
                                       class="btn btn-info">
                                        <i class="fas fa-user"></i> View Resident
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelCard(cardId) {
    if (confirm('Are you sure you want to cancel this ID card? This action cannot be undone.')) {
        fetch('id-cards.php?ajax=cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'card_id=' + cardId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the card.');
        });
    }
}
</script>

<style>
@media print {
    .id-card {
        box-shadow: none !important;
        border: 2px solid #000 !important;
    }
    
    .btn, .card-header, .card-body:not(:first-child) {
        display: none !important;
    }
    
    .id-card-container {
        max-width: none !important;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 