<!-- // admin/projects/manage.php -->
<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Handle project deletion
if (isset($_POST['delete']) && isset($_POST['id'])) {
    try {
        $stmt = $conn->prepare("SELECT image_path FROM projects WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete the project image if it exists
        if ($project['image_path'] && file_exists('../../' . $project['image_path'])) {
            unlink('../../' . $project['image_path']);
        }
        
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $success = 'Project deleted successfully';
    } catch (PDOException $e) {
        $error = 'Error deleting project';
    }
}

// Get projects with category filter
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

// Get all unique categories
$stmt = $conn->query("SELECT DISTINCT category FROM projects");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include '../includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Manage Projects</h1>
                    <a href="create.php" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                        Add New Project
                    </a>
                </div>

                <!-- Category Filter -->
                <div class="mb-6">
                    <form method="GET" class="flex space-x-4">
                        <select name="category"
                            class="rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo translate('service_' . $cat); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md">
                            Filter
                        </button>
                    </form>
                </div>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Image
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Completion Date
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($project['image_path']): ?>
                                    <img src="<?php echo SITE_URL . $project['image_path']; ?>" alt="Project thumbnail"
                                        class="h-20 w-20 object-cover rounded-md">
                                    <?php else: ?>
                                    <div class="h-20 w-20 bg-gray-200 rounded-md flex items-center justify-center">
                                        <span class="text-gray-500">No image</span>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($project['title']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($project['title_fr']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <?php echo translate('service_' . $project['category']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('F j, Y', strtotime($project['completion_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="edit.php?id=<?php echo $project['id']; ?>"
                                            class="text-yellow-600 hover:text-yellow-900">
                                            Edit
                                        </a>
                                        <form method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this project?');"
                                            class="inline">
                                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                            <button type="submit" name="delete" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>