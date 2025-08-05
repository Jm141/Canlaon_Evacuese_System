<?php
// Start output buffering to capture content
ob_start();
?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number"><?= number_format($residentStats['total_residents'] ?? 0) ?></div>
            <div class="stats-label">Total Residents</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stats-number"><?= number_format($householdStats['total_households'] ?? 0) ?></div>
            <div class="stats-label">Total Households</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="stats-number"><?= number_format($idCardStats['active_cards'] ?? 0) ?></div>
            <div class="stats-label">Active ID Cards</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-wheelchair"></i>
            </div>
            <div class="stats-number"><?= number_format($residentStats['special_needs_count'] ?? 0) ?></div>
            <div class="stats-label">Special Needs</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-pie"></i> Age Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="ageChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-bar"></i> Gender Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="genderChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities and Quick Actions -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-clock"></i> Recent Activities
                </h5>
                <a href="residents.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Resident
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Household</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentResidents)): ?>
                                <?php foreach ($recentResidents as $resident): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?></strong>
                                            <?php if ($resident['is_household_head']): ?>
                                                <span class="badge bg-primary ms-1">Head</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($resident['household_head']) ?></td>
                                        <td>
                                            <?php
                                            $birthDate = new DateTime($resident['date_of_birth']);
                                            $today = new DateTime();
                                            $age = $today->diff($birthDate)->y;
                                            echo $age;
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $resident['gender'] === 'male' ? 'primary' : 'secondary' ?>">
                                                <?= ucfirst($resident['gender']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="residents.php?action=view&id=<?= $resident['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> No recent residents found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-tasks"></i> Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="residents.php?action=add" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add New Resident
                    </a>
                    <a href="households.php?action=add" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Add New Household
                    </a>
                    <a href="id-cards.php?action=generate" class="btn btn-success">
                        <i class="fas fa-id-card"></i> Generate ID Card
                    </a>
                    <a href="reports.php" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Special Needs and Evacuation Centers -->
<div class="row">
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-wheelchair"></i> Special Needs Residents
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($specialNeedsResidents)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Special Need</th>
                                    <th>Household</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($specialNeedsResidents, 0, 5) as $resident): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?></td>
                                        <td>
                                            <span class="badge bg-warning">
                                                <?= htmlspecialchars($resident['special_needs_description']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($resident['household_head']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($specialNeedsResidents) > 5): ?>
                        <div class="text-center mt-2">
                            <a href="residents.php?filter=special_needs" class="btn btn-sm btn-outline-primary">
                                View All (<?= count($specialNeedsResidents) ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>No special needs residents found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-building"></i> Evacuation Centers
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($evacuationCenters)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Center Name</th>
                                    <th>Households</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($evacuationCenters as $center): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($center['assigned_evacuation_center']) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $center['household_count'] ?> households
                                            </span>
                                        </td>
                                        <td>
                                            <a href="households.php?filter=evacuation_center&center=<?= urlencode($center['assigned_evacuation_center']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p>No evacuation centers assigned</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Age Distribution Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
const ageChart = new Chart(ageCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($ageDistribution, 'age_group')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($ageDistribution, 'count')) ?>,
            backgroundColor: [
                '#3b82f6',
                '#1e40af',
                '#1e3a8a'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gender Distribution Chart
const genderCtx = document.getElementById('genderChart').getContext('2d');
const genderChart = new Chart(genderCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($genderDistribution, 'gender')) ?>,
        datasets: [{
            label: 'Residents',
            data: <?= json_encode(array_column($genderDistribution, 'count')) ?>,
            backgroundColor: [
                '#3b82f6',
                '#ec4899'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the main layout
require_once 'views/layouts/main.php';
?> 