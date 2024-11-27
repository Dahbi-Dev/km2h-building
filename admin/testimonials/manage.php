<!-- // admin/testimonials/manage.php -->
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
        $stmt = $conn->prepare("SELECT image_path FROM testimonials WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($testimonial['image_path'] && file_exists('../../' . $testimonial['image_path'])) {
            unlink('../../' . $testimonial['image_path']);
        }
        
        $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $success = 'Testimonial deleted successfully';
    } catch (PDOException $e) {
        $error = 'Error deleting testimonial';
    }
}

// Get all testimonials
$testimonials = $conn->query("SELECT * FROM testimonials ORDER BY display_order, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include '../includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Manage Testimonials</h1>
                    <a href="create.php" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                        Add New Testimonial
                    </a>
                </div>

                <?php if (isset($success)): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rating</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($testimonials as $testimonial): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if ($testimonial['image_path']): ?>
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="<?php echo SITE_URL . $testimonial['image_path']; ?>" alt="">
                                        </div>
                                        <?php endif; ?>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($testimonial['client_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($testimonial['company']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex text-yellow-400">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i
                                            class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-200'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $testimonial['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo ucfirst($testimonial['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo $testimonial['display_order']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="edit.php?id=<?php echo $testimonial['id']; ?>"
                                        class="text-yellow-600 hover:text-yellow-900 mr-4">Edit</a>
                                    <form method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                                        <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                        <button type="submit" name="delete"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
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