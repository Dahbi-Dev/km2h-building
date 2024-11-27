<!-- // admin/about/manage.php -->
<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST['sections'] as $id => $section) {
            $image_path = null;
            
            // Handle image upload if provided
            if (isset($_FILES['images']['name'][$id]) && $_FILES['images']['error'][$id] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/about/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $filename = uniqid() . '_' . basename($_FILES['images']['name'][$id]);
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$id], $target_path)) {
                    // Delete old image if exists
                    if (!empty($section['current_image'])) {
                        @unlink('../../' . $section['current_image']);
                    }
                    $image_path = '/uploads/about/' . $filename;
                }
            } else {
                $image_path = $section['current_image'] ?? null;
            }
            
            $stmt = $conn->prepare("
                UPDATE about_content 
                SET title = ?, 
                    content = ?, 
                    title_fr = ?, 
                    content_fr = ?,
                    image_path = ?,
                    display_order = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $section['title'],
                $section['content'],
                $section['title_fr'],
                $section['content_fr'],
                $image_path,
                $section['display_order'],
                $id
            ]);
        }
        $success = 'About page content updated successfully';
    } catch (PDOException $e) {
        $error = 'An error occurred while updating the content';
    }
}

// Fetch current content
$stmt = $conn->query("SELECT * FROM about_content ORDER BY display_order");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include '../includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-2xl font-semibold text-gray-900">Manage About Page Content</h1>

                <?php if (isset($success)): ?>
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="mt-6 space-y-8">
                    <?php foreach ($sections as $section): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- English Content -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">English Content</h3>
                                <input type="hidden" name="sections[<?php echo $section['id']; ?>][current_image]"
                                    value="<?php echo htmlspecialchars($section['image_path']); ?>">

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="sections[<?php echo $section['id']; ?>][title]"
                                            value="<?php echo htmlspecialchars($section['title']); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Content</label>
                                        <textarea name="sections[<?php echo $section['id']; ?>][content]" rows="6"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"><?php echo htmlspecialchars($section['content']); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- French Content -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">French Content</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Titre</label>
                                        <input type="text" name="sections[<?php echo $section['id']; ?>][title_fr]"
                                            value="<?php echo htmlspecialchars($section['title_fr']); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Contenu</label>
                                        <textarea name="sections[<?php echo $section['id']; ?>][content_fr]" rows="6"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"><?php echo htmlspecialchars($section['content_fr']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image and Order -->
                        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Section Image</label>
                                <?php if ($section['image_path']): ?>
                                <img src="<?php echo SITE_URL . $section['image_path']; ?>" alt="Section image"
                                    class="mt-2 h-32 w-auto object-cover rounded-md">
                                <?php endif; ?>
                                <input type="file" name="images[<?php echo $section['id']; ?>]" accept="image/*"
                                    class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Display Order</label>
                                <input type="number" name="sections[<?php echo $section['id']; ?>][display_order]"
                                    value="<?php echo htmlspecialchars($section['display_order']); ?>"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Update Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>