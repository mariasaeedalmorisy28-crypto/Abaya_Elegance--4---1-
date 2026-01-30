<?php
include 'includes/public_header.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>window.location.href='cart.php';</script>";
    exit();
}

// Calculate Total (Re-calculation for security)
$ids = array_column($_SESSION['cart'], 'id');
$ids = array_map('intval', $ids); // Sanitize
if(empty($ids)){
     echo "<script>window.location.href='cart.php';</script>";
    exit();
}
$in = str_repeat('?,', count($ids) - 1) . '?';
$sql = "SELECT * FROM abayas WHERE id IN ($in)";
$stmt = $db->prepare($sql);
$stmt->execute($ids);
$products_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

$products_map = [];
foreach ($products_db as $p) {
    $products_map[$p['id']] = $p;
}

$total_amount = 0;
$order_items_data = [];

foreach ($_SESSION['cart'] as $item) {
    if (isset($products_map[$item['id']])) {
        $p = $products_map[$item['id']];
        $subtotal = $p['price'] * $item['quantity'];
        $total_amount += $subtotal;
        
        $order_items_data[] = [
            'abaya_id' => $p['id'],
            'quantity' => $item['quantity'],
            'price' => $p['price'], // Unit price at time of purchase
            'name' => $p['name'],
            'subtotal' => $subtotal
        ];
    }
}

// Handle POST Request (Place Order)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $notes = $_POST['notes'];
    
    // User ID (if logged in)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    
    // Generate Order Number (e.g., ORD-TIMESTAMP-RAND)
    $order_number = "ORD-" . date('Ymd') . "-" . rand(1000, 9999);
    
    try {
        $db->beginTransaction();
        
        // 1. Create Order
        $query_order = "INSERT INTO orders (user_id, order_number, total_amount, status, shipping_address, notes, created_at) 
                        VALUES (:uid, :num, :total, 'pending', :address, :notes, NOW())";
        $stmt_order = $db->prepare($query_order);
        $stmt_order->bindValue(':uid', $user_id, $user_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt_order->bindValue(':num', $order_number);
        $stmt_order->bindValue(':total', $total_amount);
        $shipping_info = "الاسم: $full_name\nالايميل: $email\nالهاتف: $phone\nالعنوان: $address";
        $stmt_order->bindValue(':address', $shipping_info); // Store full details in address field for guest simplicity
        $stmt_order->bindValue(':notes', $notes);
        
        $stmt_order->execute();
        $order_id = $db->lastInsertId();
        
        // 2. Create Order Items
        $query_item = "INSERT INTO order_items (order_id, abaya_id, quantity, price, subtotal) VALUES (:oid, :aid, :qty, :price, :sub)";
        $stmt_item = $db->prepare($query_item);
        
        foreach ($order_items_data as $item) {
            $stmt_item->bindValue(':oid', $order_id);
            $stmt_item->bindValue(':aid', $item['abaya_id']);
            $stmt_item->bindValue(':qty', $item['quantity']);
            $stmt_item->bindValue(':price', $item['price']);
            // Recalculate subtotal just to be safe for DB or use DB generated column? 
            // My DB schema has 'GENERATED ALWAYS', so I shouldn't insert subtotal if it's generated?
            // Checking schema step 0: `subtotal DECIMAL(10,2) GENERATED ALWAYS AS (quantity * price) STORED`
            // If it's generated, I cannot insert into it. I must adjust the query.
            // Let's check Schema from step 0: yes, it is GENERATED ALWAYS.
            // So `INSERT INTO order_items (order_id, abaya_id, quantity, price)` is enough.
        }
        
        // Corrected Query for Generated Column
        $query_item = "INSERT INTO order_items (order_id, abaya_id, quantity, price) VALUES (:oid, :aid, :qty, :price)";
        $stmt_item = $db->prepare($query_item);
        
        foreach ($order_items_data as $item) {
            $stmt_item->bindValue(':oid', $order_id);
            $stmt_item->bindValue(':aid', $item['abaya_id']);
            $stmt_item->bindValue(':qty', $item['quantity']);
            $stmt_item->bindValue(':price', $item['price']);
            $stmt_item->execute();
        }
        
        $db->commit();
        
        // Clear Cart
        unset($_SESSION['cart']);
        
        // Redirect to Success
        echo "<script>window.location.href='order_success.php?order=$order_number';</script>";
        exit();
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "حدث خطأ أثناء إتمام الطلب: " . $e->getMessage();
    }
}
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold">إتمام الطلب</h2>
            <p class="text-muted">أكمل ملء بياناتك لتأكيد عملية الشراء</p>
        </div>
    </div>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="animate__animated animate__fadeIn">
        <div class="row g-5">
            <!-- Form Details -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-map-marker-alt text-primary me-2"></i> عنوان التوصيل</h5>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required placeholder="مثال: سارة محمد">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="example@mail.com">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required placeholder="05xxxxxxxx">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">العنوان التفصيلي <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" required placeholder="المدينة، الحي، اسم الشارع، رقم المنزل..."></textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">ملاحظات إضافية (اختياري)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="أي تعليمات خاصة للتوصيل..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3">طريقة الدفع</h5>
                        <div class="form-check p-3 border rounded mb-2" style="background: #111; border-color: #222 !important;">
                            <input class="form-check-input float-end ms-0 me-2" type="radio" name="payment_method" id="cod" checked>
                            <label class="form-check-label me-4 fw-bold" for="cod">
                                <i class="fas fa-money-bill-wave text-success me-2"></i> الدفع عند الاستلام
                            </label>
                        </div>
                         <div class="form-check p-3 border rounded mb-2 text-muted">
                            <input class="form-check-input float-end ms-0 me-2" type="radio" name="payment_method" id="card" disabled>
                            <label class="form-check-label me-4" for="card">
                                <i class="far fa-credit-card me-2"></i> بطاقة ائتمان (قريباً)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm p-4" style="background: #111;">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">ملخص الطلب</h5>
                    
                    <div class="cart-summary-list mb-4" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach($order_items_data as $item): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary rounded-pill me-2"><?php echo $item['quantity']; ?>x</span>
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                            <span class="fw-bold"><?php echo number_format($item['subtotal'], 2); ?> ر.س</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 border-top pt-3">
                        <span>المجموع الفرعي</span>
                        <span class="fw-bold"><?php echo number_format($total_amount, 2); ?> ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>الشحن (ثابت)</span>
                        <span class="fw-bold">0.00 ر.س</span>
                    </div>
                    
                    <div class="total-box p-3 rounded border text-center mb-4" style="background: #050505; border-color: #333 !important;">
                        <small class="text-muted d-block uppercase ls-1">المبلغ الإجمالي</small>
                        <span class="text-primary fs-2 fw-bold"><?php echo number_format($total_amount, 2); ?> ر.س</span>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill shadow-lg fw-bold fs-5">
                        تأكيد الطلب الآن <i class="fas fa-check ms-2"></i>
                    </button>
                    
                    <p class="text-center mt-3 text-muted small">
                        <i class="fas fa-lock me-1"></i> جميع بياناتك مشفرة وآمنة
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/public_footer.php'; ?>
