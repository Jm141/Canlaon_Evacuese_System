<?php
require_once 'config/config.php';

// Logout user
$userModel = new User();
$userModel->logout();

// Redirect to login page
redirect('login.php');
?> 