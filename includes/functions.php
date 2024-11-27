<!-- // includes/functions.php -->
<?php
require_once __DIR__ . '/config.php';

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/auth/login.php');
        exit();
    }
}

function getCurrentAdminUsername() {
    return $_SESSION['admin_username'] ?? null;
}

function getCurrentLang() {
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULT_LANG;
}

function translate($key) {
    $lang = getCurrentLang();
    include_once __DIR__ . "/../assets/lang/{$lang}.php";
    return isset($translations[$key]) ? $translations[$key] : $key;
}