<?php
include 'includes/public_header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Current User Data
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<style>
    /* Reset & Isolation */
    .acc-wrapper {
        background: #000 !important;
        min-height: 100vh !important;
        direction: rtl !important;
        font-family: 'Cairo', sans-serif !important;
        color: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .acc-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 15px 20px !important;
        background: #000 !important;
        border-bottom: 1px solid #1a1a1a !important;
    }

    .acc-header h5 {
        margin: 0 !important;
        color: #fff !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
    }

    .acc-user-card {
        padding: 30px 20px !important;
        background: #0a0a0a !important;
        border-bottom: 1px solid #111 !important;
        text-align: right !important;
    }

    .acc-user-card h4 {
        margin: 0 0 5px 0 !important;
        color: #fff !important;
        font-size: 1.2rem !important;
        font-weight: 700 !important;
    }

    .acc-user-card p {
        margin: 0 !important;
        color: #666 !important;
        font-size: 0.9rem !important;
    }

    .acc-menu {
        background: #000 !important;
    }

    .acc-menu-item {
        display: flex !important;
        align-items: center !important;
        padding: 18px 20px !important;
        border-bottom: 1px solid #111 !important;
        text-decoration: none !important;
        color: #fff !important;
        transition: background 0.2s !important;
    }

    .acc-menu-item:hover {
        background: #080808 !important;
    }

    .acc-icon {
        color: #555 !important;
        font-size: 1.1rem !important;
        width: 30px !important;
        text-align: center !important;
        margin-left: 15px !important;
    }

    .acc-label {
        flex: 1 !important;
        color: #eee !important;
        font-size: 1rem !important;
        font-weight: 500 !important;
    }

    .acc-arrow {
        color: #333 !important;
        font-size: 0.8rem !important;
    }

    .acc-footer-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 12px !important;
        padding: 20px !important;
    }

    .acc-btn {
        background: #080808 !important;
        border: 1px solid #1a1a1a !important;
        border-radius: 12px !important;
        padding: 15px 10px !important;
        color: #fff !important;
        text-decoration: none !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 0.85rem !important;
        text-align: center !important;
    }

    .acc-btn i {
        font-size: 1.2rem !important;
        color: #888 !important;
    }

    .acc-socials {
        display: flex !important;
        justify-content: center !important;
        gap: 25px !important;
        margin: 30px 0 !important;
    }

    .acc-socials a {
        color: #fff !important;
        font-size: 1.6rem !important;
        opacity: 0.6 !important;
        transition: opacity 0.3s !important;
    }

    .acc-socials a:hover {
        opacity: 1 !important;
        color: #eb6b7e !important;
    }

    .acc-copyright {
        text-align: center !important;
        color: #444 !important;
        font-size: 0.85rem !important;
        padding-bottom: 30px !important;
        line-height: 1.6 !important;
    }

    .text-danger-custom {
        color: #ff4d4d !important;
    }
</style>

<div class="acc-wrapper">
    <!-- Header -->
    <div class="acc-header">
        <div style="width: 24px;"></div> <!-- Placeholder to center title -->
        <h5>حسابي</h5>
        <a href="index.php" style="color: #fff;"><i class="fas fa-chevron-left"></i></a>
    </div>

    <!-- User Section -->
    <div class="acc-user-card">
        <h4>هلا فيك <?php echo htmlspecialchars($user['full_name']); ?></h4>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Links -->
    <div class="acc-menu">
        <a href="edit_profile.php" class="acc-menu-item">
            <div class="acc-arrow"><i class="fas fa-chevron-left"></i></div>
            <div class="acc-label">تعديل الملف الشخصي</div>
            <div class="acc-icon"><i class="far fa-user-circle"></i></div>
        </a>

        <a href="my_orders.php" class="acc-menu-item">
            <div class="acc-arrow"><i class="fas fa-chevron-left"></i></div>
            <div class="acc-label">الطلبات</div>
            <div class="acc-icon"><i class="fas fa-shopping-bag"></i></div>
        </a>

        <a href="logout.php" class="acc-menu-item">
            <div class="acc-arrow"><i class="fas fa-chevron-left"></i></div>
            <div class="acc-label text-danger-custom">تسجيل الخروج</div>
            <div class="acc-icon text-danger-custom"><i class="fas fa-sign-out-alt"></i></div>
        </a>
    </div>

    <!-- Support Buttons -->
    <div class="acc-footer-grid">
        <a href="#" class="acc-btn">
             <i class="far fa-comment-dots"></i>
             <span>شاركنا رأيك / مقترحاتك</span>
        </a>
        <a href="#" class="acc-btn">
             <i class="fas fa-headset"></i>
             <span>خدمة العملاء</span>
        </a>
    </div>

    <!-- Social -->
    <div class="acc-socials">
        <a href="#"><i class="fab fa-tiktok"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-facebook"></i></a>
    </div>

    <!-- Copyright -->
    <div class="acc-copyright">
        <p>جميع الحقوق محفوظة &copy;  2026</p>
        <p style="opacity: 0.5;">الإصدار 1.7</p>
    </div>
</div>

<div style="height: 100px;"></div> <!-- Bottom Nav Space -->

<?php include 'includes/public_footer.php'; ?>
