<!-- // pages/blog/index.php -->
<?php
require_once '../../includes/header.php';
require_once '../../includes/db.php';

$db = new Database();
$conn = $db->getConnection();

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$posts_per_page = 9;
$offset = ($page - 1) * $posts_per_page;

// Get total published posts
$total_posts = $conn->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts with author information
$stmt = $conn->prepare("
    SELECT b.*, a.username as author_name 
    FROM blog_posts b 
    LEFT JOIN admins a ON b.author_id = a.id 
    WHERE b.status = 'published' 
    ORDER BY b.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get popular posts
$popular_posts = $conn->query("
    SELECT * FROM blog_posts 
    WHERE status = 'published' 
    ORDER BY views DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get unique tags
$tags = [];
$tag_query = $conn->query("SELECT DISTINCT tags FROM blog_posts WHERE status = 'published' AND tags IS NOT NULL");
while ($row = $tag_query->fetch(PDO::FETCH_ASSOC)) {
    if ($row['tags']) {
        $post_tags = explode(',', $row['tags']);
        foreach ($post_tags as $tag) {
            $tag = trim($tag);
            if (!in_array($tag, $tags)) {
                $tags[] = $tag;
            }
        }
    }
}
?>

<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php echo translate('our_blog'); ?></h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto"><?php echo translate('blog_subtitle'); ?></p>
        </div>

        <div class="mt-12 grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($posts as $post): ?>
                    <article
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <?php if ($post['image_path']): ?>
                        <a href="post.php?id=<?php echo $post['id']; ?>">
                            <img src="<?php echo SITE_URL . $post['image_path']; ?>"
                                alt="<?php echo htmlspecialchars($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                                class="w-full h-48 object-cover hover:opacity-90 transition-opacity duration-300">
                        </a>
                        <?php endif; ?>

                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo $post['author_name']; ?></span>
                            </div>

                            <h2 class="text-xl font-semibold text-gray-900 mb-3">
                                <a href="post.php?id=<?php echo $post['id']; ?>"
                                    class="hover:text-yellow-600 transition-colors duration-300">
                                    <?php echo htmlspecialchars($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                                </a>
                            </h2>

                            <p class="text-gray-600 mb-4">
                                <?php 
                                    $content = $post[getCurrentLang() === 'fr' ? 'content_fr' : 'content'];
                                    echo htmlspecialchars(substr(strip_tags($content), 0, 150)) . '...'; 
                                    ?>
                            </p>

                            <?php if ($post['tags']): ?>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                <a href="?tag=<?php echo urlencode(trim($tag)); ?>"
                                    class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200 transition-colors duration-300">
                                    <?php echo htmlspecialchars(trim($tag)); ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <a href="post.php?id=<?php echo $post['id']; ?>"
                                class="inline-flex items-center text-yellow-600 hover:text-yellow-700 transition-colors duration-300">
                                <?php echo translate('read_more'); ?>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="mt-12">
                    <nav class="flex justify-center">
                        <ul class="flex space-x-2">
                            <?php if ($page > 1): ?>
                            <li>
                                <a href="?page=<?php echo $page - 1; ?>"
                                    class="px-4 py-2 bg-white text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-300">
                                    <?php echo translate('previous'); ?>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li>
                                <a href="?page=<?php echo $i; ?>"
                                    class="px-4 py-2 <?php echo $i === $page ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> rounded-md transition-colors duration-300">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                            <li>
                                <a href="?page=<?php echo $page + 1; ?>"
                                    class="px-4 py-2 bg-white text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-300">
                                    <?php echo translate('next'); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Popular Posts -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php echo translate('popular_posts'); ?></h3>
                    <div class="space-y-4">
                        <?php foreach ($popular_posts as $post): ?>
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="flex items-center space-x-4 group">
                            <?php if ($post['image_path']): ?>
                            <img src="<?php echo SITE_URL . $post['image_path']; ?>"
                                alt="<?php echo htmlspecialchars($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                                class="w-16 h-16 object-cover rounded-md">
                            <?php endif; ?>
                            <div>
                                <h4 class="text-gray-900 group-hover:text-yellow-600 transition-colors duration-300">
                                    <?php echo htmlspecialchars($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                                </h4>
                                <p class="text-sm text-gray-500">
                                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                </p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tags Cloud -->
                <?php if ($tags): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php echo translate('tags'); ?></h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($tags as $tag): ?>
                        <a href="?tag=<?php echo urlencode($tag); ?>"
                            class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full hover:bg-gray-200 transition-colors duration-300">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>