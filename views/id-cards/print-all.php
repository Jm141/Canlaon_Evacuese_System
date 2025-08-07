<?php
// Start output buffering to capture content
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print All ID Cards - Kanlaon Evacuation Plan</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                font-size: 8pt;
                line-height: 1.1;
            }
            
            .page {
                page-break-after: always;
                margin: 5mm;
                height: 287mm; /* A4 height minus margins */
                position: relative;
                display: grid;
                grid-template-columns: 1fr 1fr;
                grid-template-rows: 1fr 1fr;
                gap: 2mm;
            }
            
            .page:last-child {
                page-break-after: avoid;
            }
            
            .id-card {
                background: white;
                color: black;
                padding: 1rem;
                border: 1px solid #000;
                border-radius: 0;
                position: relative;
                font-family: Arial, sans-serif;
                font-size: 8pt;
                line-height: 1.2;
                height: 100%;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
            }
            
            .card-header {
                text-align: center;
                margin-bottom: 0.5rem;
                border-bottom: 1px solid #000;
                padding-bottom: 0.25rem;
            }
            
            .card-title {
                font-size: 1rem;
                font-weight: bold;
                margin: 0;
                color: #000;
                text-transform: uppercase;
            }
            
            .card-subtitle {
                font-size: 0.9rem;
                font-weight: bold;
                margin: 0.1rem 0 0 0;
                color: #dc3545;
                text-transform: uppercase;
            }
            
            .form-section {
                margin-bottom: 0.5rem;
                flex-grow: 1;
            }
            
            .form-table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .form-table td {
                padding: 0.15rem 0;
                vertical-align: top;
            }
            
            .form-table td:first-child {
                width: 45%;
                font-weight: bold;
                text-transform: uppercase;
                padding-right: 0.25rem;
                font-size: 7pt;
            }
            
            .form-table td:last-child {
                width: 55%;
                border-bottom: 1px solid #000;
                padding-bottom: 0.1rem;
                font-size: 8pt;
            }
            
            .form-label-local {
                font-size: 0.6rem;
                color: #666;
                font-style: italic;
                text-transform: none;
                font-weight: normal;
                display: block;
                margin-top: 0.1rem;
            }
            
            .control-number-section {
                margin-top: 0.5rem;
                padding-top: 0.25rem;
                border-top: 1px solid #ccc;
            }
            
            .control-number-box {
                border: 1px solid #000;
                padding: 0.25rem 0.5rem;
                text-align: center;
                font-weight: bold;
                background: #f8f9fa;
                display: inline-block;
                min-width: 120px;
                font-size: 7pt;
            }
            
            .authority-section {
                display: flex;
                justify-content: space-between;
                margin-top: 0.5rem;
                padding: 0.5rem;
                background: #f4a460;
                border: 1px solid #000;
                font-size: 6pt;
            }
            
            .logo-placeholder {
                width: 40px;
                height: 40px;
                border: 1px dashed #8b4513;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #deb887;
                font-size: 0.5rem;
                color: #8b4513;
                text-align: center;
                flex-shrink: 0;
            }
            
            .authority-list {
                flex-grow: 1;
                margin-left: 0.5rem;
            }
            
            .authority-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.1rem;
            }
            
            .authority-name {
                font-weight: bold;
                text-transform: uppercase;
                font-size: 6pt;
            }
            
            .authority-line {
                border-bottom: 1px solid #000;
                flex-grow: 1;
                margin-left: 0.25rem;
                min-width: 60px;
            }
            
            .authority-phone {
                font-size: 0.5rem;
                color: #666;
            }
            
            .footer {
                text-align: center;
                margin-top: 0.25rem;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 6pt;
            }
            
            .footer-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.25rem;
                margin-top: 0.1rem;
            }
            
            .volcano-logo {
                width: 20px;
                height: 20px;
                border: 1px solid #000;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #ff8c00;
                font-size: 0.4rem;
                text-align: center;
                position: relative;
            }
            
            .checkbox-group {
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }
            
            .checkbox-item {
                display: flex;
                align-items: center;
                gap: 0.1rem;
            }
            
            .checkbox-box {
                width: 8px;
                height: 8px;
                border: 1px solid #000;
                display: inline-block;
                position: relative;
            }
            
            .checkbox-box.checked {
                background: #000;
            }
            
            .checkbox-box.checked::after {
                content: "✓";
                position: absolute;
                top: -1px;
                left: 0;
                font-weight: bold;
                color: white;
                font-size: 6pt;
            }
        }
        
        @media screen {
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background: #f0f0f0;
            }
            
            .page {
                background: white;
                margin: 20px auto;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                max-width: 210mm;
                min-height: 297mm;
                display: grid;
                grid-template-columns: 1fr 1fr;
                grid-template-rows: 1fr 1fr;
                gap: 10px;
            }
            
            .id-card {
                border: 1px solid #ccc;
                padding: 15px;
                background: white;
                font-size: 10pt;
            }
            
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            
            .print-button:hover {
                background: #0056b3;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Print All Cards</button>
    
    <?php 
    $cardsPerPage = 4;
    $totalCards = count($idCards);
    $totalPages = ceil($totalCards / $cardsPerPage);
    
    for ($page = 0; $page < $totalPages; $page++): 
        $startIndex = $page * $cardsPerPage;
        $pageCards = array_slice($idCards, $startIndex, $cardsPerPage);
    ?>
    <div class="page">
        <?php foreach ($pageCards as $card): ?>
        <div class="id-card">
            <!-- Header -->
            <div class="card-header">
                <div class="card-title">KANLAON EVACUATION PLAN</div>
                <div class="card-subtitle">BAKWIT CARD</div>
            </div>

            <!-- Main Information Section -->
            <div class="form-section">
                <table class="form-table">
                    <tr>
                        <td>
                            HOUSEHOLD HEAD:
                            <span class="form-label-local">(PANGULO SANG PANIMALAY)</span>
                        </td>
                        <td><?= htmlspecialchars($card['household_head']) ?></td>
                    </tr>
                    <tr>
                        <td>
                            NO. OF HOUSEHOLD MEMBERS:
                            <span class="form-label-local">(KADAMUON/KADAGHANON SA PANIMALAY)</span>
                        </td>
                        <td><?= htmlspecialchars($card['household_member_count'] ?? '1') ?></td>
                    </tr>
                    <tr>
                        <td>
                            ADDRESS:
                            <span class="form-label-local">(PULOY-AN/PUY-ANAN)</span>
                        </td>
                        <td><?= htmlspecialchars($card['address']) ?></td>
                    </tr>
                    <tr>
                        <td>
                            COLLECTION POINT/PICKUP POINT:
                            <span class="form-label-local">(TILIPUNAN PARA SA BAKWIT)</span>
                        </td>
                        <td><?= htmlspecialchars($card['collection_point'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td>
                            VEHICLE FOR EVACUATION & DRIVER:
                            <span class="form-label-local">(SALAKYAN/SAKYANAN SA PAG BAKWIT KAG/UG DRAYBER)</span>
                        </td>
                        <td><?= htmlspecialchars($card['evacuation_vehicle'] ?? 'N/A') ?> / <?= htmlspecialchars($card['vehicle_driver'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td>
                            ASSIGNED EVACUATION CENTER:
                            <span class="form-label-local">(GIN TUGYAN NGA EVACUATION CENTER)</span>
                        </td>
                        <td><?= htmlspecialchars($card['assigned_evacuation_center'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td>
                            CONTACT NUMBER:
                            <span class="form-label-local">(NUMERO SANG TELEPONO)</span>
                        </td>
                        <td><?= htmlspecialchars($card['household_phone'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td>
                            SPECIAL NEEDS:
                            <span class="form-label-local">(ESPESYAL NGA KINAHANGLAN)</span>
                        </td>
                        <td>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <div class="checkbox-box <?= ($card['has_special_needs'] ?? false) ? 'checked' : '' ?>"></div>
                                    <span>YES (Oo)</span>
                                </div>
                                <div class="checkbox-item">
                                    <div class="checkbox-box <?= !($card['has_special_needs'] ?? false) ? 'checked' : '' ?>"></div>
                                    <span>NO (Indi)</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Control Number Section -->
                <div class="control-number-section">
                    <table class="control-number-table" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 0.15rem 0; vertical-align: middle; width: 30%; font-weight: bold; text-transform: uppercase; padding-right: 0.25rem; font-size: 7pt;">
                                CONTROL NUMBER:
                            </td>
                            <td style="padding: 0.15rem 0; vertical-align: middle; width: 70%;">
                                <div class="control-number-box">
                                    <?= htmlspecialchars($card['control_number']) ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Authority Section -->
            <div class="authority-section">
                <div class="logo-placeholder">LGU</div>
                
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
                        <div class="authority-name">OFFICE OF CIVIL DEFENSE NIR</div>
                        <div class="authority-line">
                            <span class="authority-phone">09956112342 / 09177040134</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div>REGIONAL TASK FORCE KANLAON</div>
                <div class="footer-logo">
                    <div class="volcano-logo">
                        <span style="position: absolute; top: 1px; left: 50%; transform: translateX(-50%); font-size: 0.4rem; font-weight: bold;">🌋</span>
                        <span style="position: absolute; bottom: 1px; left: 50%; transform: translateX(-50%); font-size: 0.3rem; text-align: center; line-height: 1; white-space: pre-line;">TASK FORCE<br>KANLAON</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endfor; ?>
</body>
</html>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 