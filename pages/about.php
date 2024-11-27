<!-- // pages/about.php -->
<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch about page content
$stmt = $conn->query("SELECT * FROM about_content ORDER BY display_order");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="relative bg-gray-900 h-[400px]">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/img/about-hero.jpg');"></div>
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 py-24">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl">
                <?php echo translate('about_us'); ?>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-300">
                <?php echo translate('about_subtitle'); ?>
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <?php foreach ($sections as $index => $section): ?>
    <div class="py-12 <?php echo $index % 2 === 0 ? '' : 'bg-gray-50'; ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="<?php echo $index % 2 === 0 ? 'lg:order-1' : ''; ?>">
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        <?php echo htmlspecialchars($section[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                    </h2>
                    <div class="mt-6 text-gray-500 space-y-4">
                        <?php echo nl2br(htmlspecialchars($section[getCurrentLang() === 'fr' ? 'content_fr' : 'content'])); ?>
                    </div>
                </div>

                <?php if ($section['image_path']): ?>
                <div class="mt-8 lg:mt-0 <?php echo $index % 2 === 0 ? 'lg:order-2' : ''; ?>">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="<?php echo SITE_URL . $section['image_path']; ?>"
                            alt="<?php echo htmlspecialchars($section[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                            class="rounded-lg shadow-lg object-cover">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once '../includes/footer.php'; ?>