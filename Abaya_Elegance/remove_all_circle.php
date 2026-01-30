<?php
// Remove "الكل" circle from index.php
$file = 'c:/xampp/htdocs/Abaya_Elegance/index.php';
$content = file_get_contents($file);

// Find and remove the "الكل" circle element (lines 28-33)
$pattern = '/\s*<a href="shop\.php" class="cat-circle-item">\s*<div class="cat-circle-img d-flex align-items-center justify-content-center bg-dark text-white">\s*<i class="fas fa-th-large fa-2x"><\/i>\s*<\/div>\s*<span class="cat-circle-name">الكل<\/span>\s*<\/a>\s*/s';

$content = preg_replace($pattern, '', $content);

file_put_contents($file, $content);
echo "تم حذف دائرة الكل بنجاح!\n";
?>
