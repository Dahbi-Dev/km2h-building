<!-- // admin/includes/admin-nav.php -->
<?php
require_once __DIR__ . '/../../includes/functions.php';

// Get current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="flex items-center">
                        <span class="text-yellow-500 font-bold text-xl">Admin Panel</span>
                    </a>
                </div>

                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php"
                            class="<?php echo $currentPage === 'dashboard.php' ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Dashboard
                        </a>

                        <div class="relative group">
                            <a href="<?php echo SITE_URL; ?>/admin/projects/manage.php"
                                class="<?php echo strpos($currentPage, 'project') !== false ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Projects
                            </a>

                            <div
                                class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-150 ease-in-out">
                                <div class="py-1">
                                    <a href="<?php echo SITE_URL; ?>/admin/projects/manage.php"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage
                                        Projects</a>
                                    <a href="<?php echo SITE_URL; ?>/admin/projects/create.php"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add New
                                        Project</a>
                                </div>
                            </div>
                        </div>

                        <div
                            class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-150 ease-in-out">
                            <div class="py-1">
                                <a href="<?php echo SITE_URL; ?>/admin/testimonials/manage.php"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Testimonials
                                </a>
                                <a href="<?php echo SITE_URL; ?>/admin/testimonials/create.php"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Add New Testimonial
                                </a>
                                <!-- Additional items can be added here -->
                            </div>
                        </div>

                        <div class="relative group">
                            <a href="<?php echo SITE_URL; ?>/admin/blog/manage.php"
                                class="<?php echo strpos($currentPage, 'blog') !== false ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Blog Posts
                            </a>
                            <div
                                class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-150 ease-in-out">
                                <div class="py-1">
                                    <a href="<?php echo SITE_URL; ?>/admin/blog/manage.php"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage Posts</a>
                                    <a href="<?php echo SITE_URL; ?>/admin/blog/create.php"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add New Post</a>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo SITE_URL; ?>/admin/about/manage.php"
                            class="<?php echo strpos($currentPage, 'about') !== false ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            About Page
                        </a>

                        <a href="<?php echo SITE_URL; ?>/admin/messages/manage.php"
                            class="<?php echo $currentPage === 'manage.php' && strpos($_SERVER['REQUEST_URI'], 'messages') !== false ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            Messages
                            <?php
                            $db = new Database();
                            $conn = $db->getConnection();
                            $newMessages = $conn->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
                            if ($newMessages > 0):
                            ?>
                            <span class="ml-2 bg-yellow-500 text-gray-900 px-2 py-0.5 rounded-full text-xs">
                                <?php echo $newMessages; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <div class="hidden md:flex items-center">
                    <a href="<?php echo SITE_URL; ?>" target="_blank"
                        class="text-gray-300 hover:text-white mr-4 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Site
                    </a>
                    <span class="text-gray-300 mr-4">|</span>
                    <span class="text-gray-300 mr-4">Welcome,
                        <?php echo htmlspecialchars(getCurrentAdminUsername()); ?></span>
                    <a href="<?php echo SITE_URL; ?>/admin/auth/logout.php"
                        class="bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Logout
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button type="button"
                    class="md:hidden bg-gray-800 p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden hidden" id="mobileMenu">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                Dashboard
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/projects/manage.php"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                Projects
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/blog/manage.php"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                Blog Posts
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/about/manage.php"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                About Page
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/messages/manage.php"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                Messages
            </a>
        </div>
    </div>
</nav>

<script>
document.querySelector('button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('hidden');
});
</script>