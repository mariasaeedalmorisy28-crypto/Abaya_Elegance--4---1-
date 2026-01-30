<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Fetch Order Details
$query = "SELECT orders.*, users.full_name, users.phone, users.email 
          FROM orders 
          JOIN users ON orders.user_id = users.id 
          WHERE orders.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "الطلب غير موجود";
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

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4 animate__animated animate__fadeIn">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="text-secondary fw-bold">تفاصيل الطلب #<?php echo htmlspecialchars($order['order_number']); ?></h2>
            <a href="orders.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i> عودة للطلبات
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Info -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-bag text-primary me-2"></i> المنتجات المطلوبة</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">المنتج</th>
                                    <th class="text-center">الكمية</th>
                                    <th>سعر الوحدة</th>
                                    <th class="text-end pe-4">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="../<?php echo $item['main_image']; ?>" class="rounded me-3" width="60" height="70" style="object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                <small class="text-muted">ID: <?php echo $item['abaya_id']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fs-5"><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price'], 2); ?> ر.س</td>
                                    <td class="text-end pe-4 fw-bold"><?php echo number_format($item['subtotal'], 2); ?> ر.س</td>
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

        <!-- Customer & Shipping Info -->
        <div class="col-lg-4">
            <!-- Customer Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user text-primary me-2"></i> تفاصيل العميل</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle p-3 me-3">
                            <i class="fas fa-user fa-lg text-secondary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($order['full_name']); ?></h6>
                            <small class="text-muted">عميل مسجل</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-envelope text-muted me-2" style="width: 20px;"></i> <?php echo htmlspecialchars($order['email']); ?></li>
                        <li class="mb-2"><i class="fas fa-phone text-muted me-2" style="width: 20px;"></i> <?php echo htmlspecialchars($order['phone']); ?></li>
                        <li><i class="fas fa-clock text-muted me-2" style="width: 20px;"></i> <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Shipping Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-truck text-primary me-2"></i> معلومات التوصيل</h5>
                </div>
                <div class="card-body">
                    <p class="fw-bold mb-1">عنوان الشحن:</p>
                    <p class="text-muted bg-light p-3 rounded border">
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                    </p>
                    
                    <?php if($order['notes']): ?>
                    <p class="fw-bold mb-1">ملاحظات العميل:</p>
                    <p class="text-muted bg-light p-3 rounded border border-warning">
                        <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                    </p>
                    <?php endif; ?>

                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">حالة الطلب:</span>
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

                    <button class="btn btn-primary w-100" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> طباعة الفاتورة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
