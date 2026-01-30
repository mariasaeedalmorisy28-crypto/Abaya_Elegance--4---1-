<?php
include 'includes/public_header.php';

if (!isset($_GET['order'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$order_number = htmlspecialchars($_GET['order']);
?>

<div class="container py-5 text-center animate__animated animate__zoomIn">
    <div class="py-5">
        <div class="mb-4">
            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 100px; height: 100px;">
                <i class="fas fa-check fa-3x"></i>
            </div>
        </div>
        
        <h1 class="fw-bold mb-2">شكراً لطلبك!</h1>
        <p class="lead text-muted mb-4">تم استلام طلبك بنجاح وسنبدأ العمل عليه فوراً.</p>
        
        <div class="card d-inline-block border-0 shadow-sm p-4 mb-5 bg-light" style="min-width: 300px;">
            <p class="mb-1 text-muted">رقم الطلب الخاص بك</p>
            <h3 class="fw-bold text-primary m-0 ls-2"><?php echo $order_number; ?></h3>
        </div>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="index.php" class="btn btn-outline-dark rounded-pill px-4">العودة للرئيسية</a>
            <a href="shop.php" class="btn btn-gold rounded-pill px-4">متابعة التسوق</a>
        </div>
    </div>
</div>

<?php include 'includes/public_footer.php'; ?>
