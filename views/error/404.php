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
                        <i class="fas fa-search text-info" style="font-size: 4rem;"></i>
                    </div>
                    
                    <!-- Error Title -->
                    <h2 class="text-info mb-3">Page Not Found</h2>
                    
                    <!-- Error Message -->
                    <p class="text-muted mb-4">
                        The page you're looking for doesn't exist or has been moved.
                    </p>
                    
                    <!-- Error Code -->
                    <div class="alert alert-light border mb-4">
                        <strong>Error 404:</strong> Not Found
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
                            Check the URL for any typos or contact support if the problem persists.
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

.fas.fa-search {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}
</style>

<?php
$content = ob_get_clean();
require_once 'views/layouts/main.php';
?> 