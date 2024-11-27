// admin/create_admin.php
<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $username = 'admin';
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if admin already exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        echo "Admin user already exists. Please use a different username.";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
        echo "Admin user created successfully.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}