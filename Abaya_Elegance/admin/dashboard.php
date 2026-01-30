<?php
// بدء الجلسة للتحقق من تسجيل الدخول
session_start();

// استدعاء ملف الاتصال بقاعدة البيانات
require_once '../config/database.php';

// إنشاء اتصال جديد بقاعدة البيانات
$db = (new Database())->getConnection();

// --- استعلامات الإحصائيات (Statistics Queries) ---
// مصفوفة تحتوي على الأرقام المهمة لعرضها في اللوحة
$stats = [
    // حساب مجموع المبيعات (فقط للطلبات المكتملة 'delivered')
    'sales' => $db->query("SELECT SUM(total_amount) FROM orders WHERE status='delivered'")->fetchColumn() ?: 0,
    
    // حساب عدد الطلبات الكلي
    'orders' => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    
    // حساب عدد العملاء المسجلين (استبعاد المدراء)
    'users' => $db->query("SELECT COUNT(*) FROM users WHERE user_type='customer'")->fetchColumn(),
    
    // حساب عدد المنتجات (العبايات) الموجودة في النظام
    'products' => $db->query("SELECT COUNT(*) FROM abayas")->fetchColumn()
];

// --- الطلبات الحديثة (Recent Orders) ---
// جلب آخر 5 طلبات مرتبة من الأحدث للأقدم
// نستخدم LEFT JOIN لجلب اسم العميل حتى لو تم حذف حسابه لاحقاً
$recent_orders = $db->query("SELECT o.*, u.full_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// استدعاء ملف رأس الصفحة (القوائم والستايل)
include '../includes/header.php';
?>

<div class="container-fluid animate-fade-in">
    <!-- قسم الترحيب (Welcome Header) -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-primary-gradient rounded-4 text-white shadow-sm position-relative overflow-hidden">
        <div class="position-relative z-index-2">
            <!-- عرض اسم المدير من الجلسة (Session) -->
            <h2 class="fw-bold mb-1">أهلاً، <?php echo $_SESSION['admin_name'] ?? 'المدير'; ?> 👋</h2>
            <p class="mb-0 opacity-75">نظرة عامة على أداء المتجر اليوم.</p>
        </div>
        <!-- أيقونة خلفية جمالية -->
        <i class="fas fa-chart-line fa-6x position-absolute top-50 end-0 translate-middle-y opacity-10" style="margin-left: -20px;"></i>
    </div>

    <!-- شبكة الإحصائيات (Stats Grid) -->
    <div class="row g-3 mb-4">
        <?php
        // تعريف بيانات البطاقات (العنوان، القيمة، الأيقونة، اللون) ليسهل عرضها في حلقة تكرار
        $cards = [
            ['title' => 'المبيعات', 'val' => number_format($stats['sales']) . ' ر.س', 'icon' => 'money-bill-wave', 'color' => 'success'],
            ['title' => 'الطلبات', 'val' => $stats['orders'], 'icon' => 'shopping-bag', 'color' => 'primary'],
            ['title' => 'العملاء', 'val' => $stats['users'], 'icon' => 'users', 'color' => 'info'],
            ['title' => 'المنتجات', 'val' => $stats['products'], 'icon' => 'shirt', 'color' => 'warning']
        ];
        // المرور على كل بطاقة وعرضها
        foreach($cards as $c): ?>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <!-- الأيقونة داخل دائرة ملونة -->
                    <div class="rounded-circle bg-soft-<?php echo $c['color']; ?> d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-<?php echo $c['icon']; ?> text-<?php echo $c['color']; ?> fs-4"></i>
                    </div>
                    <!-- البيانات النصية -->
                    <div>
                        <div class="text-muted small fw-bold"><?php echo $c['title']; ?></div>
                        <h4 class="mb-0 fw-bold"><?php echo $c['val']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- قسم الطلبات الحديثة والإجراءات السريعة -->
    <div class="row g-4">
        <!-- جدول آخر الطلبات -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-3">
                    <h5 class="fw-bold mb-0">آخر الطلبات</h5>
                    <a href="orders.php" class="btn btn-sm btn-light rounded-pill px-3">الكل</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light"><tr><th class="ps-3">رقم الطلب</th><th>العميل</th><th>المبلغ</th><th>الحالة</th></tr></thead>
                        <tbody>
                            <?php foreach($recent_orders as $o): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-primary">#<?php echo $o['id']; ?></td>
                                <td><?php echo htmlspecialchars($o['full_name']); ?></td>
                                <td class="fw-bold"><?php echo number_format($o['total_amount'], 2); ?> ر.س</td>
                                <td>
                                    <?php 
                                        // تحديد لون ونص الحالة بناءً على قيمتها في قاعدة البيانات
                                        $s = $o['status'];
                                        $cls = $s=='delivered'?'success':($s=='cancelled'?'danger':($s=='shipped'?'primary':'warning'));
                                        $txt = $s=='delivered'?'مكتمل':($s=='cancelled'?'ملغي':($s=='shipped'?'مشحون':'قيد المعالجة'));
                                    ?>
                                    <span class="badge bg-soft-<?php echo $cls; ?> text-<?php echo $cls; ?>"><?php echo $txt; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <!-- رسالة في حال عدم وجود طلبات -->
                            <?php if(empty($recent_orders)) echo '<tr><td colspan="4" class="text-center py-4 text-muted">لا توجد طلبات حديثة</td></tr>'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- الإجراءات السريعة (Quick Actions) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 p-3"><h5 class="fw-bold mb-0">روابط سريعة</h5></div>
                <div class="card-body p-3">
                    <div class="d-grid gap-3">
                        <!-- زر إضافة عباية -->
                        <a href="abayas.php" class="btn btn-outline-primary d-flex align-items-center justify-content-between p-3 rounded-3">
                            <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i> إضافة عباية جديدة</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <!-- زر إضافة تصنيف -->
                        <a href="categories.php" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-3 rounded-3">
                            <span class="fw-bold"><i class="fas fa-tags me-2"></i> إدارة التصنيفات</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <!-- زر إدارة الموظفين -->
                        <a href="users.php" class="btn btn-outline-info d-flex align-items-center justify-content-between p-3 rounded-3">
                            <span class="fw-bold"><i class="fas fa-users-cog me-2"></i> صلاحيات الموظفين</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// استدعاء الفوتر
include '../includes/footer.php'; 
?>
