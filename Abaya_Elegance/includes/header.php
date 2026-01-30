<!DOCTYPE html>
<!-- تعريف اللغة العربية واتجاه النص من اليمين لليسار -->
<html lang="ar" dir="rtl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <!-- ضمان التجاوب مع جميع الأجهزة -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>هَــ عبايه | لوحة التحكم الفاخرة</title>
    
    <!-- مكتبة بوتستراب 5 النسخة العربية (RTL) لتصميم الهيكل -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- مكتبة أيقونات Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- الخطوط العربية (تجوّل) والإنجليزية (Jost) -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- مكتبة Chart.js للرسوم البيانية والإحصائيات -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ملف الستايل الخاص بلوحة التحكم (تصميم الزجاج والمظهر الداكن) -->
    <link rel="stylesheet" href="../assets/css/admin_premium.css">
    
    <!-- مكتبة التنبيهات الجمالية SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- --- القائمة الجانبية (Sidebar) --- -->
    <nav class="sidebar shadow-lg" id="sidebar">
        <!-- شعار لوحة التحكم -->
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand d-block text-decoration-none">
                <div class="brand-haa">هَــ</div>
                <div class="brand-text-sub">عبايه</div>
            </a>
        </div>
        
        <!-- روابط القائمة -->
        <ul class="sidebar-menu mt-4">
            <!-- الرابط الرئيسي -->
            <li>
                <!-- التحقق من الصفحة الحالية لتفعيل الرابط (Active Class) -->
                <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i> <span>الرئيسية</span>
                </a>
            </li>
            
            <div class="sidebar-label">إدارة المتجر</div>
            <!-- رابط التصنيفات -->
            <li>
                <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                    <i class="fas fa-layer-group"></i> <span>التصنيفات</span>
                </a>
            </li>
            <!-- رابط المنتجات (العبايات) -->
            <li>
                <a href="abayas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'abayas.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tshirt"></i> <span>المنتجات</span>
                </a>
            </li>
            <!-- رابط الطلبات -->
            <li>
                <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> <span>الطلبات</span>
                </a>
            </li>
            
            <div class="sidebar-label">النظام</div>
            <!-- رابط المستخدمين والمسؤولين -->
            <li>
                <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i> <span>المسؤولين</span>
                </a>
            </li>
            
            <!-- زر تسجيل الخروج -->
            <li class="mt-5">
                <a href="#" onclick="confirmLogout()" class="text-danger-hover">
                    <i class="fas fa-power-off"></i> <span>خروج</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- طبقة شفافة لتغطية المحتوى عند فتح القائمة في الجوال -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div> 
    <!-- --- نهاية القائمة الجانبية --- -->

    <!-- --- محتوى الصفحة الرئيسي --- -->
    <div class="main-content w-100" id="content">
        <!-- الشريط العلوي (Top Navigation) -->
        <div class="d-flex justify-content-between align-items-center mb-5 animate-fade-in">
            <div class="d-flex align-items-center">
                <!-- زر القائمة للجوال -->
                <div class="mobile-toggle d-lg-none me-3 cursor-pointer" onclick="toggleSidebar()">
                    <i class="fas fa-bars fs-4 text-white"></i>
                </div>
                <!-- عنوان الصفحة المتغير بناءً على الملف الحالي -->
                <h4 class="fw-900 text-white mb-0" style="letter-spacing: -1px;">
                    <?php 
                        $page = basename($_SERVER['PHP_SELF']);
                        switch($page) {
                            case 'dashboard.php': echo 'لوحة التحكم'; break;
                            case 'abayas.php': echo 'إدارة العبايات'; break;
                            case 'categories.php': echo 'التصنيفات'; break;
                            case 'orders.php': echo 'الطلبات'; break;
                            case 'users.php': echo 'المستخدمين'; break;
                            default: echo 'النظام';
                        }
                    ?>
                </h4>
            </div>
            
            <!-- الجزء الأيسر من الشريط (البحث والبروفايل) -->
            <div class="d-flex align-items-center gap-3">
                <!-- مربع البحث (يظهر فقط في الشاشات المتوسطة والكبيرة) -->
                <div class="glass-search d-none d-md-flex">
                    <i class="fas fa-search text-muted ms-2"></i>
                    <input type="text" placeholder="بحث سريع..." class="bg-transparent border-0 text-white small" style="outline: none;">
                </div>
                <!-- معاينة الملف الشخصي للمدير -->
                <div class="profile-preview d-flex align-items-center gap-3 p-2 px-3 rounded-pill" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                    <div class="text-end d-none d-sm-block">
                        <div class="text-white small fw-bold"><?php echo $_SESSION['admin_name'] ?? 'المهندسة ماريا'; ?></div>
                        <div class="text-muted" style="font-size: 0.6rem;">متصل الآن</div>
                    </div>
                    <!-- صورة البروفايل (تلقائية بناءً على الاسم) -->
                    <img src="https://ui-avatars.com/api/?name=Admin&background=eb6b7e&color=fff" class="rounded-circle" width="35">
                </div>
            </div>
        </div>
