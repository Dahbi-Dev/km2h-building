<!-- // includes/header.php -->
<?php
require_once __DIR__ . '/functions.php';
$currentLang = getCurrentLang();
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate('site_title'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="<?php echo SITE_URL; ?>" class="text-xl font-bold">
                        <?php echo translate('km2h building'); ?>
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="<?php echo SITE_URL; ?>" class="nav-link"><?php echo translate('home'); ?></a>
                        <a href="<?php echo SITE_URL; ?>/pages/about.php"
                            class="nav-link"><?php echo translate('about'); ?></a>
                        <a href="<?php echo SITE_URL; ?>/pages/services/index.php"
                            class="nav-link"><?php echo translate('services'); ?></a>
                        <a href="<?php echo SITE_URL; ?>/pages/projects.php"
                            class="nav-link"><?php echo translate('projects'); ?></a>
                        <a href="<?php echo SITE_URL; ?>/pages/blog/index.php"
                            class="nav-link"><?php echo translate('blog'); ?></a>
                        <a href="<?php echo SITE_URL; ?>/pages/contact.php"
                            class="nav-link"><?php echo translate('contact'); ?></a>
                        <select id="langSwitch" class="ml-4 border rounded p-1">
                            <option value="en" <?php echo $currentLang === 'en' ? 'selected' : ''; ?>>English</option>
                            <option value="fr" <?php echo $currentLang === 'fr' ? 'selected' : ''; ?>>Français</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </nav>


    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>

</html>