<!-- // admin/blog/manage.php -->
<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Handle deletion
if (isset($_POST['delete']) && isset($_POST['id'])) {
    try {
        // First, get the image path to delete the file
        $stmt = $conn->prepare("SELECT image_path FROM blog_posts WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post['image_path'] && file_exists('../../' . $post['image_path'])) {
            unlink('../../' . $post['image_path']);
        }
        
        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $success = 'Post deleted successfully';
    } catch (PDOException $e) {
        $error = 'Error deleting post';
    }
}

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$posts_per_page = 10;
$offset = ($page - 1) * $posts_per_page;

// Get total posts count
$total_posts = $conn->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts with pagination
$stmt = $conn->prepare("
    SELECT b.*, a.username as author_name 
    FROM blog_posts b 
    LEFT JOIN admins a ON b.author_id = a.id 
    ORDER BY b.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include '../includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Manage Blog Posts</h1>
                    <a href="create.php" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                        Create New Post
                    </a>
                </div>

                <?php if (isset($success)): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Author
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Views
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($posts as $post): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if ($post['image_path']): ?>
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 object-cover rounded-md"
                                                src="<?php echo SITE_URL . $post['image_path']; ?>" alt="">
                                        </div>
                                        <?php endif; ?>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($post['title_fr']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo ucfirst($post['status'] ?? 'draft'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($post['author_name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo number_format($post['views']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="edit.php?id=<?php echo $post['id']; ?>"
                                            class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                        <form method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this post?');"
                                            style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" name="delete" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </form>
                                        <a href="<?php echo SITE_URL; ?>/pages/blog/post.php?id=<?php echo $post['id']; ?>"
                                            target="_blank" class="text-blue-600 hover:text-blue-900">View</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="mt-6">
                    <nav class="flex justify-center">
                        <ul class="flex space-x-2">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li>
                                <a href="?page=<?php echo $i; ?>"
                                    class="px-3 py-2 <?php echo $i === $page ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> rounded-md">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>