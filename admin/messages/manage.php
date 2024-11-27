<!-- // admin/messages/manage.php -->
<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Handle status updates
if (isset($_POST['mark_read']) && isset($_POST['id'])) {
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $stmt->execute([$_POST['id']]);
} elseif (isset($_POST['mark_replied']) && isset($_POST['id'])) {
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}

// Get all messages with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total = $conn->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$total_pages = ceil($total / $per_page);

$stmt = $conn->prepare("
    SELECT * FROM contact_messages 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include '../includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-2xl font-semibold text-gray-900">Contact Messages</h1>

                <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($messages as $message): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($message['name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>"
                                        class="text-yellow-600 hover:text-yellow-700">
                                        <?php echo htmlspecialchars($message['email']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($message['subject']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $message['status'] === 'new' ? 'bg-green-100 text-green-800' : 
                                                ($message['status'] === 'read' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'); ?>">
                                        <?php echo ucfirst($message['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo date('M j, Y g:i a', strtotime($message['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <?php if ($message['status'] === 'new'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                            <button type="submit" name="mark_read"
                                                class="text-blue-600 hover:text-blue-900">
                                                Mark Read
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($message['status'] !== 'replied'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                            <button type="submit" name="mark_replied"
                                                class="text-green-600 hover:text-green-900">
                                                Mark Replied
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <button type="button"
                                            onclick="viewMessage(<?php echo htmlspecialchars(json_encode($message)); ?>)"
                                            class="text-yellow-600 hover:text-yellow-900">
                                            View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="mt-6 flex justify-center">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 
                                          <?php echo $i === $page ? 'bg-yellow-50 border-yellow-500' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Message View Modal -->
    <div id="messageModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Message Details</h3>
            <div id="messageContent"></div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
    function viewMessage(message) {
        const modal = document.getElementById('messageModal');
        const content = document.getElementById('messageContent');

        content.innerHTML = `
            <p class="mb-2"><strong>From:</strong> ${message.name}</p>
            <p class="mb-2"><strong>Email:</strong> ${message.email}</p>
            <p class="mb-2"><strong>Subject:</strong> ${message.subject}</p>
            <p class="mb-2"><strong>Message:</strong></p>
            <p class="whitespace-pre-wrap">${message.message}</p>
        `;

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('messageModal').classList.add('hidden');
    }
    </script>
</body>

</html>