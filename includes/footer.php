<?php
require_once __DIR__ . '/functions.php';
$currentLang = getCurrentLang();
?>



<footer class="bg-gray-800 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4"><?php echo translate('contact_info'); ?></h3>
                <p><?php echo translate('address'); ?></p>
                <p>Email: info@company.com</p>
                <p>Tel: +1234567890</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4"><?php echo translate('quick_links'); ?></h3>
                <ul class="space-y-2">
                    <li><a href="<?php echo SITE_URL; ?>/pages/about.php"><?php echo translate('about'); ?></a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/services.php"><?php echo translate('services'); ?></a>
                    </li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/projects.php"><?php echo translate('projects'); ?></a>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4"><?php echo translate('follow_us'); ?></h3>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-gray-300">LinkedIn</a>
                    <a href="#" class="hover:text-gray-300">Twitter</a>
                    <a href="#" class="hover:text-gray-300">Facebook</a>
                </div>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-700 text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo translate('company_name'); ?>.
                <?php echo translate('all_rights_reserved'); ?></p>
        </div>
    </div>
</footer>