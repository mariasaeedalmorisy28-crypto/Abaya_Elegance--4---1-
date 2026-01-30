<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_name = $_POST['site_name'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    $currency = $_POST['currency'];
    $about_us = $_POST['about_us'];
    $facebook = $_POST['facebook_url'];
    $instagram = $_POST['instagram_url'];
    
    // Logo Upload
    $logo_url = "";
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
        $target_dir = "../assets/images/";
        $target_file = $target_dir . "logo.png"; // Keep it consistent
        if(move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)){
            $logo_url = "assets/images/logo.png";
        }
    }

    $query = "UPDATE settings SET 
              site_name = :site_name, 
              contact_email = :email, 
              contact_phone = :phone, 
              currency = :currency,
              about_us = :about,
              facebook_url = :fb,
              instagram_url = :insta
              WHERE id = 1";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':site_name', $site_name);
    $stmt->bindParam(':email', $contact_email);
    $stmt->bindParam(':phone', $contact_phone);
    $stmt->bindParam(':currency', $currency);
    $stmt->bindParam(':about', $about_us);
    $stmt->bindParam(':fb', $facebook);
    $stmt->bindParam(':insta', $instagram);
    
    if($stmt->execute()){
         $_SESSION['success'] = "تم حفظ الإعدادات بنجاح";
         header("Location: settings.php"); // Reload to see changes
         exit();
    }
}

// Fetch Settings
$query = "SELECT * FROM settings WHERE id = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-secondary fw-bold">إعدادات المتجر</h2>
            <p class="text-muted">التحكم في بيانات الموقع الأساسية ومعلومات التواصل</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- General Settings -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-cogs text-primary me-2"></i> الإعدادات العامة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم المتجر</label>
                                <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">العملة</label>
                                <input type="text" name="currency" class="form-control" value="<?php echo htmlspecialchars($settings['currency']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نبذة عن المتجر (يظهر في الفوتر)</label>
                            <textarea name="about_us" class="form-control" rows="4"><?php echo htmlspecialchars($settings['about_us']); ?></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">البريد الإلكتروني للدعم</label>
                                <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الهاتف / الواتساب</label>
                                <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-share-alt text-primary me-2"></i> روابط التواصل الاجتماعي</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-facebook text-primary"></i> رابط فيسبوك</label>
                            <input type="url" name="facebook_url" class="form-control" value="<?php echo htmlspecialchars($settings['facebook_url']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-instagram text-danger"></i> رابط انستجرام</label>
                            <input type="url" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($settings['instagram_url']); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo & Save -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-image text-primary me-2"></i> شعار الموقع</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php if(file_exists("../assets/images/logo.png")): ?>
                                <img src="../assets/images/logo.png?v=<?php echo time(); ?>" class="img-fluid mb-3" style="max-height: 150px;">
                            <?php else: ?>
                                <div class="bg-light p-5 mb-3 rounded text-muted">لا يوجد شعار</div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control" accept="image/png, image/jpeg">
                            <small class="text-muted">يفضل أن تكون الصورة PNG خلفية شفافة</small>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i> حفظ الإعدادات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
