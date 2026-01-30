<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$email = 'admin@abaya.com';
// Make sure to match the hash cost/algo if specific, but generally defaults are fine.
// The existing hashes start with $2y$10$, which corresponds to PASSWORD_DEFAULT (bcrypt).
$new_password = '123456';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $query = "UPDATE users SET password = :password WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':email', $email);
    
    if($stmt->execute()) {
        echo "Password for $email updated successfully to '$new_password'";
    } else {
        echo "Failed to update password.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
