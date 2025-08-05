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
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <!-- Error Title -->
                    <h2 class="text-danger mb-3">Server Error</h2>
                    
                    <!-- Error Message -->
                    <p class="text-muted mb-4">
                        Something went wrong on our end. Please try again later.
                    </p>
                    
                    <!-- Error Code -->
                    <div class="alert alert-light border mb-4">
                        <strong>Error 500:</strong> Internal Server Error
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Go to Dashboard
                        </a>
                        <button onclick="location.reload()" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Try Again
                        </button>
                    </div>
                    
                    <!-- Help Text -->
                    <div class="mt-4">
                        <small class="text-muted">
                            If the problem persists, please contact your system administrator.
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

.fas.fa-exclamation-circle {
    animation: shake 0.5s ease-in-out infinite alternate;
}

@keyframes shake {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(5px);
    }
}
</style>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 