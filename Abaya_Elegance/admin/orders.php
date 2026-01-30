<?php
// بدء الجلسة
session_start();

// الاتصال بقاعدة البيانات
require_once '../config/database.php';
$db = (new Database())->getConnection();

// --- التعامل مع الإجراءات المباشرة ---

// 1. حذف الطلب
if (isset($_GET['delete'])) {
    // تنفيذ الحذف مباشرة بناءً على معرف الطلب
    $db->prepare("DELETE FROM orders WHERE id = :id")->execute([':id' => $_GET['delete']]);
    // تحديث الصفحة
    header("Location: orders.php"); exit();
}

// 2. تحديث حالة الطلب
if (isset($_POST['update_status'])) {
    // تحديث الحالة في قاعدة البيانات بناءً على القيمة المختارة
    $db->prepare("UPDATE orders SET status = :status WHERE id = :id")
       ->execute([':status' => $_POST['status'], ':id' => $_POST['order_id']]);
    // تحديث الصفحة
    header("Location: orders.php"); exit();
}

// --- جلب البيانات ---
// استعلام لجلب كافة الطلبات مع دمج جدول المستخدمين للحصول على الاسم ورقم الجوال
// استخدام JOIN يضمن ربط الطلب بصاحبه
$orders = $db->query("SELECT orders.*, users.full_name, users.phone FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid animate-fade-in">
    <div class="card p-0 overflow-hidden border-0">
        <!-- ترويسة الصفحة -->
        <div class="card-header bg-transparent border-0 p-4">
            <h5 class="mb-0 fw-bold text-white">إدارة الطلبات</h5>
            <p class="text-muted small mb-0 mt-1">تحديث الحالات ومتابعة الطلبات بسرعة</p>
        </div>
        
        <!-- جدول الطلبات -->
        <div class="table-responsive">
            <table class="table premium-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">رقم الطلب</th>
                        <th>العميل</th>
                        <th>السعر</th>
                        <th>الحالة (تحديث مباشر)</th>
                        <th>التاريخ</th>
                        <th class="text-center">تحكم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orders)): ?>
                    <!-- رسالة عند عدم وجود طلبات -->
                    <tr><td colspan="6" class="text-center py-5 text-muted">لا توجد طلبات حالياً</td></tr>
                    <?php else: ?>
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#<?php echo $order['id']; ?></td>
                        <td>
                            <div class="text-white fw-bold"><?php echo htmlspecialchars($order['full_name']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($order['phone']); ?></div>
                        </td>
                        <td class="fw-bold"><?php echo number_format($order['total_amount'], 2); ?> ر.س</td>
                        
                        <!-- قائمة منسدلة لتحديث الحالة مباشرة -->
                        <td style="min-width: 160px;">
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="update_status" value="1">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <!-- عند تغيير القيمة (onchange) يتم إرسال الفورم تلقائياً -->
                                <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()" style="font-size: 0.85rem;">
                                    <option value="pending" <?php echo $order['status']=='pending'?'selected':''; ?>>انتظار</option>
                                    <option value="processing" <?php echo $order['status']=='processing'?'selected':''; ?>>جاري التجهيز</option>
                                    <option value="shipped" <?php echo $order['status']=='shipped'?'selected':''; ?>>تم الشحن</option>
                                    <option value="delivered" <?php echo $order['status']=='delivered'?'selected':''; ?>>تم التوصيل</option>
                                    <option value="cancelled" <?php echo $order['status']=='cancelled'?'selected':''; ?>>ملغي</option>
                                </select>
                            </form>
                        </td>
                        
                        <td class="small text-muted"><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- زر عرض التفاصيل الفاتورة -->
                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-icon bg-glass text-white border-0" title="عرض التفاصيل"><i class="fas fa-eye"></i></a>
                                <!-- زر الحذف -->
                                <a href="orders.php?delete=<?php echo $order['id']; ?>" class="btn btn-sm btn-icon bg-glass text-danger border-0" title="حذف نهائي"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
