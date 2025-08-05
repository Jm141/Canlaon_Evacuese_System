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
                        background: white;
                        color: black;
                        padding: 2rem;
                        border: 2px solid #000;
                        border-radius: 0.5rem;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                        position: relative;
                        margin-bottom: 30px;
                        font-family: Arial, sans-serif;
                    ">
                        <!-- Header -->
                        <div style="text-align: center; margin-bottom: 2rem; border-bottom: 2px solid #000; padding-bottom: 1rem;">
                            <h2 style="margin: 0; font-weight: bold; font-size: 1.5rem; color: #000;">
                                KANLAON EVACUATION PLAN
                            </h2>
                            <h3 style="margin: 0.5rem 0 0 0; font-weight: bold; font-size: 1.25rem; color: #dc3545;">
                                BAKWIT CARD
                            </h3>
                        </div>

                        <!-- Main Information Section -->
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    HOUSEHOLD HEAD: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(PANGULO SANG PANIMALAY)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= htmlspecialchars($idCard['household_head']) ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    NO. OF HOUSEHOLD MEMBERS: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(KADAMUON/KADAGHANON SA PANIMALAY)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= $idCard['household_member_count'] ?? 1 ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    ADDRESS: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(PULOY-AN/PUY-ANAN)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= htmlspecialchars($idCard['address']) ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    COLLECTION POINT/PICKUP POINT: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(TILIPUNAN PARA SA BAKWIT)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= htmlspecialchars($idCard['collection_point'] ?? 'Not specified') ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    VEHICLE FOR EVACUATION & DRIVER: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(SALAKYAN/SAKYANAN SA PAG BAKWIT KAG/UG DRAYBER)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= htmlspecialchars($idCard['evacuation_vehicle'] ?? 'Not specified') ?>
                                    <?php if ($idCard['vehicle_driver']): ?>
                                        / <?= htmlspecialchars($idCard['vehicle_driver']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    ASSIGNED EVACUATION CENTER: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(GINTALANA NGA EVACUATION CENTER)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= htmlspecialchars($idCard['assigned_evacuation_center'] ?? 'Not assigned') ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    PHONE NUMBER OF FAMILY LEADER: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(NUMERO SA SELPON SANG PANGULO SANG PANIMALAY)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= htmlspecialchars($idCard['contact_number'] ?? 'Not provided') ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    PERSONS WITH SPECIAL NEEDS: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(MIYEMBRO NGA MAY ESPESYAL NGA PANGINAHANGLANON)</span>
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?php 
                                    // This would need to be calculated from the database
                                    echo "None"; // Placeholder - should be actual data
                                    ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; margin-bottom: 0.75rem; align-items: baseline;">
                                <div style="font-weight: bold; min-width: 200px; flex-shrink: 0;">
                                    STAYING INSIDE EVACUATION CENTER?: <span style="font-size: 0.8rem; color: #666; font-style: italic;">(MUSULOD BA MO SA EVACUATION CENTER?)</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 20px; height: 20px; border: 2px solid #000; display: inline-block; position: relative; <?= $idCard['assigned_evacuation_center'] ? 'background: #000;' : '' ?>">
                                            <?php if ($idCard['assigned_evacuation_center']): ?>
                                                <span style="position: absolute; top: -2px; left: 2px; font-weight: bold; color: white;">✓</span>
                                            <?php endif; ?>
                                        </div>
                                        <span>YES (Oo)</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 20px; height: 20px; border: 2px solid #000; display: inline-block; position: relative; <?= !$idCard['assigned_evacuation_center'] ? 'background: #000;' : '' ?>">
                                            <?php if (!$idCard['assigned_evacuation_center']): ?>
                                                <span style="position: absolute; top: -2px; left: 2px; font-weight: bold; color: white;">✓</span>
                                            <?php endif; ?>
                                        </div>
                                        <span>NO (Indi)</span>
                                    </div>
                                </div>
                                <div style="font-weight: bold; margin-left: 2rem;">
                                    CONTROL NUMBER:
                                </div>
                                <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; padding-bottom: 0.25rem; min-height: 1.2rem;">
                                    <?= htmlspecialchars($idCard['control_number']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Authority Section -->
                        <div style="display: flex; justify-content: space-between; margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #000;">
                            <div style="width: 120px; height: 120px; border: 2px dashed #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f8f9fa; font-size: 0.8rem; color: #666; text-align: center;">
                                Place LGU logo here
                            </div>
                            
                            <div style="flex-grow: 1; margin-left: 2rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: bold;">LDRRMO</div>
                                    <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; min-width: 150px;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: bold;">PUNONG BARANGAY</div>
                                    <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; min-width: 150px;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: bold;">PUROK LEADER</div>
                                    <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; min-width: 150px;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: bold;">LOCAL POLICE STATION</div>
                                    <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; min-width: 150px;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: bold;">OFFICE OF CIVIL DEFENSE NIR:</div>
                                    <div style="border-bottom: 1px solid #000; flex-grow: 1; margin-left: 1rem; min-width: 200px;">
                                        09956112342 / 09177040134
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div style="text-align: center; margin-top: 1rem; font-weight: bold;">
                            <div>REGIONAL TASK FORCE KANLAON</div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-top: 0.5rem;">
                                <div style="width: 60px; height: 60px; border: 2px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f8f9fa; font-size: 0.6rem; text-align: center;">
                                    TASK FORCE KANLAON<br>
                                    MOUNT KANLAON<br>
                                    EMERGENCY RESPONSE
                                </div>
                            </div>
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
                                            <span class="badge bg-<?= $idCard['status'] === 'active' ? 'success' : 'danger' ?>">
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
                                        <td><strong>Address:</strong></td>
                                        <td><?= htmlspecialchars($idCard['address']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add any JavaScript functionality here if needed
</script>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 