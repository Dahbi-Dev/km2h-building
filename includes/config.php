<!-- // includes/config.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'construction_portfolio');
define('SITE_URL', 'http://localhost/km2h-building');
define('DEFAULT_LANG', 'en');