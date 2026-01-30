<?php
// بدء الجلسة
session_start();
require_once '../config/database.php';
$db = (new Database())->getConnection();

// --- التعامل مع المدخلات (إضافة / تعديل) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    // تحديد معرف التصنيف في حالة التعديل
    $id = $data['id'] ?? null;
    
    // معالجة رفع صورة التصنيف
    $img_path = null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
        // التحقق من نوع الملف (يجب أن يكون صورة)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);
        
        if (in_array($file_type, $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            // تسمية فريدة
            $name = uniqid() . '.' . $ext;
            $dest = "../assets/images/categories/";
            // إنشاء المجلد إذا لم يكن موجوداً
            if (!is_dir($dest)) mkdir($dest, 0777, true);
            // نقل الملف
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest . $name)) {
                $img_path = "assets/images/categories/" . $name;
            }
        }
    }

    // التحقق من نوع الإجراء وتنفيذ الاستعلام المناسب
    if ($data['action'] === 'add' || $data['action'] === 'edit') {
        // إذا كان إضافة: إدراج سجل جديد
        // إذا كان تعديل: تحديث السجل، وتحديث الصورة فقط إذا تم رفع واحدة جديدة
        $sql = $data['action'] === 'add' 
            ? "INSERT INTO categories (name, description, image) VALUES (:name, :desc, :img)"
            : "UPDATE categories SET name=:name, description=:desc " . ($img_path ? ", image=:img" : "") . " WHERE id=:id";
        
        // ربط الباراميترات
        $params = [':name' => $data['name'], ':desc' => $data['description']];
        // في حالة الإضافة أو وجود صورة جديدة، نمرر مسار الصورة
        if ($img_path || $data['action'] === 'add') $params[':img'] = $img_path ?: '';
        // في حالة التعديل، نمرر المعرف
        if ($id) $params[':id'] = $id;

        // تنفيذ الاستعلام
        $db->prepare($sql)->execute($params);
        // تحديث الصفحة
        header("Location: categories.php"); exit;
    }
}

// --- منطق الحذف ---
if (isset($_GET['delete'])) {
    // حذف التصنيف مباشرة بناءً على المعرف
    $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$_GET['delete']]);
    header("Location: categories.php"); exit;
}

// --- جلب البيانات ---
// استعلام لجلب كافة التصنيفات لعرضها في الجدول
$categories = $db->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid animate-fade-in">
    <div class="card p-0 border-0 bg-transparent">
        <div class="d-flex justify-content-between align-items-center p-4">
            <div><h5 class="fw-bold text-white mb-1">إدارة التصنيفات</h5><p class="text-muted small mb-0">تنظيم أقسام المتجر</p></div>
            <!-- زر إضافة تصنيف يفتح النافذة المنبثقة -->
            <button class="btn btn-primary rounded-pill px-4" onclick="openModal()"><i class="fas fa-plus me-1"></i> إضافة تصنيف</button>
        </div>
        <div class="table-responsive">
            <table class="table premium-table align-middle mb-0">
                <thead><tr><th class="ps-4">التصنيف</th><th>الوصف</th><th>التاريخ</th><th class="text-center">تحكم</th></tr></thead>
                <tbody>
                    <?php if(empty($categories)): ?>
                    <tr><td colspan="4" class="text-center py-5 text-muted">لا يوجد تصنيفات</td></tr>
                    <?php else: ?>
                    <?php foreach($categories as $c): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <!-- عرض صورة التصنيف -->
                                <img src="../<?php echo $c['image'] ? $c['image'] : 'assets/images/no-img.png'; ?>" class="rounded-3 object-fit-cover" width="50" height="50">
                                <div><div class="fw-bold text-white"><?php echo htmlspecialchars($c['name']); ?></div><small class="text-muted">#<?php echo $c['id']; ?></small></div>
                            </div>
                        </td>
                        <!-- عرض جزء من الوصف -->
                        <td><small class="text-muted"><?php echo mb_strimwidth($c['description'], 0, 50, '...'); ?></small></td>
                        <td class="small text-muted font-jost"><?php echo date('Y-m-d', strtotime($c['created_at'])); ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- زر التعديل: يمرر كائن البيانات بصيغة JSON للدالة -->
                                <button onclick='openModal(<?php echo json_encode($c); ?>)' class="btn btn-sm btn-icon bg-glass text-white border-0"><i class="fas fa-pen"></i></button>
                                <!-- زر الحذف المباشر -->
                                <a href="categories.php?delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-icon bg-glass text-danger border-0"><i class="fas fa-trash"></i></a>
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

<!-- النافذة الموحدة (Unified Modal) -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" enctype="multipart/form-data">
                <!-- حقول مخفية لتحديد العملية والمعرف -->
                <input type="hidden" name="action" id="m_action" value="add">
                <input type="hidden" name="id" id="m_id">
                <div class="modal-header border-0 pb-0"><h5 class="fw-bold" id="m_title">تصنيف جديد</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label>اسم التصنيف</label><input type="text" name="name" id="m_name" class="form-control" required></div>
                    <div class="mb-3"><label>الوصف</label><textarea name="description" id="m_desc" class="form-control" rows="3"></textarea></div>
                    <div class="mb-3"><label>صورة التصنيف</label><input type="file" name="image" class="form-control" accept="image/*" onchange="validateImage(this)"></div>
                </div>
                <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">حفظ</button></div>
            </form>
        </div>
    </div>
</div>

<script>
// دالة فتح النافذة وتعبئة البيانات عند التعديل
function openModal(data = null) {
    // إذا مررت بيانات، فاجعل الوضع 'edit' وإلا 'add'
    document.getElementById('m_action').value = data ? 'edit' : 'add';
    document.getElementById('m_id').value = data ? data.id : '';
    document.getElementById('m_title').innerText = data ? 'تعديل التصنيف' : 'إضافة تصنيف';
    // تعبئة الحقول
    document.getElementById('m_name').value = data ? data.name : '';
    document.getElementById('m_desc').value = data ? data.description : '';
    // إظهار النافذة باستخدام Bootstrap Modal API
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

// دالة التحقق من الصور في المتصفح
function validateImage(input) {
    const files = input.files;
    for (let i = 0; i < files.length; i++) {
        if (!files[i].type.startsWith('image/')) {
            premiumSwal.fire({
                icon: 'error',
                title: 'خطأ في الملف',
                text: 'لا يمكن اضافة الا صورة فقط ي قلبي',
            });
            input.value = ''; // مسح الاختيار
            return;
        }
    }
}
</script>
<?php include '../includes/footer.php'; ?>
