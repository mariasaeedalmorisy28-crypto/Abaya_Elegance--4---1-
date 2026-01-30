<?php
include 'includes/public_header.php';

if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_active']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user['user_type'];

            if ($user['user_type'] == 'admin') {
                echo "<script>window.location.href='admin/dashboard.php';</script>";
            } else {
                echo "<script>window.location.href='index.php';</script>";
            }
            exit();
        } else {
            $error = "حسابك غير نشط، يرجى التواصل مع الإدارة";
        }
    } else {
        $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
    }
}
?>
<div class="auth-wrapper">
    <div class="auth-container animate__animated animate__fadeInUp">
        <div class="auth-tabs">
            <a href="register.php" class="auth-tab">حساب جديد</a>
            <a href="login.php" class="auth-tab active">تسجيل الدخول</a>
        </div>
        
        <div class="auth-body">
            <?php if($error): ?>
                <div class="alert alert-danger text-center small mb-4"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="auth-form-group">
                    <input type="email" name="email" class="auth-input" placeholder="عنوان البريد أو رقم الهاتف" required>
                </div>
                
                <div class="auth-form-group">
                    <input type="password" name="password" id="password" class="auth-input" placeholder="كلمة السر" required>
                    <i class="far fa-eye auth-password-toggle" onclick="togglePassword()"></i>
                </div>
                
                <a href="#" class="auth-link">هل نسيت كلمة السر؟</a>
                
                <button type="submit" class="auth-submit-btn">تسجيل الدخول</button>
            </form>

            <div class="social-login">
                <div class="social-title">أو</div>
                <div class="social-group">
                    <a href="#" class="social-btn google">
                        <i class="fab fa-google"></i>
                        المتابعة بحساب جوجل
                    </a>
                    <a href="#" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                        المتابعة بحساب فيس بوك
                    </a>
                </div>
            </div>

            <div class="auth-skip">
                <a href="index.php">تخطي</a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pwdInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.auth-password-toggle');
    if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        pwdInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>

<?php include 'includes/public_footer.php'; ?>
