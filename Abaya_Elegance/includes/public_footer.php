<!-- تذييل الصفحة (Footer) -->
<footer class="bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row">
            <!-- عمود: عن المتجر وروابط السوشيال ميديا -->
            <div class="col-md-4 mb-4">
                <!-- اسم الموقع -->
                <h5 class="fw-bold mb-3 text-warning"><?php echo htmlspecialchars($site_name); ?></h5>
                <p class="text-muted small">
                    <!-- وصف مختصر عن المتجر -->
                    <?php echo htmlspecialchars($settings['about_us'] ?? 'متجر عباية إليجانس يقدم لك أرقى وأفخم العبايات المصممة خصيصاً لتناسب ذوقك الرفيع.'); ?>
                </p>
                <div class="mt-3">
                    <?php if(!empty($settings['facebook_url'])): ?>
                        <a href="<?php echo $settings['facebook_url']; ?>" class="text-white me-3 hover-scale"><i class="fab fa-facebook fa-lg"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($settings['instagram_url'])): ?>
                        <a href="<?php echo $settings['instagram_url']; ?>" class="text-white me-3 hover-scale"><i class="fab fa-instagram fa-lg"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($settings['twitter_url'])): ?>
                        <a href="<?php echo $settings['twitter_url']; ?>" class="text-white hover-scale"><i class="fab fa-twitter fa-lg"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- عمود: روابط سريعة -->
            <div class="col-md-2 mb-4">
                <h6 class="fw-bold mb-3">روابط سريعة</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php" class="text-secondary text-decoration-none hover-white">الرئيسية</a></li>
                    <li class="mb-2"><a href="shop.php" class="text-secondary text-decoration-none hover-white">التسوق</a></li>
                    </ul>
            </div>
            
            <!-- عمود: معلومات التواصل -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">تواصل معنا</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><i class="fas fa-map-marker-alt text-warning me-2"></i> المملكة العربية السعودية</li>
                    <li class="mb-2"><i class="fas fa-phone text-warning me-2"></i> <?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?></li>
                    <li class="mb-2"><i class="fas fa-envelope text-warning me-2"></i> <?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?></li>
                </ul>
            </div>
            
            <!-- عمود: النشرة البريدية -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">النشرة البريدية</h6>
                <p class="small text-muted">اشترك لتصلك أحدث العروض الحصرية.</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="بريدك الإلكتروني">
                    <button class="btn btn-warning btn-sm" type="button"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
        
        <hr class="border-secondary my-4">
        
        <div class="row align-items-center">
            <!-- حقوق النشر وتاريخ السنة الحالي تلقائياً -->
            <div class="col-md-6 text-center text-md-end text-muted small">
                &copy; <?php echo date('Y'); ?> جميع الحقوق محفوظة لـ <?php echo htmlspecialchars($site_name); ?>
            </div>
            <!-- شعارات وسائل الدفع -->
            <div class="col-md-6 text-center text-md-start">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/PayPal.svg/1200px-PayPal.svg.png" height="20" class="mx-2 opacity-50 bg-white p-1 rounded">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png" height="20" class="mx-2 opacity-50 bg-white p-1 rounded">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" height="20" class="mx-2 opacity-50 bg-white p-1 rounded">
            </div>
        </div>
    </div>
</footer>

<!-- شاشة التحميل (Preloader) -->
<div id="preloader">
    <div class="spinner-gold"></div>
</div>

<!-- زر الصعود للأعلى -->
<button id="scrollTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>

