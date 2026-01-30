<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

echo "Starting Seeding Process...\n";

// 1. Seed Categories
// Using Placehold.co for reliable placeholder images
$categories = [
    [1, 'عبايات يومية', 'تشكيلة مريحة للاستخدام اليومي', 'https://placehold.co/600x400/333/d4af37?text=Daily+Abaya'],
    [2, 'عبايات سهرة', 'تألقي في مناسباتك الخاصة', 'https://placehold.co/600x400/000/d4af37?text=Evening+Abaya'],
    [3, 'عبايات شتوي', 'دفء وأناقة لفصل الشتاء', 'https://placehold.co/600x400/555/d4af37?text=Winter+Abaya']
];

$sql_cat = "INSERT INTO categories (id, name, description, image) VALUES (:id, :name, :desc, :img) ON DUPLICATE KEY UPDATE name=:name";
$stmt_cat = $db->prepare($sql_cat);

foreach ($categories as $cat) {
    $stmt_cat->execute([':id' => $cat[0], ':name' => $cat[1], ':desc' => $cat[2], ':img' => $cat[3]]);
    echo "Category inserted/updated: " . $cat[1] . "\n";
}

// 2. Seed Abayas (Products)
$products = [
    [
        'name' => 'عباية كلاسيك سوداء',
        'desc' => 'عباية كلاسيكية سوداء بتصميم انسيابي وقماش كريب فاخر، مناسبة للدوام والمناسبات الرسمية.',
        'cat_id' => 1,
        'price' => 250.00,
        'old_price' => 300.00,
        'img' => 'https://placehold.co/600x800/222/d4af37?text=Classic+Black'
    ],
    [
        'name' => 'عباية مطرزة ذهبي',
        'desc' => 'عباية سهرة فاخرة مع تطريز يدوي باللون الذهبي على الأكمام والأطراف.',
        'cat_id' => 2,
        'price' => 550.00,
        'old_price' => NULL,
        'img' => 'https://placehold.co/600x800/000/ffd700?text=Gold+Embroidery'
    ],
    [
        'name' => 'عباية مخمل شتوي',
        'desc' => 'عباية شتوية من المخمل الناعم لتدفئة مثالية ومظهر راقي.',
        'cat_id' => 3,
        'price' => 380.00,
        'old_price' => 450.00,
        'img' => 'https://placehold.co/600x800/444/d4af37?text=Winter+Velvet'
    ],
    [
        'name' => 'عباية لينن بيج',
        'desc' => 'عباية صيفية من قماش اللينن الطبيعي باللون البيج الهادئ.',
        'cat_id' => 1,
        'price' => 290.00,
        'old_price' => NULL,
        'img' => 'https://placehold.co/600x800/dccbb6/333?text=Linen+Beige'
    ],
    [
        'name' => 'عباية فراشة ملكية',
        'desc' => 'تصميم فراشة واسع ومريح يعطي فخامة وحضور مميز.',
        'cat_id' => 2,
        'price' => 420.00,
        'old_price' => 500.00,
        'img' => 'https://placehold.co/600x800/1a1a1a/d4af37?text=Royal+Butterfly'
    ]
];

$sql_prod = "INSERT INTO abayas (name, description, category_id, price, old_price, main_image, stock_quantity, color, size) 
             VALUES (:name, :desc, :cat, :price, :old, :img, 50, 'أسود', 'S,M,L,XL')"; // Simple insert, won't duplicate check heavily here
$stmt_prod = $db->prepare($sql_prod);

// Clear old products to avoid clutter if re-run (Optional, maybe risky if user added data, but user said "Empty")
// Let's check count first.
$count = $db->query("SELECT COUNT(*) FROM abayas")->fetchColumn();
if ($count == 0) {
    foreach ($products as $p) {
        $stmt_prod->execute([
            ':name' => $p['name'],
            ':desc' => $p['desc'],
            ':cat' => $p['cat_id'],
            ':price' => $p['price'],
            ':old' => $p['old_price'],
            ':img' => $p['img']
        ]);
        echo "Product inserted: " . $p['name'] . "\n";
    }
} else {
    echo "Products table not empty, skipping product seeding to preserve data.\n";
}

echo "Seeding Completed Successfully!";
?>
