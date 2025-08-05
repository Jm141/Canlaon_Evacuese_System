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
            .page-break { page-break-before: always; }
            .id-card { page-break-inside: avoid; }
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: white;
        }
        
        .page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 10mm;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 5mm;
            page-break-after: always;
        }
        
        .page:last-child {
            page-break-after: avoid;
        }
        
        .id-card {
            background: white;
            border: 2px solid #000;
            padding: 8mm;
            box-sizing: border-box;
            font-size: 8pt;
            line-height: 1.2;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 4mm;
            border-bottom: 1px solid #000;
            padding-bottom: 2mm;
        }
        
        .card-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            color: #000;
        }
        
        .card-subtitle {
            font-size: 10pt;
            font-weight: bold;
            margin: 1mm 0 0 0;
            color: #dc3545;
        }
        
        .form-section {
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: flex;
            margin-bottom: 2mm;
            align-items: baseline;
            min-height: 4mm;
        }
        
        .form-label {
            font-weight: bold;
            min-width: 35mm;
            flex-shrink: 0;
            font-size: 7pt;
        }
        
        .form-label-local {
            font-size: 6pt;
            color: #666;
            font-style: italic;
        }
        
        .form-value {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            margin-left: 2mm;
            padding-bottom: 1mm;
            min-height: 3mm;
            font-size: 7pt;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 2mm;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 1mm;
        }
        
        .checkbox-box {
            width: 3mm;
            height: 3mm;
            border: 1px solid #000;
            display: inline-block;
            position: relative;
        }
        
        .checkbox-box.checked {
            background: #000;
        }
        
        .checkbox-box.checked::after {
            content: '✓';
            position: absolute;
            top: -1mm;
            left: 0.5mm;
            font-weight: bold;
            color: white;
            font-size: 6pt;
        }
        
        .authority-section {
            display: flex;
            justify-content: space-between;
            margin-top: 3mm;
            padding-top: 2mm;
            border-top: 1px solid #000;
            flex-grow: 1;
        }
        
        .logo-placeholder {
            width: 15mm;
            height: 15mm;
            border: 1px dashed #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            font-size: 5pt;
            color: #666;
            text-align: center;
            flex-shrink: 0;
        }
        
        .authority-list {
            flex-grow: 1;
            margin-left: 3mm;
        }
        
        .authority-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1mm;
        }
        
        .authority-name {
            font-weight: bold;
            font-size: 6pt;
        }
        
        .authority-line {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            margin-left: 2mm;
            min-width: 20mm;
        }
        
        .footer {
            text-align: center;
            margin-top: 2mm;
            font-weight: bold;
            font-size: 7pt;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1mm;
        }
        
        .volcano-logo {
            width: 8mm;
            height: 8mm;
            border: 1px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            font-size: 4pt;
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
        <p class="text-muted">Bulk Print - <?= count($idCards) ?> ID Cards (4 per page)</p>
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
                <strong>Print Format:</strong> A4 Size (4 cards per page)
            </div>
        </div>
    </div>
    
    <!-- ID Cards Pages -->
    <?php 
    $cardsPerPage = 4;
    $totalPages = ceil(count($idCards) / $cardsPerPage);
    
    for ($page = 0; $page < $totalPages; $page++): 
        $startIndex = $page * $cardsPerPage;
        $pageCards = array_slice($idCards, $startIndex, $cardsPerPage);
    ?>
    <div class="page <?= $page > 0 ? 'page-break' : '' ?>">
        <?php foreach ($pageCards as $card): ?>
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
                            <span style="font-size: 6pt;">YES (Oo)</span>
                        </div>
                        <div class="checkbox-item">
                            <div class="checkbox-box <?= !$card['assigned_evacuation_center'] ? 'checked' : '' ?>"></div>
                            <span style="font-size: 6pt;">NO (Indi)</span>
                        </div>
                    </div>
                    <div class="form-label" style="margin-left: 3mm;">
                        CONTROL NUMBER:
                    </div>
                    <div class="form-value" style="display: flex; align-items: center; gap: 1mm;">
                        <div style="flex-grow: 1; border-bottom: 1px solid #000; padding-bottom: 1mm; min-height: 3mm;">
                            <?= htmlspecialchars($card['control_number']) ?>
                        </div>
                        <div style="flex-shrink: 0; margin-left: 1mm;">
                            <?php 
                            // Generate barcode for control number
                            $barcodeGenerator = new BarcodeGenerator();
                            $controlBarcode = $barcodeGenerator->createBarcodeImage($card['control_number']);
                            ?>
                            <img src="data:image/png;base64,<?= base64_encode($controlBarcode) ?>" 
                                 alt="Control Number Barcode" 
                                 style="width: 15mm; height: 8mm; object-fit: contain;">
                        </div>
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
                        <div class="authority-line" style="min-width: 25mm;">
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
    </div>
    <?php endfor; ?>
    
    <!-- Print Footer -->
    <div class="text-center mt-4 no-print">
        <p class="text-muted">
            <small>
                Generated on <?= date('F j, Y g:i A') ?> | 
                Total Cards: <?= count($idCards) ?> | 
                Pages: <?= $totalPages ?> | 
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