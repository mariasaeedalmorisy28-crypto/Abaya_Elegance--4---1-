<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h1>User Debug Info</h1>";

try {
    $query = "SELECT id, full_name, email, password, user_type, is_active FROM users";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Password (First 20 chars)</th><th>Type</th><th>Active</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($user['password'], 0, 20)) . "...</td>"; // Truncate for display safety mostly, but we need to see format
            echo "<td>" . htmlspecialchars($user['user_type']) . "</td>";
            echo "<td>" . htmlspecialchars($user['is_active']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No users found in database.";
    }
    
    // Test a specific password verify if needed
    // echo "<br>Test '123456': " . (password_verify('123456', '$2y$10$...') ? 'Match' : 'No Match');
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
