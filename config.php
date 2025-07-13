<?php
// Database configuration
define('DB_HOST', 'localhost:3307');
define('DB_NAME', 'homepage_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_NAME', 'Yizhe Xie - Homepage');
define('SITE_URL', 'http://localhost/Homepage/');
define('ADMIN_EMAIL', 'xieyizhe66@gmail.com');

// Security settings
define('SECRET_KEY', 'your-secret-key-here');
define('PASSWORD_SALT', 'your-password-salt-here');

// Error reporting (set to false in production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Shanghai');
?> 