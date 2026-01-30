<?php
include 'includes/public_header.php';

// Fetch Categories for Circular Slider
$query_cat = "SELECT * FROM categories";
$stmt_cat = $db->prepare($query_cat);
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Fetch New Arrivals (Limit 6 for better filling)
$query_new = "SELECT * FROM abayas ORDER BY created_at DESC LIMIT 6";
$stmt_new = $db->prepare($query_new);
$stmt_new->execute();
$new_arrivals = $stmt_new->fetchAll(PDO::FETCH_ASSOC);

// Fetch Latest 3 Abayas for Hero Slider
$query_hero = "SELECT * FROM abayas ORDER BY id DESC LIMIT 3";
$stmt_hero = $db->prepare($query_hero);
$stmt_hero->execute();
$hero_abayas = $stmt_hero->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- 1. Category Circles Section (Story Style) -->
<section class="category-circles-wrapper border-bottom mb-4">
    <div class="container container-custom">
        <div class="category-circles-container">
            <!-- Add a static "All" or "New" circle if desired, or just DB categories -->
            <a href="shop.php" class="cat-circle-item">
                <div class="cat-circle-img d-flex align-items-center justify-content-center bg-dark text-white">
                    <i class="fas fa-th-large fa-2x"></i>
                </div>
                <span class="cat-circle-name">ط§ظ„ظƒظ„</span>
            </a>

            <?php foreach($categories as $cat): ?>
            <a href="shop.php?category=<?php echo $cat['id']; ?>" class="cat-circle-item">
                <img src="<?php echo $cat['image']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="cat-circle-img">
                <span class="cat-circle-name"><?php echo htmlspecialchars($cat['name']); ?></span>
            </a>
            <?php endforeach; ?>
            
             <!-- Dummy extra circles to mimic the 'full' look of reference if DB has few items -->
             <?php if(count($categories) < 6): ?>
                <a href="shop.php?sort=newest" class="cat-circle-item">
                    <img src="https://placehold.co/150/f0f0f0/d4af37?text=New" class="cat-circle-img">
                    <span class="cat-circle-name">ظˆطµظ„ ط­ط¯ظٹط«ط§ظ‹</span>
                </a>
                <a href="shop.php" class="cat-circle-item">
                    <img src="https://placehold.co/150/f0f0f0/d4af37?text=Sale" class="cat-circle-img">
                    <span class="cat-circle-name">طھط®ظپظٹط¶ط§طھ</span>
                </a>
             <?php endif; ?>
        </div>
    </div>
</section>

<!-- 2. Hero Ads Slider Section -->
<section class="hero-slider-section mb-5">
    <div class="swiper adsSwiper">
        <div class="swiper-wrapper">
            <?php if(!empty($hero_abayas)): ?>
                <?php foreach($hero_abayas as $index => $hero): ?>
                <div class="swiper-slide hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo $hero['main_image']; ?>');">
                    <div class="container h-100 d-flex align-items-center justify-content-center text-center">
                        <div class="slide-content text-white">
                            <h1 class="display-3 fw-bold mb-3 animate-text"><?php echo htmlspecialchars($hero['name']); ?></h1>
                            <p class="lead mb-4 animate-text"><?php echo mb_strimwidth(htmlspecialchars($hero['description']), 0, 100, "..."); ?></p>
                            <a href="product.php?id=<?php echo $hero['id']; ?>" class="btn btn-outline-light btn-lg rounded-0 px-5 fw-bold animate-text">ط§ظƒطھط´ظپظٹ ط§ظ„ظ…ط¬ظ…ظˆط¹ط©</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback slide if No Abayas -->
                <div class="swiper-slide hero-slide dark-theme" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1583391733956-6c78276477e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
                    <div class="container h-100 d-flex align-items-center justify-content-center text-center">
                        <div class="slide-content">
                            <h1 class="display-3 fw-bold mb-3 animate-text">ط§ظƒطھط´ظپظٹ ط§ظ„ظ…ط²ظٹط¯</h1>
                            <p class="lead mb-4 animate-text">ط£ظپط®ظ… ط§ظ„ط¹ط¨ط§ظٹط§طھ ط§ظ„ط¹طµط±ظٹط© ط¨ظ„ظ…ط³ط© ظ…ظ† ط§ظ„ط±ظ‚ظٹ ظˆط§ظ„ظپط®ط§ظ…ط©</p>
                            <a href="shop.php" class="btn btn-light btn-lg rounded-0 px-5 fw-bold animate-text">طھط³ظˆظ‚ظٹ ط§ظ„ط¢ظ†</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Pagination & Navigation -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>



<!-- 3. New Arrivals (Swiper for beautiful scrolling) -->
<section class="py-4">
    <div class="container-fluid px-3 px-md-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
             <a href="shop.php" class="text-white text-decoration-none small border-bottom border-secondary pb-1">ط¹ط±ط¶ ط§ظ„ظƒظ„</a>
             <h5 class="fw-bold mb-0">ط§ظ„ط£ظƒط«ط± ظ…ط¨ظٹط¹ط§ظ‹</h5>
        </div>
        
        <?php if(count($new_arrivals) > 0): ?>
        <div class="swiper productSwiper">
            <div class="swiper-wrapper">
                <?php foreach($new_arrivals as $abaya): ?>
                <div class="swiper-slide px-2">
                    <div class="product-card shadow-sm rounded-4 overflow-hidden" style="background: #0a0a0a; border: 1px solid #1a1a1a;">
                        <div class="product-img-wrapper" style="height: 260px;">
                            <a href="product.php?id=<?php echo $abaya['id']; ?>">
                                <img src="<?php echo $abaya['main_image']; ?>" alt="<?php echo htmlspecialchars($abaya['name']); ?>">
                            </a>
                            <div class="product-like-icon"><i class="far fa-heart"></i></div>
                            <div class="product-bag-icon quick-view-trigger cursor-pointer" data-id="<?php echo $abaya['id']; ?>">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="product-title text-white mb-1"><?php echo htmlspecialchars($abaya['name']); ?></div>
                            <div class="product-price text-primary">
                                <?php echo number_format($abaya['price'], 0); ?> ط±.ط³
                                <?php if($abaya['old_price']): ?>
                                    <span class="old-price small ms-2" style="color: #666; text-decoration: line-through;"><?php echo number_format($abaya['old_price'], 0); ?> ط±.ط³</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination position-relative mt-4"></div>
        </div>
        <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted">ط¬ط§ط±ظٹ ط¥ط¶ط§ظپط© ط§ظ„ظ…ظ†طھط¬ط§طھ...</p>
            </div>
        <?php endif; ?>
    </div>
</section>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Hero Ads Slider
    var adsSwiper = new Swiper(".adsSwiper", {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        effect: "fade",
        fadeEffect: {
            crossFade: true
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    // 2. Product Horizontal Swipers
    var productSwipers = new Swiper(".productSwiper", {
        slidesPerView: 2.2,
        spaceBetween: 12,
        freeMode: true,
        grabCursor: true,
        breakpoints: {
            // when window width is >= 768px
            768: {
                slidesPerView: 4,
                spaceBetween: 20
            },
            // when window width is >= 1200px
            1200: {
                slidesPerView: 5,
                spaceBetween: 25
            }
        }
    });
});
</script>

<?php include 'includes/public_footer.php'; ?>
