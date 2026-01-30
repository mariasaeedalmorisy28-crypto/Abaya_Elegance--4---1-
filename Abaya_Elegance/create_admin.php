<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$pass = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (full_name, email, password, user_type, is_active) VALUES ('Admin', 'admin@abaya.com', '$pass', 'admin', 1)";

try {
    $db->exec($sql);
    echo "Admin user created. Email: admin@abaya.com / Pass: admin123";
} catch(PDOException $e) {
    echo "Error (maybe admin exists): " . $e->getMessage();
}
?>
