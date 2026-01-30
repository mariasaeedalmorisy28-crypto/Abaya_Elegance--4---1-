<?php
include 'includes/public_header.php';

// Build Query based on filters
$where_clauses = [];
$params = [];

// Search Filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where_clauses[] = "(name LIKE :search OR description LIKE :search)";
    $params[':search'] = "%" . $_GET['search'] . "%";
}

// Category Filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $where_clauses[] = "category_id = :category";
    $params[':category'] = $_GET['category'];
}

// Price Filter
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 10000;
if (isset($_GET['min_price']) || isset($_GET['max_price'])) {
     $where_clauses[] = "price BETWEEN :min AND :max";
     $params[':min'] = $min_price;
     $params[':max'] = $max_price;
}

$sql = "SELECT * FROM abayas";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Sorting
$sort = $_GET['sort'] ?? 'newest';
switch ($sort) {
    case 'price_low': $sql .= " ORDER BY price ASC"; break;
    case 'price_high': $sql .= " ORDER BY price DESC"; break;
    default: $sql .= " ORDER BY created_at DESC"; break;
}

$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Categories for Sidebar
$cat_stmt = $db->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-secondary text-decoration-none">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">التسوق</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 1020;"> <!-- z-index adjusted to be below navbar -->
                <div class="card-header py-3" style="background: #111;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-filter text-primary me-2"></i> تصفية المنتجات</h5>
                </div>
                <div class="card-body">
                    <form action="shop.php" method="GET">
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">بحث</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="اسم العباية..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">التصنيفات</label>
                            <?php foreach($categories as $cat): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" value="<?php echo $cat['id']; ?>" id="cat_<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="cat_<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="category" value="" id="cat_all" <?php echo (!isset($_GET['category']) || empty($_GET['category'])) ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label text-muted" for="cat_all">
                                    جميع التصنيفات
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">السعر</label>
                            <div class="d-flex align-items-center">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="من" value="<?php echo $min_price ?: ''; ?>">
                                <span class="mx-2 text-muted">-</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="إلى" value="<?php echo $max_price != 10000 ? $max_price : ''; ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-2">تطبيق الفلتر</button>
                        <a href="shop.php" class="btn btn-outline-secondary w-100 btn-sm">إعادة تعيين</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Top Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded shadow-sm border border-dark" style="background: #111;">
                <span class="text-muted small">عرض <?php echo count($products); ?> منتج</span>
                <form action="shop.php" method="GET" id="sortForm" class="d-flex align-items-center">
                    <!-- Keep current filters hidden -->
                    <?php 
                    foreach($_GET as $key => $val){
                        if($key != 'sort') echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($val).'">';
                    }
                    ?>
                    <label class="me-2 small text-muted text-nowrap">ترتيب حسب:</label>
                    <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('sortForm').submit()">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>الأحدث</option>
                        <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>السعر: من الأقل للأعلى</option>
                        <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>السعر: من الأعلى للأقل</option>
                    </select>
                </form>
            </div>

            <!-- Grid -->
            <?php if(count($products) > 0): ?>
            <div class="row g-4">
                <?php foreach($products as $abaya): ?>
                <div class="col-md-6 col-lg-4 animate__animated animate__fadeInUp">
                    <div class="product-card h-100 shadow-sm">
                        <div class="product-img-wrapper">
                            <!-- Discount Badge -->
                            <?php if($abaya['old_price'] > $abaya['price']): ?>
                                <?php $discount = round((($abaya['old_price'] - $abaya['price']) / $abaya['old_price']) * 100); ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-3 z-1">-<?php echo $discount; ?>%</span>
                            <?php endif; ?>
                            
                            <img src="<?php echo $abaya['main_image']; ?>" alt="<?php echo htmlspecialchars($abaya['name']); ?>">
                            
                            <!-- Overlay -->
                            <div class="overlay-actions">
                                <a href="product.php?id=<?php echo $abaya['id']; ?>" class="action-btn" title="عرض التفاصيل">
                                    <i class="far fa-eye"></i>
                                </a>
                                <div class="action-btn quick-view-trigger cursor-pointer" data-id="<?php echo $abaya['id']; ?>" title="معاينة سريعة">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body text-center p-3">
                            <h6 class="card-title fw-bold my-2 text-truncate">
                                <a href="product.php?id=<?php echo $abaya['id']; ?>" class="text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($abaya['name']); ?>
                                </a>
                            </h6>
                            <div class="price-box">
                                <span class="text-primary fw-bold fs-5"><?php echo number_format($abaya['price'], 2); ?> ر.س</span>
                                <?php if($abaya['old_price']): ?>
                                    <small class="text-muted text-decoration-line-through ms-2"><?php echo number_format($abaya['old_price'], 2); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div class="text-center py-5 rounded shadow-sm border border-dark" style="background: #111;">
                    <i class="fas fa-search fa-3x text-muted mb-3 opacity-25"></i>
                    <h4 class="fw-bold text-muted">لا توجد نتائج مطابقة لبحثك</h4>
                    <p class="text-muted">حاول تغيير خيارات الفلتر أو البحث عن كلمة أخرى</p>
                    <a href="shop.php" class="btn btn-gold mt-3">عرض جميع المنتجات</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/public_footer.php'; ?>
