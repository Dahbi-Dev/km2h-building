<!-- // index.php -->
<?php require_once 'includes/header.php'; ?>

<div class="relative bg-gray-900 h-[600px]">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/img/hero-bg.jpg');"></div>
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 py-32">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl">
                <?php echo translate('hero_title'); ?>
            </h1>
            <p class="mt-6 text-xl text-gray-300">
                <?php echo translate('hero_subtitle'); ?>
            </p>
            <div class="mt-10">
                <a href="<?php echo SITE_URL; ?>/pages/contact.php"
                    class="px-8 py-3 bg-yellow-500 text-black font-semibold rounded-md hover:bg-yellow-400 transition">
                    <?php echo translate('contact_us'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12"><?php echo translate('our_services'); ?></h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            $services = [
                'agricultural' => 'Agricultural Development',
                'irrigation' => 'Large Supply and Irrigation',
                'roads' => 'Roads',
                'airports' => 'Airports',
                'public_works' => 'Public Works',
                'civil' => 'Civil Engineering',
                'earthworks' => 'General Earthworks',
                'environment' => 'Environment',
                'tourism' => 'Tourism Development',
                'urban' => 'Urban Development'
            ];

            foreach ($services as $key => $service) : ?>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
                <h3 class="text-xl font-semibold mb-4"><?php echo translate('service_' . $key); ?></h3>
                <p class="text-gray-600 mb-4"><?php echo translate('service_' . $key . '_desc'); ?></p>
                <a href="<?php echo SITE_URL; ?>/pages/services/<?php echo $key; ?>.php"
                    class="text-yellow-500 hover:text-yellow-600">
                    <?php echo translate('learn_more'); ?> →
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12"><?php echo translate('featured_projects'); ?></h2>
        <?php
        require_once 'includes/db.php';
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM projects ORDER BY completion_date DESC LIMIT 3");
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($projects as $project) : ?>
            <div class="bg-white rounded-lg overflow-hidden shadow-md">
                <img src="<?php echo SITE_URL . $project['image_path']; ?>"
                    alt="<?php echo htmlspecialchars($project[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                    class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">
                        <?php echo htmlspecialchars($project[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                    </h3>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars(substr($project[getCurrentLang() === 'fr' ? 'description_fr' : 'description'], 0, 150)) . '...'; ?>
                    </p>
                    <a href="<?php echo SITE_URL; ?>/pages/projects.php?id=<?php echo $project['id']; ?>"
                        class="mt-4 inline-block text-yellow-500 hover:text-yellow-600">
                        <?php echo translate('view_project'); ?> →
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>