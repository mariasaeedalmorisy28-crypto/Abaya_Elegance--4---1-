<?php
// بدء الجلسة
session_start();
require_once '../config/database.php';
$db = (new Database())->getConnection();

// --- التعامل مع المدخلات (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    // تحديد معرف العباية إذا كان تعديلاً
    $id = $data['id'] ?? null;
    
    // دالة مساعدة لرفع الصور
    // تأخذ الملف والمسار، وتتحقق من الأخطاء، ثم تنقل الملف وتعيد المسار الجديد
    $upload = function($file, $dest) {
        if ($file['error'] === 0) {
            // التحقق من نوع الملف (يجب أن يكون صورة)
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = mime_content_type($file['tmp_name']);
            
            if (!in_array($file_type, $allowed_types)) {
                return null;
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            // تسمية فريدة للملف لتجنب التكرار
            $name = uniqid() . '.' . $ext;
            // إنشاء المجلد إذا لم يكن موجوداً
            if (!is_dir($dest)) mkdir($dest, 0777, true);
            // نقل الملف وإرجاع المسار النسبي
            if (move_uploaded_file($file['tmp_name'], $dest . $name)) return "assets/images/abayas/" . $name;
        }
        return null;
    };

    // معالجة الإضافة والتعديل
    if ($data['action'] === 'add' || $data['action'] === 'edit') {
        // محاولة رفع الصورة الرئيسية
        $main_img = $upload($_FILES['main_image'] ?? [], "../assets/images/abayas/");
        
        // تجهيز استعلام SQL بناءً على الإجراء (إضافة أو تعديل)
        // في التعديل، نحدث الصورة فقط إذا تم رفع صورة جديدة
        $sql = $data['action'] === 'add' 
            ? "INSERT INTO abayas (name, description, category_id, price, old_price, color, size, material, stock_quantity, main_image) VALUES (:name, :desc, :cat, :price, :old, :col, :size, :mat, :stock, :img)"
            : "UPDATE abayas SET name=:name, description=:desc, category_id=:cat, price=:price, old_price=:old, color=:col, size=:size, material=:mat, stock_quantity=:stock " . ($main_img ? ", main_image=:img" : "") . " WHERE id=:id";
        
        // ربط البيانات بالاستعلام
        $params = [
            ':name' => $data['name'], ':desc' => $data['description'], ':cat' => $data['category_id'],
            ':price' => $data['price'], ':old' => $data['old_price'] ?: null, ':col' => $data['color'],
            ':size' => $data['size'], ':mat' => $data['material'], ':stock' => $data['stock_quantity']
        ];
        if ($main_img) $params[':img'] = $main_img;
        if ($id) $params[':id'] = $id;

        // تنفيذ الاستعلام
        $db->prepare($sql)->execute($params);
        // الحصول على معرف المنتج (إما الجديد أو المعدل) لربط صور المعرض به
        $pid = $id ?: $db->lastInsertId();

        // معالجة صور المعرض (Gallery)
        if (!empty($_FILES['gallery']['name'][0])) {
            foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp) {
                // رفع كل صورة وإضافتها لجدول product_images
                if ($path = $upload(['name'=>$_FILES['gallery']['name'][$k], 'tmp_name'=>$tmp, 'error'=>$_FILES['gallery']['error'][$k]], "../assets/images/abayas/")) {
                    $db->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)")->execute([$pid, $path]);
                }
            }
        }
        // العودة للصفحة
        header("Location: abayas.php"); exit;
    }
}

// --- منطق الحذف ---
// 1. حذف العباية بالكامل
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM abayas WHERE id = ?")->execute([$_GET['delete']]);
    header("Location: abayas.php"); exit;
}
// 2. حذف صورة معينة من المعرض
if (isset($_GET['del_img'])) {
    // جلب مسار الصورة لحذف الملف الفعلي من الخادم
    $path = $db->query("SELECT image_path FROM product_images WHERE id=".$_GET['del_img'])->fetchColumn();
    // حذف الملف إذا وجد
    if ($path && file_exists("../$path")) unlink("../$path");
    // حذف السجل من قاعدة البيانات
    $db->prepare("DELETE FROM product_images WHERE id = ?")->execute([$_GET['del_img']]);
    header("Location: abayas.php"); exit;
}

// --- جلب البيانات للعرض ---
// جلب الاقسام للقائمة المنسدلة
$cats = $db->query("SELECT * FROM categories")->fetchAll();
// جلب العبايات مع اسم التصنيف (LEFT JOIN)
$abayas = $db->query("SELECT a.*, c.name as cname FROM abayas a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.id DESC")->fetchAll(PDO::FETCH_ASSOC);
// جلب صور المعرض وتجميعها حسب معرف المنتج لتسهيل العرض
$gallery = [];
foreach($db->query("SELECT * FROM product_images") as $img) $gallery[$img['product_id']][] = $img;

include '../includes/header.php';
?>

