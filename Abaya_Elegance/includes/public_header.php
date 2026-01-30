<?php
// بدء الجلسة إذا لم تكن مفعلة
if(session_status() === PHP_SESSION_NONE) session_start();

// التحقق من أن كلاس قاعدة البيانات لم يتم تضمينه مسبقاً
if(!class_exists('Database')){
    require_once 'config/database.php';
}

// --- جلب إعدادات الموقع العامة (الشعار والاسم) ---
if(!isset($settings)){
    $database = new Database();
    $db = $database->getConnection();
    // جلب الإعدادات من جدول settings
    $query = "SELECT * FROM settings WHERE id = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
}

// إعداد القيم الافتراضية في حال عدم وجود إعدادات
$site_name = $settings['site_name'] ?? 'هَــ عبايه';
$logo = isset($settings['logo_url']) && !empty($settings['logo_url']) ? $settings['logo_url'] : 'assets/images/official_logo.jpg';

// --- حساب عدد العناصر في السلة ---
$cart_count = 0;
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <!-- ضمان التجاوب مع جميع الأجهزة (موبايل، تابلت، ديسكتوب) ومنع التكبير عند النقر المزدوج -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($site_name); ?></title>
    
    <!-- مكتبة بوتستراب 5 النسخة العربية -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- مكتبة أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- مكتبة Animate.css للحركات البصرية -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- مكتبة Swiper للسلايدرات -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- ملف الستايل المخصص للموقع العام -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-white">

<!-- 1. شريط التنقل للكمبيوتر (يخفى في الموبايل) -->
<nav class="navbar-zahraah d-none d-lg-block sticky-top">
    <div class="container-fluid px-5"> <!-- حاوية عريضة للشاشات الكبيرة -->
        <div class="row align-items-center">
            
            <!-- القسم الأيمن: شريط البحث -->
            <div class="col-4">
                <form action="shop.php" method="GET" class="search-container-desktop">
                    <input type="text" name="search" class="search-input-desktop" placeholder="ابحث عن ما تحتاجه هنا...">
                    <button type="submit" class="search-icon-desktop">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- القسم الأوسط: الشعار واسم الموقع -->
            <div class="col-4 text-center">
                <a href="index.php" class="brand-center-desktop">
                    <img src="assets/images/official_logo.jpg" alt="Logo" class="main-logo-img">
                    <div class="brand-text">
                        <?php echo htmlspecialchars($site_name); ?>
                        <span>ABAYA STORE</span>
                    </div>
                </a>
            </div>

            <!-- القسم الأيسر: الأيقونات وتسجيل الدخول -->
            <div class="col-4">
                <div class="nav-left-desktop">
                    
                    <!-- أيقونة السلة مع العداد -->
                    <a href="cart.php" class="nav-item-icon position-relative">
                        <i class="fas fa-shopping-bag"></i>
                        <?php if($cart_count > 0): ?>
                            <!-- شارة حمراء لعدد العناصر -->
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; transform: translate(-50%, 0);">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                        <span>السلة</span>
                    </a>

                    <!-- التحكم بتسجيل الدخول -->
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <!-- زر تسجيل الدخول للزوار -->
                        <a href="login.php" class="btn-login-pink">تسجيل الدخول</a>
                    <?php else: ?>
                        <!-- قائمة المستخدم المنسدلة عند تسجيل الدخول -->
                        <div class="dropdown d-inline-block ms-3">
                            <a href="#" class="nav-item-icon dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="far fa-user"></i>
                                <span>حسابي</span>
                            </a>
                             <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                <li><a class="dropdown-item" href="profile.php">الملف الشخصي</a></li>
                                <li><a class="dropdown-item" href="my_orders.php">طلباتي</a></li>
                                <!-- رابط لوحة التحكم للمدير فقط -->
                                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/dashboard.php">لوحة التحكم</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">تسجيل خروج</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</nav>

<!-- 2. رأس الصفحة للموبايل (يظهر فقط في الشاشات الصغيرة) -->
<header class="mobile-header d-lg-none sticky-top">
    <!-- أيقونة البحث يساراً -->
    <a href="shop.php" class="mobile-icon">
        <i class="fas fa-search"></i>
    </a>
    
    <!-- الشعار في الوسط -->
    <a href="index.php" class="mobile-logo text-decoration-none">
        <img src="assets/images/official_logo.jpg" alt="Logo" class="mobile-logo-img">
        <span class="ms-2"><?php echo htmlspecialchars($site_name); ?></span>
    </a>
    
    <!-- أيقونة التنبيهات يميناً -->
    <button class="mobile-icon">
        <i class="far fa-bell"></i>
    </button>
</header>

<!-- 3. شريط التنقل السفلي للموبايل (Bottom Navigation) -->
<nav class="bottom-nav d-lg-none">
    <!-- الرابط الرئيسي -->
    <a href="index.php" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>الرئيسية</span>
    </a>
    
    <!-- رابط التصنيفات -->
    <a href="categories.php" class="bottom-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
        <i class="fas fa-th-large"></i>
        <span>الفئات</span>
    </a>
    
    <!-- رابط السلة -->
    <a href="cart.php" class="bottom-nav-item position-relative <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">
        <i class="fas fa-shopping-bag"></i>
        <!-- عداد السلة -->
        <?php if($cart_count > 0): ?>
            <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; margin-left: 10px; margin-top: 5px;">
                <?php echo $cart_count; ?>
            </span>
        <?php endif; ?>
        <span>السلة</span>
    </a>
    
    <!-- رابط الحساب -->
    <a href="<?php echo isset($_SESSION['user_id']) ? 'profile.php' : 'login.php'; ?>" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php' || basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">
        <i class="far fa-user"></i>
        <span>حسابي</span>
    </a>
</nav>
