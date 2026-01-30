<?php
include 'includes/public_header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='shop.php';</script>";
    exit();
}

$product_id = $_GET['id'];

// Get Product Details
$query = "SELECT abayas.*, categories.name as category_name 
          FROM abayas 
          LEFT JOIN categories ON abayas.category_id = categories.id 
          WHERE abayas.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<script>window.location.href='shop.php';</script>";
    exit();
}

// Get Related Products (Same Category)
$related_query = "SELECT * FROM abayas WHERE category_id = :cat_id AND id != :id LIMIT 4";
$related_stmt = $db->prepare($related_query);
$related_stmt->bindParam(':cat_id', $product['category_id']);
$related_stmt->bindParam(':id', $product_id);
$related_stmt->execute();
$related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Gallery Images from Database
$gallery_stmt = $db->prepare("SELECT image_path FROM product_images WHERE product_id = :id ORDER BY is_main DESC, id ASC");
$gallery_stmt->bindParam(':id', $product_id);
$gallery_stmt->execute();
$gallery_images = $gallery_stmt->fetchAll(PDO::FETCH_COLUMN);

// Fallback: If no gallery images, use main image
if (empty($gallery_images) && !empty($product['main_image'])) {
    $gallery_images[] = $product['main_image'];
}
?>

<div class="container py-5">
    <div class="row g-5 mb-5 align-items-center">
        <!-- Product Images -->
        <div class="col-lg-7 animate__animated animate__fadeInRight">
            <div class="product-gallery">
                <div class="main-image mb-3 border border-dark rounded-4 overflow-hidden position-relative shadow-lg" style="background: #000;">
                    <?php if($product['old_price'] > $product['price']): ?>
                        <?php $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100); ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-4 py-2 px-3 z-1 fs-6 rounded-pill">-<?php echo $discount; ?>%</span>
                    <?php endif; ?>
                    <img src="<?php echo $product['main_image']; ?>" id="mainImg" class="w-100 h-100 object-fit-cover" style="max-height: 700px; cursor: zoom-in;">
                </div>
                <!-- Thumbnails -->
                <div class="row g-2 justify-content-center" id="thumbnailGallery">
                    <?php foreach($gallery_images as $index => $img): ?>
                    <div class="col-2">
                        <img src="<?php echo $img; ?>" 
                             class="img-thumbnail w-100 cursor-pointer shadow-sm gallery-thumb border-dark <?php echo $index === 0 ? 'active-thumb' : ''; ?>" 
                             onclick="changeImage(this, '<?php echo $img; ?>')" 
                             style="height: 80px; object-fit: cover; background: #111;">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-5 animate__animated animate__fadeInLeft">
            <div class="ps-lg-4">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-secondary text-decoration-none small">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="shop.php" class="text-secondary text-decoration-none small">التسوق</a></li>
                        <li class="breadcrumb-item active small" aria-current="page"><?php echo htmlspecialchars($product['category_name']); ?></li>
                    </ol>
                </nav>

                <h1 class="fw-bold mb-2 display-6" style="letter-spacing: -0.5px;"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div id="product-model" class="text-muted small mb-4">رقم الموديل : <span class="font-monospace text-secondary"><?php echo $product['category_name'] === 'Abayas' ? 'ABY-00' . $product['id'] : 'MOD-00' . $product['id']; ?></span></div>

                <div class="price-area mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold fs-1 text-primary"><?php echo number_format($product['price'], 0); ?> ر.س</span>
                        <?php if($product['old_price']): ?>
                            <span class="text-muted text-decoration-line-through fs-4"><?php echo number_format($product['old_price'], 0); ?> ر.س</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="d-block text-muted small fw-bold mb-2">الوصف :</span>
                    <p class="text-secondary mb-0" style="font-size: 0.95rem; line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>

                <form action="cart_action.php" method="GET">
                    <input type="hidden" name="add" value="<?php echo $product['id']; ?>">
                    
                    <div class="mb-4">
                        <label class="d-block text-muted small fw-bold mb-3">المقاس :</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php 
                            $sizes = explode(',', $product['size']);
                            foreach($sizes as $index => $s): 
                            ?>
                                <input type="radio" class="btn-check" name="size" id="size_<?php echo $index; ?>" value="<?php echo trim($s); ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                <label class="btn btn-outline-dark px-4 py-2 rounded-3 border-2" for="size_<?php echo $index; ?>"><?php echo trim($s); ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="d-block text-muted small fw-bold mb-3">الكمية :</label>
                        <div class="input-group" style="width: 150px;">
                            <button class="btn btn-dark border-dark px-3" type="button" onclick="decrementQty()"><i class="fas fa-minus small"></i></button>
                            <input type="number" name="quantity" id="qtyInput" class="form-control bg-black text-white border-dark text-center fw-bold" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            <button class="btn btn-dark border-dark px-3" type="button" onclick="incrementQty()"><i class="fas fa-plus small"></i></button>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-gold btn-lg py-3 fw-bold rounded-3 shadow-lg">
                            <i class="fas fa-shopping-bag me-2"></i> إضافة للسلة
                        </button>
                        <a href="https://wa.me/<?php echo $settings['contact_phone'] ?? ''; ?>?text=أريد الاستفسار عن: <?php echo urlencode($product['name']); ?>" target="_blank" class="btn btn-outline-success btn-lg py-3 fw-bold rounded-3">
                            <i class="fab fa-whatsapp me-2"></i> هل لديك أي استفسار؟
                        </a>
                    </div>
                </form>

                <div class="mt-5 p-4 rounded-4" style="background: #111; border: 1px solid #222;">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <i class="fas fa-shield-alt text-primary mb-2 fs-4"></i>
                            <div class="small fw-bold text-white">دفع آمن</div>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-truck text-primary mb-2 fs-4"></i>
                            <div class="small fw-bold text-white">شحن سريع</div>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo text-primary mb-2 fs-4"></i>
                            <div class="small fw-bold text-white">إرجاع خلال ٧ أيام</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if(count($related_products) > 0): ?>
    <div class="related-products pt-5 mt-5 border-top border-dark">
        <div class="d-flex justify-content-between align-items-center mb-4">
             <h4 class="fw-bold mb-0">منتجات قد تعجبك</h4>
             <a href="shop.php" class="text-muted text-decoration-none small">عرض الكل</a>
        </div>
        <div class="row g-3 scrolling-wrapper row-products flex-nowrap flex-md-wrap">
            <?php foreach($related_products as $related): ?>
            <div class="col-6 col-md-3">
                <div class="product-card">
                    <div class="product-img-wrapper" style="aspect-ratio: 3/4;">
                        <a href="product.php?id=<?php echo $related['id']; ?>">
                            <img src="<?php echo $related['main_image']; ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                        </a>
                        <!-- Heart & Bag Icons -->
                        
                        <div class="product-bag-icon quick-view-trigger cursor-pointer" data-id="<?php echo $related['id']; ?>">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="product-title"><?php echo htmlspecialchars($related['name']); ?></div>
                        <div class="product-price">
                            <?php echo number_format($related['price'], 0); ?> ر.س
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    function changeImage(element, src) {
        // Update main image
        const mainImg = document.getElementById('mainImg');
        mainImg.style.opacity = '0';
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        }, 200);

        // Update active thumbnail class
        document.querySelectorAll('.gallery-thumb').forEach(thumb => {
            thumb.classList.remove('active-thumb');
        });
        element.classList.add('active-thumb');
    }

    function incrementQty() {
        var input = document.getElementById('qtyInput');
        var max = input.getAttribute('max');
        if(parseInt(input.value) < parseInt(max)) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decrementQty() {
        var input = document.getElementById('qtyInput');
        if(parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
</script>

<?php include 'includes/public_footer.php'; ?>
