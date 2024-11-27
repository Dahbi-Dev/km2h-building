<!-- // pages/blog/post.php -->
<?php
require_once '../../includes/header.php';
require_once '../../includes/db.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: ' . SITE_URL . '/pages/blog/index.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Update view count
$conn->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$id]);

// Get post with author information
$stmt = $conn->prepare("
    SELECT b.*, a.username as author_name 
    FROM blog_posts b 
    LEFT JOIN admins a ON b.author_id = a.id 
    WHERE b.id = ? AND b.status = 'published'
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: ' . SITE_URL . '/pages/blog/index.php');
    exit();
}

// Get related posts
$stmt = $conn->prepare("
    SELECT * FROM blog_posts 
    WHERE status = 'published' 
    AND id != ? 
    AND (
        tags LIKE ? 
        OR category = (SELECT category FROM blog_posts WHERE id = ?)
    )
    LIMIT 3
");
$tags = $post['tags'] ? '%' . $post['tags'] . '%' : '';
$stmt->execute([$id, $tags, $id]);
$related_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<article class="bg-white">
    <?php if ($post['image_path']): ?>
    <div class="relative h-[400px] lg:h-[600px]">
        <img src="<?php echo SITE_URL . $post['image_path']; ?>"
            alt="<?php echo htmlspecialchars($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="relative max-w-7xl mx-auto px-4 h-full flex items-end pb-12">
            <div class="max-w-3xl">
                <h1 class="text-4xl font-bold text-white mb-4">
                    <?php echo htmlspecialchars($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                </h1>
                <div class="flex items-center text-gray-200 text-sm">
                    <span><?php echo $post['author_name']; ?></span>
                    <span class="mx-2">•</span>
                    <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                    <span class="mx-2">•</span>
                    <span><?php echo $post['views']; ?> <?php echo translate('views'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="prose lg:prose-lg max-w-none">
                    <?php echo nl2br(htmlspecialchars($post[getCurrentLang() === 'fr' ? 'content_fr' : 'content'])); ?>
                </div>

                <?php if ($post['tags']): ?>
                <div class="mt-8 flex flex-wrap gap-2">
                    <?php foreach (explode(',', $post['tags']) as $tag): ?>
                    <a href="index.php?tag=<?php echo urlencode(trim($tag)); ?>"
                        class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full hover:bg-gray-200 transition-colors duration-300">
                        <?php echo htmlspecialchars(trim($tag)); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Share Buttons -->
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php echo translate('share_article'); ?></h3>
                    <div class="flex space-x-4">
                        <a href="https://twitter.com/share?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                            target="_blank"
                            class="bg-blue-400 text-white px-4 py-2 rounded-md hover:bg-blue-500 transition-colors duration-300">
                            Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>"
                            target="_blank"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-300">
                            Facebook
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                            target="_blank"
                            class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800 transition-colors duration-300">
                            LinkedIn
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Author Info -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php echo translate('about_author'); ?></h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-500 text-xl font-semibold">
                                    <?php echo strtoupper(substr($post['author_name'], 0, 1)); ?>
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-gray-900 font-medium"><?php echo htmlspecialchars($post['author_name']); ?>
                            </h4>
                            <p class="text-gray-500 text-sm"><?php echo translate('author_role'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php if ($related_posts): ?>
                <div class="mt-8 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php echo translate('related_posts'); ?></h3>
                    <div class="space-y-4">
                        <?php foreach ($related_posts as $related): ?>
                        <a href="post.php?id=<?php echo $related['id']; ?>" class="flex items-center space-x-4 group">
                            <?php if ($related['image_path']): ?>
                            <img src="<?php echo SITE_URL . $related['image_path']; ?>"
                                alt="<?php echo htmlspecialchars($related[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>"
                                class="w-16 h-16 object-cover rounded-md">
                            <?php endif; ?>
                            <div>
                                <h4 class="text-gray-900 group-hover:text-yellow-600 transition-colors duration-300">
                                    <?php echo htmlspecialchars($related[getCurrentLang() === 'fr' ? 'title_fr' : 'title']); ?>
                                </h4>
                                <p class="text-sm text-gray-500">
                                    <?php echo date('F j, Y', strtotime($related['created_at'])); ?>
                                </p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="index.php" class="inline-flex items-center text-yellow-600 hover:text-yellow-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <?php echo translate('back_to_blog'); ?>
            </a>
        </div>
    </div>
</article>

<?php require_once '../../includes/footer.php'; ?>