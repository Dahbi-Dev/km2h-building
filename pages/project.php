<!-- // pages/project.php -->
<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: ' . SITE_URL . '/pages/projects.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get project details
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header('Location: ' . SITE_URL . '/pages/projects.php');
    exit();
}

// Get related projects in the same category
$stmt = $conn->prepare("
    SELECT * FROM projects 
    WHERE category = ? AND id != ? 
    ORDER BY completion_date DESC 
    LIMIT 3
");
$stmt->execute([$project['category'], $id]);
$relatedProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bg-white">
    <?php if ($project['image_path']): ?>
    <div class="relative h-[500px]">
        <img src="<?php echo SITE_URL . $project['image_path']; ?>"
            alt="<?php echo htmlspecialchars($project[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="relative max-w-7xl mx-auto px-4 h-full flex items-center">
            <div class="text-white max-w-3xl">
                <span
                    class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-4">
                    <?php echo translate('service_' . $project['category']); ?>
                </span>
                <h1 class="text-4xl font-bold mb-4">
                    <?php echo htmlspecialchars($project[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                </h1>
                <p class="text-lg text-gray-200">
                    <?php echo translate('completed'); ?>:
                    <?php echo date('F Y', strtotime($project['completion_date'])); ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <div class="prose lg:prose-lg">
                    <?php echo nl2br(htmlspecialchars($project[getCurrentLang() === 'fr' ? 'description_fr' : 'description'])); ?>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-lg p-6 sticky top-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo translate('project_details'); ?></h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500"><?php echo translate('category'); ?></dt>
                            <dd class="mt-1 text-gray-900"><?php echo translate('service_' . $project['category']); ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500"><?php echo translate('completion_date'); ?></dt>
                            <dd class="mt-1 text-gray-900">
                                <?php echo date('F j, Y', strtotime($project['completion_date'])); ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <?php if ($relatedProjects): ?>
        <div class="mt-16">
            <h2 class="text-2xl font-bold mb-8"><?php echo translate('related_projects'); ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($relatedProjects as $related): ?>
                <a href="project.php?id=<?php echo $related['id']; ?>" class="group block">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <?php if ($related['image_path']): ?>
                        <div class="relative h-48">
                            <img src="<?php echo SITE_URL . $related['image_path']; ?>"
                                alt="<?php echo htmlspecialchars($related[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity">
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">
                                <?php echo htmlspecialchars($related[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                            </h3>
                            <span class="text-yellow-600 group-hover:text-yellow-700">
                                <?php echo translate('view_details'); ?> →
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-12 text-center">
            <a href="projects.php" class="inline-flex items-center text-yellow-600 hover:text-yellow-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <?php echo translate('back_to_projects'); ?>
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>