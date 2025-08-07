<?php
// Reports page content
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Reports & Analytics</h2>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download"></i> Export
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="reports.php?action=export&type=residents&format=csv&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>">
                <i class="fas fa-file-csv"></i> Export Residents (CSV)
            </a></li>
            <li><a class="dropdown-item" href="reports.php?action=export&type=households&format=csv&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>">
                <i class="fas fa-file-csv"></i> Export Households (CSV)
            </a></li>
            <li><a class="dropdown-item" href="reports.php?action=export&type=id_cards&format=csv&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>">
                <i class="fas fa-file-csv"></i> Export ID Cards (CSV)
            </a></li>
            <li><a class="dropdown-item" href="reports.php?action=export&type=special_needs&format=csv">
                <i class="fas fa-file-csv"></i> Export Special Needs (CSV)
            </a></li>
        </ul>
    </div>
</div>

<!-- Report Type Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="reports.php" class="row g-3">
            <div class="col-md-3">
                <label for="type" class="form-label">Report Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="overview" <?= $reportType === 'overview' ? 'selected' : '' ?>>Overview Dashboard</option>
                    <option value="residents" <?= $reportType === 'residents' ? 'selected' : '' ?>>Residents Report</option>
                    <option value="households" <?= $reportType === 'households' ? 'selected' : '' ?>>Households Report</option>
                    <option value="id_cards" <?= $reportType === 'id_cards' ? 'selected' : '' ?>>ID Cards Report</option>
                    <option value="special_needs" <?= $reportType === 'special_needs' ? 'selected' : '' ?>>Special Needs Report</option>
                    <option value="evacuation" <?= $reportType === 'evacuation' ? 'selected' : '' ?>>Evacuation Report</option>
                    <option value="demographics" <?= $reportType === 'demographics' ? 'selected' : '' ?>>Demographics Report</option>
                </select>
            </div>
            <?php if (isMainAdmin() && !empty($barangays)): ?>
            <div class="col-md-2">
                <label for="barangay_id" class="form-label">Barangay</label>
                <select class="form-select" id="barangay_id" name="barangay_id">
                    <option value="">All Barangays</option>
                    <?php foreach ($barangays as $barangay): ?>
                        <option value="<?= $barangay['id'] ?>" <?= ($_GET['barangay_id'] ?? '') == $barangay['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($barangay['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $dateFrom ?>">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $dateTo ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Generate
                </button>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <a href="reports.php" class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<?php if ($reportType === 'overview'): ?>
    <!-- Overview Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number"><?= number_format($data['total_residents']) ?></div>
                <div class="stats-label">Total Residents</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="stats-number"><?= number_format($data['total_households']) ?></div>
                <div class="stats-label">Total Households</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stats-number"><?= number_format($data['active_id_cards']) ?></div>
                <div class="stats-label">Active ID Cards</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-wheelchair"></i>
                </div>
                <div class="stats-number"><?= number_format($data['special_needs']) ?></div>
                <div class="stats-label">Special Needs</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Age Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="ageChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Gender Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="genderChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($reportType === 'residents'): ?>
    <!-- Residents Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Residents Report</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($data['total_count']) ?></div>
                        <div class="stats-label">Total Residents</div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Gender</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['by_gender'] as $gender): ?>
                                    <tr>
                                        <td><?= ucfirst($gender['gender']) ?></td>
                                        <td><?= number_format($gender['count']) ?></td>
                                        <td><?= number_format(($gender['count'] / $data['total_count']) * 100, 1) ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Civil Status</th>
                            <th>Contact</th>
                            <th>Special Needs</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['residents'] as $resident): ?>
                            <tr>
                                <td><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?></td>
                                <td><?= $resident['age'] ?></td>
                                <td><?= ucfirst($resident['gender']) ?></td>
                                <td><?= ucfirst($resident['civil_status']) ?></td>
                                <td><?= htmlspecialchars($resident['contact_number']) ?></td>
                                <td>
                                    <?php if ($resident['has_special_needs']): ?>
                                        <span class="badge bg-warning">Yes</span>
                                    <?php else: ?>
                                        <span class="text-muted">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($resident['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($reportType === 'households'): ?>
    <!-- Households Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Households Report</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($data['total_count']) ?></div>
                        <div class="stats-label">Total Households</div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Evacuation Center</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['by_evacuation_center'] as $center): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($center['evacuation_center']) ?></td>
                                        <td><?= number_format($center['count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Control Number</th>
                            <th>Household Head</th>
                            <th>Address</th>
                            <th>Evacuation Center</th>
                            <th>Phone</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['households'] as $household): ?>
                            <tr>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($household['control_number']) ?></span></td>
                                <td><?= htmlspecialchars($household['household_head']) ?></td>
                                <td><?= htmlspecialchars($household['address']) ?></td>
                                <td><?= htmlspecialchars($household['assigned_evacuation_center']) ?></td>
                                <td><?= htmlspecialchars($household['phone_number']) ?></td>
                                <td><?= date('M j, Y', strtotime($household['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($reportType === 'id_cards'): ?>
    <!-- ID Cards Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">ID Cards Report</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($data['total_count']) ?></div>
                        <div class="stats-label">Total ID Cards</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($data['active_cards']) ?></div>
                        <div class="stats-label">Active Cards</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($data['expired_cards']) ?></div>
                        <div class="stats-label">Expired Cards</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format(count($data['expiring_soon'])) ?></div>
                        <div class="stats-label">Expiring Soon</div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Card Number</th>
                            <th>Resident</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Generated By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['id_cards'] as $card): ?>
                            <tr>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($card['card_number']) ?></span></td>
                                <td><?= htmlspecialchars($card['resident_name']) ?></td>
                                <td><?= date('M j, Y', strtotime($card['issue_date'])) ?></td>
                                <td>
                                    <?php if ($card['expiry_date']): ?>
                                        <?= date('M j, Y', strtotime($card['expiry_date'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($card['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php elseif ($card['status'] == 'expired'): ?>
                                        <span class="badge bg-danger">Expired</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= ucfirst($card['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($card['generated_by_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($reportType === 'special_needs'): ?>
    <!-- Special Needs Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Special Needs Report</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($data['total_count']) ?></div>
                        <div class="stats-label">Special Needs Residents</div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Special Needs Description</th>
                            <th>Contact</th>
                            <th>Emergency Contact</th>
                            <th>Household</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['special_needs_residents'] as $resident): ?>
                            <tr>
                                <td><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?></td>
                                <td><?= $resident['age'] ?></td>
                                <td><?= ucfirst($resident['gender']) ?></td>
                                <td><?= htmlspecialchars($resident['special_needs_description']) ?></td>
                                <td><?= htmlspecialchars($resident['contact_number']) ?></td>
                                <td>
                                    <?= htmlspecialchars($resident['emergency_contact_name']) ?><br>
                                    <small><?= htmlspecialchars($resident['emergency_contact_number']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($resident['household_head']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($reportType === 'evacuation'): ?>
    <!-- Evacuation Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Evacuation Report</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Evacuation Centers</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Center</th>
                                    <th>Households</th>
                                    <th>Residents</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['households_by_center'] as $center): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($center['evacuation_center']) ?></td>
                                        <td><?= number_format($center['household_count']) ?></td>
                                        <td><?= number_format($center['resident_count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Collection Points</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Collection Point</th>
                                    <th>Households</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['by_collection_point'] as $point): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($point['collection_point']) ?></td>
                                        <td><?= number_format($point['count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($reportType === 'demographics'): ?>
    <!-- Demographics Report -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Age Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="ageChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Gender Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="genderChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (in_array($reportType, ['overview', 'demographics'])): ?>
        // Age Distribution Chart
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($data['age_distribution'], 'age_group')) ?>,
                datasets: [{
                    label: 'Residents',
                    data: <?= json_encode(array_column($data['age_distribution'], 'count')) ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($data['gender_distribution'], 'gender')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($data['gender_distribution'], 'count')) ?>,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    <?php endif; ?>
});
</script> 