<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$query = "SELECT count(*) as count FROM abayas";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total Abayas: " . $row['count'];
?>
