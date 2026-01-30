    </div>
    <!-- End Page Content -->
</div>
<!-- End Wrapper -->

<!-- مكتبة بوتستراب JS للتفاعلات (القوائم المنسدلة، المودال، إلخ) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- مكتبة SweetAlert2 للتنبيهات الجميلة -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- أكواد جافاسكريبت مخصصة -->
<script>
    // --- دالة التحكم في القائمة الجانبية للجوال ---
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        // تبديل كلاس 'active' للإظهار والإخفاء
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        
        // تحسين الحركة (Animation) عند الفتح والإغلاق
        if(sidebar.classList.contains('active')) {
            sidebar.style.marginRight = "0";
            overlay.style.display = "block";
            // تأخير بسيط لتفعيل الشفافية (Fade In)
            setTimeout(() => overlay.style.opacity = "1", 10);
        } else {
            sidebar.style.marginRight = "-280px";
            overlay.style.opacity = "0";
            // الانتظار حتى تنتهي الحركة ثم إخفاء العنصر
            setTimeout(() => overlay.style.display = "none", 400);
        }
    }

    // --- إعدادات SweetAlert العامة (Mixin) ---
    // توحيد التصميم الداكن الفاخر لجميع التنبيهات
    const premiumSwal = Swal.mixin({
        background: 'rgba(20, 20, 20, 0.95)', // خلفية داكنة شفافة
        color: '#fff', // نص أبيض
        confirmButtonColor: '#eb6b7e', // لون زر التأكيد (وردي)
        cancelButtonColor: 'rgba(255,255,255,0.1)', // لون زر الإلغاء (شفاف)
        backdrop: `rgba(0,0,0,0.6) blur(4px)`, // خلفية مموهة خلف التنبيه
        customClass: {
            popup: 'premium-popup'
        }
    });

    // --- دالة تأكيد الحذف العامة ---
    // تستخدم في جميع أزرار الحذف
    function confirmDelete(url) {
        premiumSwal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من استرجاع هذا العنصر!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // التوجيه لرابط الحذف عند الموافقة
                window.location.href = url;
            }
        })
    }
    
    // --- دالة تأكيد تسجيل الخروج ---
    function confirmLogout() {
        premiumSwal.fire({
            title: 'تسجيل خروج',
            text: "هل تود تسجيل الخروج من النظام فعلاً؟",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'خروج',
            cancelButtonText: 'بقاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php'; 
            }
        })
    }

    // --- عرض رسائل النجاح والفشل القادمة من الجلسة (PHP Session) ---
    
    // رسالة نجاح
    <?php if(isset($_SESSION['success'])): ?>
        premiumSwal.fire({
            icon: 'success',
            title: 'تم بنجاح!',
            text: '<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>', // عرض الرسالة وحذفها من الجلسة
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    // رسالة خطأ
    <?php if(isset($_SESSION['error'])): ?>
        premiumSwal.fire({
            icon: 'error',
            title: 'عذراً...',
            text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
        });
    <?php endif; ?>
</script>

</body>
</html>
