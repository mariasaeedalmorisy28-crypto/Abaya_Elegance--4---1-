<?php
include 'includes/public_header.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Here sends email logic or save to DB messages table
    // For now, just simulate success
    $msg = "تم إرسال رسالتك بنجاح! شكراً لتواصلك معنا.";
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center mb-5">
            <h2 class="fw-bold section-title">تواصل معنا</h2>
            <p class="text-muted">يسعدنا دائماً الاستماع إلى عملائنا. لا تتردد في مراسلتنا لأي استفسار أو اقتراح.</p>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-4">
            <div class="contact-info bg-white p-4 rounded shadow-sm h-100">
                <h4 class="fw-bold mb-4">معلومات الاتصال</h4>
                
                <div class="d-flex mb-4">
                    <div class="icon text-primary ms-3"><i class="fas fa-map-marker-alt fa-2x"></i></div>
                    <div>
                        <h6 class="fw-bold">العنوان</h6>
                        <p class="text-muted small">المملكة العربية السعودية، الرياض، شارع العليا</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="icon text-primary ms-3"><i class="fas fa-phone fa-2x"></i></div>
                    <div>
                        <h6 class="fw-bold">الهاتف</h6>
                        <p class="text-muted small"><?php echo htmlspecialchars($settings['contact_phone']); ?></p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="icon text-primary ms-3"><i class="fas fa-envelope fa-2x"></i></div>
                    <div>
                        <h6 class="fw-bold">البريد الإلكتروني</h6>
                        <p class="text-muted small"><?php echo htmlspecialchars($settings['contact_email']); ?></p>
                    </div>
                </div>

                <div class="social-links mt-5 pt-4 border-top text-center">
                   <h6 class="fw-bold mb-3">تابعنا على</h6>
                   <a href="#" class="btn btn-outline-dark btn-sm rounded-circle m-1"><i class="fab fa-facebook-f"></i></a>
                   <a href="#" class="btn btn-outline-dark btn-sm rounded-circle m-1"><i class="fab fa-instagram"></i></a>
                   <a href="#" class="btn btn-outline-dark btn-sm rounded-circle m-1"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="bg-white p-5 rounded shadow-sm h-100">
                <?php if($msg): ?>
                    <div class="alert alert-success"><?php echo $msg; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الموضوع</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الرسالة</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-gold px-5 rounded-pill">إرسال الرسالة</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/public_footer.php'; ?>