<div class="container-fluid animate-fade-in">
    <div class="card p-0 border-0 bg-transparent">
        <div class="d-flex justify-content-between align-items-center p-4">
            <div><h5 class="fw-bold text-white mb-1">إدارة العبايات</h5><p class="text-muted small mb-0">المخزون والمنتجات</p></div>
            <!-- زر فتح نافذة الإضافة -->
            <button class="btn btn-primary rounded-pill px-4" onclick="openModal()"><i class="fas fa-plus me-1"></i> إضافة عباية</button>
        </div>
        <div class="table-responsive">
            <table class="table premium-table align-middle mb-0">
                <thead><tr><th class="ps-4">المنتج</th><th>القسم</th><th>السعر</th><th>المخزون</th><th class="text-center">تحكم</th></tr></thead>
                <tbody>
                    <?php foreach($abayas as $a): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <!-- عرض الصورة الرئيسية أو صورة افتراضية -->
                                <img src="../<?php echo $a['main_image'] ? $a['main_image'] : 'assets/images/no-img.png'; ?>" class="rounded-3 object-fit-cover" width="50" height="65">
                                <div><div class="fw-bold text-white"><?php echo $a['name']; ?></div><small class="text-muted">#<?php echo $a['id']; ?></small></div>
                            </div>
                        </td>
                        <td><span class="badge bg-glass text-light fw-normal"><?php echo $a['cname']; ?></span></td>
                        <td><span class="text-primary fw-bold"><?php echo $a['price']; ?> ر.س</span></td>
                        <td>
                            <!-- حالة المخزون ملونة حسب الكمية -->
                            <span class="badge <?php echo $a['stock_quantity'] < 5 ? 'bg-danger' : 'bg-success'; ?> rounded-pill">
                                <?php echo $a['stock_quantity']; ?> <?php echo $a['stock_quantity'] < 5 ? 'منخفض' : 'متوفر'; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- زر التعديل يفتح النافذة ويمرر بيانات المنتج كـ JSON -->
                                <button onclick='openModal(<?php echo json_encode($a + ['gallery' => $gallery[$a['id']] ?? []]); ?>)' class="btn btn-sm btn-icon bg-glass text-white border-0"><i class="fas fa-pen"></i></button>
                                <!-- زر الحذف المباشر -->
                                <a href="abayas.php?delete=<?php echo $a['id']; ?>" class="btn btn-sm btn-icon bg-glass text-danger border-0"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- النافذة الموحدة للإضافة والتعديل (Unified Modal) -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" enctype="multipart/form-data">
                <!-- حقول مخفية لتحديد نوع العملية (إضافة/تعديل) ومعرف المنتج -->
                <input type="hidden" name="action" id="m_action" value="add">
                <input type="hidden" name="id" id="m_id">
                
                <div class="modal-header border-0 pb-0"><h5 class="fw-bold" id="m_title">عباية جديدة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8"><label>اسم العباية</label><input type="text" name="name" id="m_name" class="form-control" required></div>
                        <div class="col-md-4"><label>القسم</label><select name="category_id" id="m_cat" class="form-select"><?php foreach($cats as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?></select></div>
                        <div class="col-md-4"><label>السعر</label><input type="number" step="0.01" name="price" id="m_price" class="form-control" required></div>
                        <div class="col-md-4"><label>سعر قديم</label><input type="number" step="0.01" name="old_price" id="m_old" class="form-control"></div>
                        <div class="col-md-4"><label>المخزون</label><input type="number" name="stock_quantity" id="m_stock" class="form-control" value="1"></div>
                        <div class="col-md-4"><label>اللون</label><input type="text" name="color" id="m_color" class="form-control"></div>
                        <div class="col-md-4"><label>المقاس</label><select name="size" id="m_size" class="form-select"><option>S</option><option>M</option><option>L</option><option>XL</option><option>XXL</option></select></div>
                        <div class="col-md-4"><label>الخامة</label><input type="text" name="material" id="m_material" class="form-control"></div>
                        <div class="col-12"><label>الوصف</label><textarea name="description" id="m_desc" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label>الصورة الرئيسية</label><input type="file" name="main_image" class="form-control" accept="image/*" onchange="validateImage(this)"></div>
                        <div class="col-md-6"><label>المعرض</label><input type="file" name="gallery[]" class="form-control" multiple accept="image/*" onchange="validateImage(this)"></div>
                        <!-- منطقة عرض الصور الموجودة عند التعديل -->
                        <div class="col-12" id="gallery_preview"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary w-100 rounded-pill">حفظ</button></div>
            </form>
        </div>
    </div>
</div>

<script>
// دالة فتح النافذة
// إذا تم تمرير بيانات (data)، فهي عملية تعديل، وإلا فهي إضافة
function openModal(data = null) {
    // تعيين نوع العملية
    document.getElementById('m_action').value = data ? 'edit' : 'add';
    document.getElementById('m_id').value = data ? data.id : '';
    document.getElementById('m_title').innerText = data ? 'تعديل العباية' : 'إضافة عباية';
    
    // تعبئة الحقول تلقائياً في حالة التعديل
    const fields = ['name', 'price', 'color', 'material', 'stock', 'desc', 'cat', 'size'];
    fields.forEach(f => document.getElementById('m_' + f).value = data ? (data[f === 'desc' ? 'description' : (f === 'cat' ? 'category_id' : (f === 'stock' ? 'stock_quantity' : f))] || '') : '');
    document.getElementById('m_old').value = data ? (data.old_price || '') : '';

    // عرض صور المعرض مع زر للحذف
    const galDiv = document.getElementById('gallery_preview');
    galDiv.innerHTML = '';
    if (data && data.gallery) {
        data.gallery.forEach(img => {
            galDiv.innerHTML += `<div class="d-inline-block position-relative me-2 mt-2"><img src="../${img.image_path}" class="rounded" width="50"><a href="abayas.php?del_img=${img.id}" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-decoration-none">x</a></div>`;
        });
    }

    // إظهار النافذة
    new bootstrap.Modal(document.getElementById('productModal')).show();
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
