<!-- // pages/projects.php -->
<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$db = new Database();
$conn = $db->getConnection();

// Handle category filtering
$category = $_GET['category'] ?? '';
$query = "SELECT * FROM projects";
if ($category) {
    $query .= " WHERE category = :category";
}
$query .= " ORDER BY completion_date DESC";

$stmt = $conn->prepare($query);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$stmt = $conn->query("SELECT DISTINCT category FROM projects");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="relative bg-gray-900 h-[400px]">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/img/projects-hero.jpg');">
    </div>
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 py-24">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl">
                <?php echo translate('our_projects'); ?>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-300">
                <?php echo translate('projects_subtitle'); ?>
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Category Filter -->
    <div class="mb-12">
        <div class="flex justify-center flex-wrap gap-2">
            <a href="?category="
                class="px-4 py-2 rounded-full text-sm font-medium <?php echo !$category ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                <?php echo translate('all_categories'); ?>
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="?category=<?php echo $cat; ?>"
                class="px-4 py-2 rounded-full text-sm font-medium <?php echo $category === $cat ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                <?php echo translate('service_' . $cat); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($projects as $project): ?>
        <a href="project.php?id=<?php echo $project['id']; ?>" class="group block">
            <div class="relative bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative h-64">
                    <?php if ($project['image_path']): ?>
                    <img src="<?php echo SITE_URL . $project['image_path']; ?>"
                        alt="<?php echo htmlspecialchars($project[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No image available</span>
                    </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                </div>
                <div class="p-6">
                    <span
                        class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-3">
                        <?php echo translate('service_' . $project['category']); ?>
                    </span>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($project[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4">
                        <?php echo substr(htmlspecialchars($project[getCurrentLang() === 'fr' ? 'description_fr' : 'description']), 0, 150) . '...'; ?>
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            <?php echo date('F Y', strtotime($project['completion_date'])); ?>
                        </span>
                        <span
                            class="text-yellow-600 group-hover:text-yellow-700 inline-flex items-center text-sm font-medium">
                            <?php echo translate('view_details'); ?>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>