<?php
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print All ID Cards - Kanlaon Evacuation Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .id-card { page-break-inside: avoid; margin-bottom: 20px; }
        }
        
        .id-card {
            background: white;
            color: black;
            padding: 2rem;
            border: 2px solid #000;
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            font-family: Arial, sans-serif;
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid #000;
            padding-bottom: 1rem;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
            color: #000;
        }
        
        .card-subtitle {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0.5rem 0 0 0;
            color: #dc3545;
        }
        
        .form-section {
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: flex;
            margin-bottom: 0.75rem;
            align-items: baseline;
        }
        
        .form-label {
            font-weight: bold;
            min-width: 200px;
            flex-shrink: 0;
        }
        
        .form-label-local {
            font-size: 0.8rem;
            color: #666;
            font-style: italic;
        }
        
        .form-value {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            margin-left: 1rem;
            padding-bottom: 0.25rem;
            min-height: 1.2rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-box {
            width: 20px;
            height: 20px;
            border: 2px solid #000;
            display: inline-block;
            position: relative;
        }
        
        .checkbox-box.checked::after {
            content: '✓';
            position: absolute;
            top: -2px;
            left: 2px;
            font-weight: bold;
        }
        
        .authority-section {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #000;
        }
        
        .logo-placeholder {
            width: 120px;
            height: 120px;
            border: 2px dashed #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            font-size: 0.8rem;
            color: #666;
            text-align: center;
        }
        
        .authority-list {
            flex-grow: 1;
            margin-left: 2rem;
        }
        
        .authority-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .authority-name {
            font-weight: bold;
        }
        
        .authority-line {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            margin-left: 1rem;
            min-width: 150px;
        }
        
        .footer {
            text-align: center;
            margin-top: 1rem;
            font-weight: bold;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .volcano-logo {
            width: 60px;
            height: 60px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            font-size: 0.6rem;
            text-align: center;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
        }
        
        .print-info {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Print Header -->
    <div class="print-header no-print">
        <h1>Kanlaon Evacuation Plan - ID Cards</h1>
        <p class="text-muted">Bulk Print - <?= count($idCards) ?> ID Cards</p>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print All
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
    
    <!-- Print Information -->
    <div class="print-info no-print">
        <div class="row">
            <div class="col-md-6">
                <strong>Generated By:</strong> <?= htmlspecialchars($user['full_name']) ?><br>
                <strong>Date:</strong> <?= date('F j, Y g:i A') ?><br>
                <strong>Total Cards:</strong> <?= count($idCards) ?>
            </div>
            <div class="col-md-6">
                <strong>Barangay:</strong> <?= htmlspecialchars($user['barangay_name'] ?? 'All Barangays') ?><br>
                <strong>Status:</strong> Active ID Cards Only<br>
                <strong>Print Format:</strong> A4 Size
            </div>
        </div>
    </div>
    
    <!-- ID Cards -->
    <?php foreach ($idCards as $card): ?>
    <div class="id-card">
        <!-- Header -->
        <div class="card-header">
            <div class="card-title">KANLAON EVACUATION PLAN</div>
            <div class="card-subtitle">BAKWIT CARD</div>
        </div>

        <!-- Main Information Section -->
        <div class="form-section">
            <div class="form-row">
                <div class="form-label">
                    HOUSEHOLD HEAD: <span class="form-label-local">(PANGULO SANG PANIMALAY)</span>
                </div>
                <div class="form-value">
                    <?= htmlspecialchars($card['household_head']) ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    NO. OF HOUSEHOLD MEMBERS: <span class="form-label-local">(KADAMUON/KADAGHANON SA PANIMALAY)</span>
                </div>
                <div class="form-value">
                    <?= $card['household_member_count'] ?? 1 ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    ADDRESS: <span class="form-label-local">(PULOY-AN/PUY-ANAN)</span>
                </div>
                <div class="form-value">
                    <?= htmlspecialchars($card['address']) ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    COLLECTION POINT/PICKUP POINT: <span class="form-label-local">(TILIPUNAN PARA SA BAKWIT)</span>
                </div>
                <div class="form-value">
                    <?= htmlspecialchars($card['collection_point'] ?? 'Not specified') ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    VEHICLE FOR EVACUATION & DRIVER: <span class="form-label-local">(SALAKYAN/SAKYANAN SA PAG BAKWIT KAG/UG DRAYBER)</span>
                </div>
                <div class="form-value">
                    <?= htmlspecialchars($card['evacuation_vehicle'] ?? 'Not specified') ?>
                    <?php if ($card['vehicle_driver']): ?>
                        / <?= htmlspecialchars($card['vehicle_driver']) ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    ASSIGNED EVACUATION CENTER: <span class="form-label-local">(GINTALANA NGA EVACUATION CENTER)</span>
                </div>
                <div class="form-value">
                    <?= htmlspecialchars($card['assigned_evacuation_center'] ?? 'Not assigned') ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    PHONE NUMBER OF FAMILY LEADER: <span class="form-label-local">(NUMERO SA SELPON SANG PANGULO SANG PANIMALAY)</span>
                </div>
                <div class="form-value">
                    <?= htmlspecialchars($card['contact_number'] ?? 'Not provided') ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    PERSONS WITH SPECIAL NEEDS: <span class="form-label-local">(MIYEMBRO NGA MAY ESPESYAL NGA PANGINAHANGLANON)</span>
                </div>
                <div class="form-value">
                    <?php 
                    // This would need to be calculated from the database
                    echo "None"; // Placeholder - should be actual data
                    ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-label">
                    STAYING INSIDE EVACUATION CENTER?: <span class="form-label-local">(MUSULOD BA MO SA EVACUATION CENTER?)</span>
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <div class="checkbox-box <?= $card['assigned_evacuation_center'] ? 'checked' : '' ?>"></div>
                        <span>YES (Oo)</span>
                    </div>
                    <div class="checkbox-item">
                        <div class="checkbox-box <?= !$card['assigned_evacuation_center'] ? 'checked' : '' ?>"></div>
                        <span>NO (Indi)</span>
                    </div>
                </div>
                <div class="form-label" style="margin-left: 2rem;">
                    CONTROL NUMBER:
                </div>
                <div class="form-value">
                    <?= htmlspecialchars($card['control_number']) ?>
                </div>
            </div>
        </div>

        <!-- Authority Section -->
        <div class="authority-section">
            <div class="logo-placeholder">
                Place LGU logo here
            </div>
            
            <div class="authority-list">
                <div class="authority-item">
                    <div class="authority-name">LDRRMO</div>
                    <div class="authority-line"></div>
                </div>
                <div class="authority-item">
                    <div class="authority-name">PUNONG BARANGAY</div>
                    <div class="authority-line"></div>
                </div>
                <div class="authority-item">
                    <div class="authority-name">PUROK LEADER</div>
                    <div class="authority-line"></div>
                </div>
                <div class="authority-item">
                    <div class="authority-name">LOCAL POLICE STATION</div>
                    <div class="authority-line"></div>
                </div>
                <div class="authority-item">
                    <div class="authority-name">OFFICE OF CIVIL DEFENSE NIR:</div>
                    <div class="authority-line" style="min-width: 200px;">
                        09956112342 / 09177040134
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>REGIONAL TASK FORCE KANLAON</div>
            <div class="footer-logo">
                <div class="volcano-logo">
                    TASK FORCE KANLAON<br>
                    MOUNT KANLAON<br>
                    EMERGENCY RESPONSE
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <!-- Print Footer -->
    <div class="text-center mt-4 no-print">
        <p class="text-muted">
            <small>
                Generated on <?= date('F j, Y g:i A') ?> | 
                Total Cards: <?= count($idCards) ?> | 
                Kanlaon Evacuation Plan System
            </small>
        </p>
    </div>
</body>
</html>

<?php
$content = ob_get_clean();
// For print-all, we don't use the main layout
echo $content;
?> 