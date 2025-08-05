<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #1e40af;
            --dark-blue: #1e1b4b;
            --accent-blue: #3b82f6;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f8fafc;
            --border-color: #e5e7eb;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
            min-height: 100vh;
            width: 280px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .sidebar-header h3,
        .sidebar.collapsed .sidebar-header p,
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.875rem;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.25rem;
        }
        
        .sidebar.collapsed .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar.collapsed .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }
        
        .sidebar-toggle-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .sidebar.collapsed .sidebar-toggle-btn {
            right: 0.5rem;
        }

        .sidebar-header h3 {
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
        }

        .sidebar-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            margin: 0.25rem 0 0 0;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--accent-blue);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: var(--accent-blue);
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            text-align: center;
        }

        .nav-link span {
            font-weight: 500;
        }

        /* Main Content */
        .main-content {
            margin-left: 70px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.sidebar-expanded {
            margin-left: 280px;
        }

        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .top-nav-left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--text-dark);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
            display: none;
        }

        .sidebar-toggle:hover {
            background-color: var(--bg-light);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0 1rem;
        }

        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background-color: var(--bg-light);
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-dark);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-light);
            text-transform: capitalize;
        }

        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }

        /* Content Area */
        .content {
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .btn-secondary {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--bg-light);
            border-color: var(--text-light);
        }

        .btn-success {
            background: var(--success-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning {
            background: var(--warning-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background: var(--danger-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        /* Forms */
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 0.875rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        /* Tables */
        .table {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background: var(--bg-light);
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .table tbody td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: var(--bg-light);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: var(--success-color);
        }

        .alert-warning {
            background-color: #fef3c7;
            color: var(--warning-color);
        }

        .alert-danger {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        .alert-info {
            background-color: #dbeafe;
            color: var(--accent-blue);
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.25rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: var(--text-light);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.sidebar-expanded {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }
            
            .sidebar-toggle-btn {
                display: none;
            }

            .content {
                padding: 1rem;
            }

            .top-nav {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .user-info {
                padding: 0.5rem;
            }

            .user-details {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .card-header {
                padding: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }

        /* Loading Spinner */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Print Styles */
        @media print {
            .sidebar, .top-nav, .btn, .no-print {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar collapsed" id="sidebar">
        <div class="sidebar-header">
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn">
                <i class="fas fa-chevron-right"></i>
            </button>
            <h3><i class="fas fa-users"></i> <?= APP_NAME ?></h3>
            <p>Resident Management</p>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="residents.php" class="nav-link <?= $currentPage === 'residents' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Residents</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="households.php" class="nav-link <?= $currentPage === 'households' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Households</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="id-cards.php" class="nav-link <?= $currentPage === 'id-cards' ? 'active' : '' ?>">
                    <i class="fas fa-id-card"></i>
                    <span>ID Cards</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="evacuation-centers.php" class="nav-link <?= $currentPage === 'evacuation-centers' ? 'active' : '' ?>">
                    <i class="fas fa-building"></i>
                    <span>Evacuation Centers</span>
                </a>
            </div>
            
            <?php if (isMainAdmin()): ?>
            <div class="nav-item">
                <a href="users.php" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                    <i class="fas fa-user-cog"></i>
                    <span>User Management</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="barangays.php" class="nav-link <?= $currentPage === 'barangays' ? 'active' : '' ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Barangays</span>
                </a>
            </div>
            <?php endif; ?>
            
            <div class="nav-item">
                <a href="reports.php" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="profile.php" class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="top-nav-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
            </div>
            
            <div class="top-nav-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= $user['full_name'] ?></div>
                        <div class="user-role"><?= str_replace('_', ' ', $user['role']) ?></div>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content">
            <?php if ($flashMessage = $this->getFlashMessage('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $flashMessage ?>
                </div>
            <?php endif; ?>

            <?php if ($flashMessage = $this->getFlashMessage('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $flashMessage ?>
                </div>
            <?php endif; ?>

            <?php if ($flashMessage = $this->getFlashMessage('warning')): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <?= $flashMessage ?>
                </div>
            <?php endif; ?>

            <?php if ($flashMessage = $this->getFlashMessage('info')): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <?= $flashMessage ?>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $content ?? '' ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            
            // Sidebar toggle from top navigation (mobile)
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    // Mobile: show/hide sidebar
                    sidebar.classList.toggle('show');
                } else {
                    // Desktop: expand/collapse sidebar
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-expanded');
                    updateToggleIcon();
                }
            });
            
            // Sidebar toggle from sidebar button (desktop only)
            sidebarToggleBtn.addEventListener('click', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-expanded');
                    updateToggleIcon();
                }
            });
            
            // Update toggle button icon
            function updateToggleIcon() {
                const icon = sidebarToggleBtn.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.className = 'fas fa-chevron-right';
                } else {
                    icon.className = 'fas fa-chevron-left';
                }
            }
            
            // Initialize toggle icon
            updateToggleIcon();

            // Close sidebar when clicking outside (mobile only)
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-expanded');
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);

        // Confirm delete actions
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });

        // Form validation
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        });
    </script>
</body>
</html> 