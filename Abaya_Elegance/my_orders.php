<?php
include 'includes/public_header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch User Orders
$query = "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':uid', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .orders-wrapper {
        background: #000 !important;
        min-height: 100vh !important;
        direction: rtl !important;
        font-family: 'Cairo', sans-serif !important;
        color: #fff !important;
    }
    .orders-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 15px 20px !important;
        background: #000 !important;
        border-bottom: 1px solid #1a1a1a !important;
    }
    .order-card {
        background: #0d0d0d !important;
        border: 1px solid #1a1a1a !important;
        border-radius: 15px !important;
        padding: 20px !important;
        margin-bottom: 15px !important;
        transition: transform 0.2s !important;
    }
    .order-card:hover {
        transform: translateY(-3px) !important;
        border-color: #333 !important;
    }
    .status-badge {
        padding: 5px 12px !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
    }
</style>

<div class="orders-wrapper">
    <div class="orders-header">
        <div style="width: 24px;"></div>
        <h5 class="m-0 fw-bold">طلباتي</h5>
        <a href="profile.php" style="color: #fff;"><i class="fas fa-chevron-left"></i></a>
    </div>

    <div class="container py-4">
        <?php if(count($orders) > 0): ?>
            <div class="row">
                <?php foreach($orders as $order): ?>
                <div class="col-12">
                    <div class="order-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">رقم الطلب: #<?php echo htmlspecialchars($order['order_number']); ?></span>
                            <?php 
                            $status_class = '';
                            $status_text = '';
                            switch($order['status']) {
                                case 'pending': $status_class='bg-warning text-dark'; $status_text='قيد الانتظار'; break;
                                case 'processing': $status_class='bg-info text-white'; $status_text='قيد المعالجة'; break;
                                case 'shipped': $status_class='bg-primary text-white'; $status_text='تم الشحن'; break;
                                case 'delivered': $status_class='bg-success text-white'; $status_text='تم التوصيل'; break;
                                case 'cancelled': $status_class='bg-danger text-white'; $status_text='ملغي'; break;
                            }
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold mb-1"><?php echo number_format($order['total_amount'], 2); ?> ر.س</div>
                                <div class="text-muted small"><?php echo date('d M Y', strtotime($order['created_at'])); ?></div>
                            </div>
                            <a href="order_view.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-light btn-sm rounded-pill px-4">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-basket fa-4x text-muted mb-4 opacity-25"></i>
                <h4 class="fw-bold">لا يوجد لديك طلبات بعد</h4>
                <p class="text-muted mb-4">اكتشفي تشكيلتنا الجديدة وابدأي التسوق الآن</p>
                <a href="shop.php" class="btn btn-primary rounded-pill px-5" style="background: var(--primary-color); border: none;">ابدأ التسوق</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div style="height: 80px;"></div>

<?php include 'includes/public_footer.php'; ?>
