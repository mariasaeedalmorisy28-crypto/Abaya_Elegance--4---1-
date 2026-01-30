<?php
include 'includes/public_header.php';

// Fetch Product Details for items in Cart
$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_column($_SESSION['cart'], 'id');
    // Sanitize IDs
    $ids = array_map('intval', $ids);
    $in = str_repeat('?,', count($ids) - 1) . '?';
    
    $sql = "SELECT * FROM abayas WHERE id IN ($in)";
    $stmt = $db->prepare($sql);
    $stmt->execute($ids);
    $products_db = $stmt->fetchAll(PDO::FETCH_ASSOC); // Get all products in one query
    
    // Map products by ID for easy lookup
    $products_map = [];
    foreach ($products_db as $p) {
        $products_map[$p['id']] = $p;
    }

    // Build Cart display array
    foreach ($_SESSION['cart'] as $key => $session_item) {
        if (isset($products_map[$session_item['id']])) {
            $product = $products_map[$session_item['id']];
            $item_total = $product['price'] * $session_item['quantity'];
            $total_price += $item_total;

            $cart_items[] = [
                'key' => $key, // Session array index
                'id' => $product['id'],
                'name' => $product['name'],
                'image' => $product['main_image'],
                'price' => $product['price'],
                'size' => $session_item['size'],
                'quantity' => $session_item['quantity'],
                'subtotal' => $item_total
            ];
        }
    }
}
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-4">سلة التسوق</h2>
        </div>
    </div>

    <?php if(empty($cart_items)): ?>
        <div class="text-center py-5 rounded shadow-sm border border-dark" style="background: #111;">
            <i class="fas fa-shopping-basket fa-4x text-muted mb-3 opacity-25"></i>
            <h3 class="fw-bold text-muted">سلة التسوق فارغة</h3>
            <p class="text-muted mb-4">لم تقم بإضافة أي منتجات للسلة بعد.</p>
            <a href="shop.php" class="btn btn-gold px-5 py-2 rounded-pill">تصفح المنتجات</a>
        </div>
    <?php else: ?>
        <form action="cart_action.php" method="POST">
            <input type="hidden" name="action" value="update_cart">
            <div class="row g-4">
                <!-- Cart Items List -->
                <div class="col-lg-8 animate__animated animate__fadeInUp">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead style="background: #111; color: #fff;">
                                    <tr>
                                        <th class="ps-4" style="width: 40%">المنتج</th>
                                        <th style="width: 20%">السعر</th>
                                        <th style="width: 20%">الكمية</th>
                                        <th style="width: 20%">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cart_items as $item): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <a href="product.php?id=<?php echo $item['id']; ?>">
                                                    <img src="<?php echo $item['image']; ?>" class="rounded me-3" width="70" height="80" style="object-fit: cover;">
                                                </a>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <a href="product.php?id=<?php echo $item['id']; ?>" class="text-dark text-decoration-none">
                                                            <?php echo htmlspecialchars($item['name']); ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">المقاس: <?php echo htmlspecialchars($item['size']); ?></small>
                                                    <a href="cart_action.php?remove=<?php echo $item['key']; ?>" class="d-block text-danger small text-decoration-none mt-1">
                                                        <i class="fas fa-trash-alt me-1"></i> حذف
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($item['price'], 2); ?> ر.س</td>
                                        <td>
                                            <input type="number" name="quantity[<?php echo $item['key']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control form-control-sm text-center" style="width: 70px;">
                                        </td>
                                        <td class="fw-bold text-primary"><?php echo number_format($item['subtotal'], 2); ?> ر.س</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-3 d-flex justify-content-between" style="background: #111; border-top: 1px solid #222;">
                            <a href="shop.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="fas fa-arrow-right me-2"></i> متابعة التسوق
                            </a>
                            <button type="submit" class="btn btn-gold btn-sm rounded-pill px-4">
                                <i class="fas fa-sync-alt me-2"></i> تحديث السلة
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4">ملخص الطلب</h5>
                        
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">مجموع المنتجات</span>
                            <span class="fw-bold"><?php echo number_format($total_price, 2); ?> ر.س</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">الشحن</span>
                            <span class="text-success small">يتم احتسابه عند الدفع</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">الإجمالي الكلي</span>
                            <span class="fw-bold fs-4 text-primary"><?php echo number_format($total_price, 2); ?> ر.س</span>
                        </div>

                        <a href="checkout.php" class="btn btn-gold w-100 py-3 rounded-pill fw-bold shadow">
                            إتمام الطلب <i class="fas fa-check-circle ms-2"></i>
                        </a>

                        <div class="mt-4 text-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" width="40" class="mx-1 opacity-50">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png" width="40" class="mx-1 opacity-50">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/PayPal.svg/1200px-PayPal.svg.png" width="60" class="mx-1 opacity-50">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/public_footer.php'; ?>
