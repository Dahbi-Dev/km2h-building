<!-- // pages/contact.php -->
<?php 
require_once '../includes/header.php';
require_once '../includes/db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = translate('fill_all_fields');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = translate('invalid_email');
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            
            // Send email notification to admin
            $to = "admin@yourcompany.com";
            $headers = "From: $email\r\n";
            $headers .= "Reply-To: $email\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            $emailBody = "
                <h2>New Contact Message</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong><br>$message</p>
            ";
            
            // mail($to, "New Contact Form Submission: $subject", $emailBody, $headers);
            
            $success = translate('message_sent');
            
            // Clear form
            $name = $email = $subject = $message = '';
        } catch (PDOException $e) {
            $error = translate('error_sending');
        }
    }
}
?>

<div class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-center mb-12"><?php echo translate('contact_us'); ?></h1>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $success; ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div>
            <h2 class="text-2xl font-semibold mb-6"><?php echo translate('contact_info'); ?></h2>
            <div class="space-y-4">
                <p class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <?php echo translate('company_address'); ?>
                </p>
                <p class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    info@yourcompany.com
                </p>
                <p class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    +1234567890
                </p>
            </div>
        </div>

        <div>
            <form method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        <?php echo translate('name'); ?>
                    </label>
                    <input type="text" name="name" id="name" required
                        value="<?php echo htmlspecialchars($name ?? ''); ?>"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        <?php echo translate('email'); ?>
                    </label>
                    <input type="email" name="email" id="email" required
                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">
                        <?php echo translate('subject'); ?>
                    </label>
                    <input type="text" name="subject" id="subject" required
                        value="<?php echo htmlspecialchars($subject ?? ''); ?>"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">
                        <?php echo translate('message'); ?>
                    </label>
                    <textarea name="message" id="message" rows="4" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-400 transition">
                    <?php echo translate('send_message'); ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>