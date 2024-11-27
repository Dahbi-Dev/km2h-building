<!-- // admin/projects/edit.php -->
<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

requireLogin();

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: ' . SITE_URL . '/admin/projects/manage.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Define available categories
$categories = [
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

$error = $success = '';

// Fetch current project data
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header('Location: ' . SITE_URL . '/admin/projects/manage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $title_fr = trim($_POST['title_fr'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $description_fr = trim($_POST['description_fr'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $completion_date = trim($_POST['completion_date'] ?? '');
    
    if (empty($title) || empty($description) || empty($category) || empty($completion_date)) {
        $error = 'All fields are required';
    } else {
        try {
            // Handle image upload if new image is provided
            $image_path = $project['image_path'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/projects/';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    // Delete old image if exists
                    if ($project['image_path'] && file_exists('../../' . $project['image_path'])) {
                        unlink('../../' . $project['image_path']);
                    }
                    $image_path = '/uploads/projects/' . $filename;
                }
            }
            
            $stmt = $conn->prepare("
                UPDATE projects 
                SET title = ?, 
                    title_fr = ?, 
                    description = ?, 
                    description_fr = ?, 
                    category = ?, 
                    completion_date = ?, 
                    image_path = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $title,
                $title_fr,
                $description,
                $description_fr,
                $category,
                $completion_date,
                $image_path,
                $id
            ]);
            
            $success = 'Project updated successfully';
            
            // Refresh project data
            $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error = 'An error occurred while updating the project';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include '../includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-2xl font-semibold text-gray-900">Edit Project</h1>

                <?php if ($error): ?>
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">English Version</h2>
                            <div class="mt-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" name="title" id="title" required
                                    value="<?php echo htmlspecialchars($project['title']); ?>"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>
                            <div class="mt-4">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="10" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"><?php echo htmlspecialchars($project['description']); ?></textarea>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-lg font-medium text-gray-900">French Version</h2>
                            <div class="mt-4">
                                <label for="title_fr" class="block text-sm font-medium text-gray-700">Titre</label>
                                <input type="text" name="title_fr" id="title_fr" required
                                    value="<?php echo htmlspecialchars($project['title_fr']); ?>"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>
                            <div class="mt-4">
                                <label for="description_fr"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description_fr" id="description_fr" rows="10" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"><?php echo htmlspecialchars($project['description_fr']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                <?php foreach ($categories as $key => $name): ?>
                                <option value="<?php echo $key; ?>"
                                    <?php echo $project['category'] === $key ? 'selected' : ''; ?>>
                                    <?php echo $name; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="completion_date" class="block text-sm font-medium text-gray-700">Completion
                                Date</label>
                            <input type="date" name="completion_date" id="completion_date" required
                                value="<?php echo htmlspecialchars($project['completion_date']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Image</label>
                            <?php if ($project['image_path']): ?>
                            <div class="mt-2">
                                <img src="<?php echo SITE_URL . $project['image_path']; ?>" alt="Current project image"
                                    class="max-w-xs rounded-md">
                            </div>
                            <?php else: ?>
                            <p class="mt-2 text-sm text-gray-500">No image currently set</p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">
                                Upload New Image (optional)
                            </label>
                            <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-yellow-50 file:text-yellow-700
                                          hover:file:bg-yellow-100">
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="manage.php"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Back to Projects
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="mt-2 max-w-xs rounded-md shadow-sm">
                    `;
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>

</html>