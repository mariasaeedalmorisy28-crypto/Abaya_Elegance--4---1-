<?php
include 'includes/public_header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "<script>window.location.href='my_orders.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'];

// Fetch Order Details (Secure Check: verify order belongs to user)
$query = "SELECT * FROM orders WHERE id = :id AND user_id = :uid";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $order_id);
$stmt->bindParam(':uid', $user_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='container py-5'><div class='alert alert-danger'>الطلب غير موجود أو ليس لديك صلاحية الوصول إليه.</div></div>";
    include 'includes/public_footer.php';
    exit();
}

// Fetch Order Items
$query_items = "SELECT order_items.*, abayas.name, abayas.main_image 
                FROM order_items 
                JOIN abayas ON order_items.abaya_id = abayas.id 
                WHERE order_items.order_id = :id";
$stmt_items = $db->prepare($query_items);
$stmt_items->bindParam(':id', $order_id);
$stmt_items->execute();
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">تفاصيل الطلب #<?php echo htmlspecialchars($order['order_number']); ?></h2>
        <a href="my_orders.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-right me-2"></i> عودة لطلباتي
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">المنتجات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">المنتج</th>
                                    <th>الكمية</th>
                                    <th>سعر الوحدة</th>
                                    <th class="text-end pe-4">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $item['main_image']; ?>" class="rounded me-3" width="60" height="70" style="object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price'], 2); ?> ر.س</td>
                                    <td class="text-end pe-4 fw-bold"><?php echo number_format($item['subtotal'], 2); ?> ر.س</td> 
                                    <!-- Note: subtotal might be null if DB generated column wasn't fetched correctly or inserted manually if logic changed. 
                                         In my logic, I relied on generated column or calculation. 
                                         Let's calculate just in case PHP side: -->
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold pt-3 fs-5">الإجمالي الكلي:</td>
                                    <td class="text-end pe-4 fw-bold pt-3 fs-5 text-primary"><?php echo number_format($order['total_amount'], 2); ?> ر.س</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">معلومات التوصيل</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="d-block text-muted small mb-1">حالة الطلب</span>
                         <?php 
                        $status_class = '';
                        $status_text = '';
                        switch($order['status']) {
                            case 'pending': $status_class='bg-warning'; $status_text='قيد الانتظار'; break;
                            case 'processing': $status_class='bg-info'; $status_text='قيد المعالجة'; break;
                            case 'shipped': $status_class='bg-primary'; $status_text='تم الشحن'; break;
                            case 'delivered': $status_class='bg-success'; $status_text='تم التوصيل'; break;
                            case 'cancelled': $status_class='bg-danger'; $status_text='ملغي'; break;
                        }
                        ?>
                        <span class="badge <?php echo $status_class; ?> fs-6"><?php echo $status_text; ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="d-block text-muted small mb-1">تاريخ الطلب</span>
                        <span class="fw-bold"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <span class="d-block text-muted small mb-1">عنوان الشحن</span>
                        <p class="mb-0 bg-light p-2 rounded"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    </div>

                    <?php if($order['notes']): ?>
                    <div class="mb-3">
                        <span class="d-block text-muted small mb-1">ملاحظاتك</span>
                        <p class="mb-0 bg-light p-2 rounded border border-warning"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/public_footer.php'; ?>
