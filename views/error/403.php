<?php
ob_start();
?>

<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <!-- Error Icon -->
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <!-- Error Title -->
                    <h2 class="text-danger mb-3">Access Denied</h2>
                    
                    <!-- Error Message -->
                    <p class="text-muted mb-4">
                        <?= htmlspecialchars($message ?? 'You do not have permission to access this page.') ?>
                    </p>
                    
                    <!-- Error Code -->
                    <div class="alert alert-light border mb-4">
                        <strong>Error 403:</strong> Forbidden
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Go to Dashboard
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                    </div>
                    
                    <!-- Help Text -->
                    <div class="mt-4">
                        <small class="text-muted">
                            If you believe this is an error, please contact your administrator.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.min-vh-100 {
    min-height: 100vh;
}

.card {
    border-radius: 1rem;
}

.fas.fa-exclamation-triangle {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}
</style>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 