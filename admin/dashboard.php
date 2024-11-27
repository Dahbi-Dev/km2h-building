<!-- // admin/dashboard.php -->
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Fetch statistics with modified queries
$stats = [
    'messages' => [
        'total' => $conn->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
        'new' => $conn->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
        'last_week' => $conn->query("SELECT COUNT(*) FROM contact_messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn()
    ],
  'blog' => [
    'total' => $conn->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
    'views' => $conn->query("SELECT COALESCE(SUM(views), 0) FROM blog_posts")->fetchColumn(),
    'recent' => $conn->query("SELECT COUNT(*) FROM blog_posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn()
],
    'projects' => [
        'total' => $conn->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
        'by_category' => $conn->query("SELECT category, COUNT(*) as count FROM projects GROUP BY category")->fetchAll(PDO::FETCH_KEY_PAIR)
    ]
];

// Get recent messages
$recentMessages = $conn->query("
    SELECT * FROM contact_messages 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get recent blog posts
$recentPosts = $conn->query("
    SELECT * FROM blog_posts 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php include __DIR__ . '/includes/admin-nav.php'; ?>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Welcome,
                    <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
                <p class="mt-2 text-gray-600">Here's an overview of your website's current status</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Messages Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">New Messages</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            <?php echo $stats['messages']['new']; ?></div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                            <span>of <?php echo $stats['messages']['total']; ?> total</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="messages/manage.php" class="font-medium text-yellow-600 hover:text-yellow-500">
                                View all messages
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Blog Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Blog Posts</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            <?php echo $stats['blog']['total']; ?></div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                            <span><?php echo $stats['blog']['recent']; ?> this month</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="blog/manage.php" class="font-medium text-yellow-600 hover:text-yellow-500">
                                Manage blog posts
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Projects Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Projects</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            <?php echo $stats['projects']['total']; ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="projects/manage.php" class="font-medium text-yellow-600 hover:text-yellow-500">
                                Manage projects
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Messages -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Messages</h3>
                    </div>
                    <div class="px-5 py-5">
                        <div class="flow-root">
                            <ul class="-my-4 divide-y divide-gray-200">
                                <?php foreach ($recentMessages as $message): ?>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?php echo htmlspecialchars($message['name']); ?>
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                <?php echo htmlspecialchars($message['subject']); ?>
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($message['created_at'])); ?>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Recent Blog Posts -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Blog Posts</h3>
                    </div>
                    <div class="px-5 py-5">
                        <div class="flow-root">
                            <ul class="-my-4 divide-y divide-gray-200">
                                <?php foreach ($recentPosts as $post): ?>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Views: <?php echo number_format($post['views'] ?? 0); ?>
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Categories Chart -->
            <div class="mt-8 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Projects by Category</h3>
                <canvas id="projectsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <script>
    // Initialize projects chart
    const ctx = document.getElementById('projectsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($stats['projects']['by_category'])); ?>,
            datasets: [{
                label: 'Number of Projects',
                data: <?php echo json_encode(array_values($stats['projects']['by_category'])); ?>,
                backgroundColor: 'rgba(252, 211, 77, 0.5)',
                borderColor: 'rgb(252, 211, 77)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    </script>
</body>

</html>