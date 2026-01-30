<?php
include 'includes/public_header.php';

// Fetch Categories
$stmt = $db->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="text-center mb-5 animate__animated animate__fadeInDown">
        <h2 class="fw-bold mb-3 display-5">تصفح الفئات</h2>
        <div class="d-flex justify-content-center">
            <div style="height: 3px; width: 80px; background-color: var(--primary-color);"></div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="d-flex flex-column gap-3">
                <?php foreach($categories as $index => $cat): ?>
                <div class="animate__animated animate__fadeInUp" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <a href="shop.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none">
                        <div class="category-simple-item d-flex align-items-center bg-dark p-2 rounded-pill border border-dark">
                            <!-- Image Side -->
                            <div class="flex-shrink-0">
                                <div class="cat-img-simple rounded-circle">
                                    <img src="<?php echo $cat['image']; ?>" class="w-100 h-100 object-fit-cover rounded-circle" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                </div>
                            </div>
                            
                            <!-- Text Side -->
                            <div class="flex-grow-1 text-center pe-3">
                                <h4 class="fw-bold text-white m-0"><?php echo htmlspecialchars($cat['name']); ?></h4>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .category-simple-item {
        background: #0d0d0d !important;
        border: 1px solid #222 !important;
        transition: background-color 0.2s ease;
    }

    .category-simple-item:active {
        background-color: #1a1a1a !important;
    }

    .cat-img-simple {
        width: 80px;
        height: 80px;
        background: #000;
        border: 2px solid #222;
        padding: 2px;
    }
    
    @media (max-width: 576px) {
        .cat-img-simple {
            width: 70px;
            height: 70px;
        }
    }
</style>

<?php include 'includes/public_footer.php'; ?>
