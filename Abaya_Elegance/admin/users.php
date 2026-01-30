<?php
// بدء الجلسة للتحقق من الصلاحيات
session_start();

// استدعاء ملف الاتصال بقاعدة البيانات
require_once '../config/database.php';
// إنشاء كائن الاتصال
$db = (new Database())->getConnection();

// --- التعامل مع الإجراءات (حذف / ترقية) ---
// التحقق من وجود طلب حذف مستخدم
if (isset($_GET['delete'])) {
    // تجهيز استعلام الحذف وتنفيذه فوراً (بدون صفحة تأكيد إضافية)
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    // إعادة التوجيه لنفس الصفحة لتحديث القائمة
    header("Location: users.php"); exit();
}

// التحقق من وجود طلب ترقية/تنزيل رتبة
if (isset($_GET['promote'])) {
    // استعلام ذكي لتبديل الحالة: إذا كان عميل يصبح مديراً والعكس
    $db->prepare("UPDATE users SET user_type = CASE WHEN user_type = 'customer' THEN 'admin' ELSE 'customer' END WHERE id = :id")
       ->execute([':id' => $_GET['promote']]);
    // إعادة التوجيه
    header("Location: users.php"); exit();
}

// --- جلب بيانات المستخدمين ---
// استعلام لجلب كافة المستخدمين مرتبين من الأحدث للأقدم
$users = $db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// استدعاء الهيدر
include '../includes/header.php';
?>

<div class="container-fluid animate-fade-in">
    <div class="card p-0 overflow-hidden border-0">
        <!-- ترويسة الجدول -->
        <div class="card-header bg-transparent border-0 p-4">
            <h5 class="mb-0 fw-bold text-white">إدارة الحسابات</h5>
            <p class="text-muted small mb-0 mt-1">التحكم السريع بالمستخدمين</p>
        </div>
        
        <!-- جدول عرض المستخدمين -->
        <div class="table-responsive">
            <table class="table premium-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">المستخدم</th>
                        <th>البريد</th>
                        <th>الرتبة</th>
                        <th>التاريخ</th>
                        <th class="text-center">تحكم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // حلقة تكرار لعرض كل مستخدم في صف
                    foreach($users as $user): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <!-- عرض أول حرف من الاسم كأيقونة -->
                                <div class="avatar-sm rounded-circle bg-glass d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                    <?php echo strtoupper(mb_substr($user['full_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-white"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                    <div class="text-muted small">#<?php echo $user['id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <!-- تمييز المدير بلون مختلف -->
                            <span class="badge <?php echo $user['user_type'] == 'admin' ? 'bg-primary' : 'bg-glass text-muted'; ?> px-3 rounded-pill">
                                <?php echo $user['user_type'] == 'admin' ? 'مدير' : 'عميل'; ?>
                            </span>
                        </td>
                        <td class="small text-muted"><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- زر الترقية/التنزيل -->
                                <a href="users.php?promote=<?php echo $user['id']; ?>" class="btn btn-sm btn-icon bg-glass text-warning border-0" title="ترقية/تنزيل"><i class="fas fa-shield-halved"></i></a>
                                <!-- زر الحذف المباشر -->
                                <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-icon bg-glass text-danger border-0" title="حذف مباشر"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