<!-- مكتبات الجافاسكريبت -->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- هيكل نافذة العرض السريع (Quick View Modal) -->
<div id="quickViewModal" class="qv-modal">
    <div class="qv-content animate__animated animate__zoomIn">
        <span class="qv-close">&times;</span>
        
        <!-- الجانب الأيمن: الصورة -->
        <div class="qv-image-side">
            <img id="qv-img" src="" alt="Product Image">
        </div>

        <!-- الجانب الأيسر: المعلومات -->
        <div class="qv-info-side">
            <div class="d-flex justify-content-between align-items-start mb-2">
                 <h2 id="qv-title" class="qv-title mb-0"></h2>
                 <a id="qv-details-link" href="#" class="text-muted small text-decoration-none ms-2" style="white-space: nowrap;">
                    التفاصيل <i class="fas fa-external-link-alt"></i>
                 </a>
            </div>
            
            <div id="qv-model" class="qv-model">رقم الموديل : <span id="qv-id"></span></div>
            
            <div id="qv-price" class="qv-price"></div>
            
            <div class="mb-4">
                <span class="qv-option-label">اللون :</span>
                <div class="d-flex gap-2">
                    <div id="qv-color" class="qv-option-box m-0"></div>
                </div>
            </div>
            
            <div class="mb-5">
                <span class="qv-option-label">المقاس :</span>
                <div class="d-flex gap-2">
                    <div class="qv-option-box m-0 active-option">مقاس واحد</div>
                </div>
            </div>
            
            <button id="qv-add-to-cart" class="qv-add-btn w-100">إضافة للسلة</button>
        </div>
    </div>
</div>

<script>
    // --- منطق العرض السريع (Quick View) ---
    const qvModal = document.getElementById('quickViewModal');
    const qvClose = document.querySelector('.qv-close');
    
    // الاستماع للنقر على أي زر يحمل الكلاس 'quick-view-trigger'
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quick-view-trigger')) {
            const trigger = e.target.closest('.quick-view-trigger');
            const productId = trigger.getAttribute('data-id');
            openQuickView(productId);
        }
    });

    // دالة فتح النافذة وجلب البيانات
    function openQuickView(id) {
        // جلب بيانات المنتج بصيغة JSON
        fetch('get_product_json.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'عذراً',
                        text: 'تعذر تحميل بيانات المنتج',
                        background: '#111',
                        color: '#fff'
                    });
                    return;
                }
                
                // تعبئة البيانات في النافذة
                document.getElementById('qv-img').src = data.main_image;
                document.getElementById('qv-title').innerText = data.name;
                document.getElementById('qv-id').innerText = data.category_name === 'Abayas' ? 'ABY-00' + data.id : 'MOD-00' + data.id;
                document.getElementById('qv-price').innerText = data.formatted_price;
                document.getElementById('qv-color').innerText = data.color || 'أسود ملكي';
                document.getElementById('qv-details-link').href = 'product.php?id=' + data.id;
                
                // تحديث زر الإضافة للسلة
                document.getElementById('qv-add-to-cart').onclick = function() {
                    window.location.href = 'cart_action.php?add=' + data.id;
                };

                // إظهار النافذة ومنع السكرول في الخلفية
                qvModal.classList.add('show');
                document.body.style.overflow = 'hidden'; 
            })
            .catch(err => {
                console.error('Quick View Error:', err);
            });
    }

    // إغلاق النافذة من زر الإغلاق
    qvClose.onclick = function() {
        qvModal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    // إغلاق النافذة عند النقر خارجها
    window.onclick = function(event) {
        if (event.target == qvModal) {
            qvModal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    }
</script>

<script>
    // --- شاشة التحميل (Preloader) ---
    window.addEventListener('load', function() {
        var preloader = document.getElementById('preloader');
        preloader.style.opacity = '0'; // تدرج في الاختفاء
        setTimeout(function() {
            preloader.style.display = 'none'; // إزالة العنصر بعد انتهاء الحركة
        }, 500);
    });

    // --- زر الصعود للأعلى ---
    var scrollTopBtn = document.getElementById("scrollTopBtn");
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (scrollTopBtn) {
            // يظهر الزر بعد نزول 20 بيكسل
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                scrollTopBtn.style.display = "block";
            } else {
                scrollTopBtn.style.display = "none";
            }
        }
    }

    if(scrollTopBtn) {
        scrollTopBtn.onclick = function() {
            window.scrollTo({top: 0, behavior: 'smooth'}); // صعود سلس
        }
    }

    // --- خاصية السحب للتمرير في قائمة التصنيفات (Drag to Scroll) ---
    const slider = document.querySelector('.category-circles-container');
    let isDown = false;
    let startX;
    let scrollLeft;

    if(slider) {
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.classList.add('active'); // تغيير شكل المؤشر
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('active');
        });
        
        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('active');
        });
        
        slider.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2; // زيادة سرعة التمرير
            slider.scrollLeft = scrollLeft - walk;
        });
    }
</script>

</body>
</html>
