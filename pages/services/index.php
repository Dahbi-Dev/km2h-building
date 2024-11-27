<!-- // pages/services/index.php -->
<?php
require_once '../../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-center mb-12"><?php echo translate('our_services'); ?></h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        $services = [
            [
                'key' => 'agricultural',
                'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'
            ],
            [
                'key' => 'irrigation',
                'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636'
            ],
            // Add other services with their icons
        ];

        foreach ($services as $service): ?>
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
            <svg class="w-12 h-12 text-yellow-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="<?php echo $service['icon']; ?>" />
            </svg>
            <h3 class="text-xl font-semibold mb-4">
                <?php echo translate('service_' . $service['key']); ?>
            </h3>
            <p class="text-gray-600 mb-4">
                <?php echo translate('service_' . $service['key'] . '_desc'); ?>
            </p>
            <a href="<?php echo SITE_URL; ?>/pages/services/<?php echo $service['key']; ?>.php"
                class="text-yellow-600 hover:text-yellow-700 font-medium">
                <?php echo translate('learn_more'); ?> →
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

// Example of a specific service page (agricultural.php)
<?php
require_once '../../includes/header.php';
?>

<div class="relative bg-gray-900 h-[400px]">
    <div class="absolute inset-0 bg-cover bg-center"
        style="background-image: url('/assets/img/services/agricultural.jpg');"></div>
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 py-24">
        <h1 class="text-4xl font-bold text-white text-center">
            <?php echo translate('service_agricultural'); ?>
        </h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="prose lg:prose-lg mx-auto">
        <?php echo translate('service_agricultural_full_desc'); ?>

        <h2 class="text-2xl font-bold mt-8 mb-4"><?php echo translate('our_approach'); ?></h2>
        <p><?php echo translate('service_agricultural_approach'); ?></p>

        <h2 class="text-2xl font-bold mt-8 mb-4"><?php echo translate('key_benefits'); ?></h2>
        <ul class="space-y-4">
            <?php
            $benefits = ['benefit1', 'benefit2', 'benefit3', 'benefit4'];
            foreach ($benefits as $benefit): ?>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span><?php echo translate('agricultural_' . $benefit); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>