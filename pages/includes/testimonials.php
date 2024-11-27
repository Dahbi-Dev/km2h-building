// pages/includes/testimonials.php
<?php
$stmt = $conn->prepare("
    SELECT * FROM testimonials 
    WHERE status = 'active' 
    ORDER BY display_order, created_at DESC
");
$stmt->execute();
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">
                <?php echo translate('client_testimonials'); ?>
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                <?php echo translate('testimonials_subtitle'); ?>
            </p>
        </div>

        <div class="mt-12">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex items-center mb-4">
                        <?php if ($testimonial['image_path']): ?>
                        <img class="h-12 w-12 rounded-full object-cover"
                            src="<?php echo SITE_URL . $testimonial['image_path']; ?>"
                            alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>">
                        <?php endif; ?>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                <?php echo htmlspecialchars($testimonial['client_name']); ?>
                            </h3>
                            <?php if ($testimonial['company']): ?>
                            <p class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($testimonial['position'] ? $testimonial['position'] . ', ' : ''); ?>
                                <?php echo htmlspecialchars($testimonial['company']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex text-yellow-400 mb-4">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i
                            class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-200'; ?>"></i>
                        <?php endfor; ?>
                    </div>

                    <blockquote class="text-gray-600 italic">
                        "<?php echo htmlspecialchars($testimonial[getCurrentLang() === 'fr' ? 'content_fr' : 'content']); ?>"
                    </blockquote>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>