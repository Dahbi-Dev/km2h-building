<!-- // admin/blog/create.php -->
<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

requireLogin();

$error = $success = '';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $title_fr = trim($_POST['title_fr'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $content_fr = trim($_POST['content_fr'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_description_fr = trim($_POST['meta_description_fr'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required in at least one language';
    } else {
        try {
            // Handle image upload
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/blog/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_path = '/uploads/blog/' . $filename;
                }
            }
            
            $stmt = $conn->prepare("
                INSERT INTO blog_posts (
                    title, title_fr, content, content_fr, 
                    meta_description, meta_description_fr,
                    tags, image_path, status, author_id, 
                    created_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
                )
            ");
            
            $stmt->execute([
                $title,
                $title_fr,
                $content,
                $content_fr,
                $meta_description,
                $meta_description_fr,
                $tags,
                $image_path,
                $status,
                $_SESSION['admin_id']
            ]);
            
            $success = 'Blog post created successfully';
            
            // Clear form data after successful submission
            $title = $title_fr = $content = $content_fr = $meta_description = $meta_description_fr = $tags = '';
        } catch (PDOException $e) {
            $error = 'An error occurred while creating the post';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include '../includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Create New Blog Post</h1>
                    <a href="manage.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                        Back to Posts
                    </a>
                </div>

                <?php if ($error): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- English Content -->
                            <div>
                                <h2 class="text-lg font-medium text-gray-900 mb-4">English Content</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title"
                                            value="<?php echo htmlspecialchars($title ?? ''); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Content</label>
                                        <textarea name="content" rows="10"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                                        <input type="text" name="meta_description"
                                            value="<?php echo htmlspecialchars($meta_description ?? ''); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    </div>
                                </div>
                            </div>

                            <!-- French Content -->
                            <div>
                                <h2 class="text-lg font-medium text-gray-900 mb-4">French Content</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Titre</label>
                                        <input type="text" name="title_fr"
                                            value="<?php echo htmlspecialchars($title_fr ?? ''); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Contenu</label>
                                        <textarea name="content_fr" rows="10"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"><?php echo htmlspecialchars($content_fr ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description Meta</label>
                                        <input type="text" name="meta_description_fr"
                                            value="<?php echo htmlspecialchars($meta_description_fr ?? ''); ?>"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tags</label>
                                <input type="text" name="tags" value="<?php echo htmlspecialchars($tags ?? ''); ?>"
                                    placeholder="Separate tags with commas"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Featured Image</label>
                            <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-yellow-50 file:text-yellow-700
                                          hover:file:bg-yellow-100">
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" name="save_draft" value="1"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Save as Draft
                        </button>
                        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                            Publish Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Image preview functionality
    document.querySelector('input[type="file"]').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" 
                             class="mt-2 max-w-xs rounded-md shadow-sm">
                    `;
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>

</html>