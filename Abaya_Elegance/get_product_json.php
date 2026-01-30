<?php
header('Content-Type: application/json; charset=utf-8');
include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Product ID is missing']);
    exit();
}

$id = (int)$_GET['id'];
$query = "SELECT abayas.*, categories.name as category_name 
          FROM abayas 
          LEFT JOIN categories ON abayas.category_id = categories.id 
          WHERE abayas.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit();
}

// Format price with SAR
$product['formatted_price'] = number_format($product['price'], 0) . ' ر.س';
if ($product['old_price']) {
    $product['formatted_old_price'] = number_format($product['old_price'], 0) . ' ر.س';
}

echo json_encode($product);
