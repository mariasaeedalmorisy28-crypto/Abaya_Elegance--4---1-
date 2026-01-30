<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$query = "SELECT count(*) as count FROM abayas";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
file_put_contents('debug_log.txt', "Total Abayas: " . $row['count'] . "\n", FILE_APPEND);
?>
