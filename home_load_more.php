<?php
// Assumes $conn, $login_status, and session customer id are already set upstream.
$customer_id_1 = isset($_SESSION['GBDELIVERING_TEMP_CUSTOMER_USER_2021']) ? $_SESSION['GBDELIVERING_TEMP_CUSTOMER_USER_2021'] : '';

// Get total products
$total_products = 0;
$sql_total = "SELECT COUNT(*) as total FROM product";
$result_total = $conn->query($sql_total);
if ($result_total) {
    $total_products = $result_total->fetch_assoc()['total'];
}

// Get random products for side panels (desktop only)
$sql_side = "SELECT p.product_id, p.product_name, pr.price,
            (SELECT picture FROM product_picture WHERE product_id = p.product_id LIMIT 1) as picture
            FROM product p 
            LEFT JOIN product_price pr ON pr.product_id = p.product_id 
            ORDER BY RAND() LIMIT 20";
$result_side = $conn->query($sql_side);
$side_products = [];
if ($result_side) {
    while ($sp = $result_side->fetch_assoc()) {
        $side_products[] = $sp;
    }
}
?>

<div class="ali-home-full">

    <!-- Left Side Panel (desktop only) -->
    <aside class="ali-side-panel ali-side-left">
        <div class="ali-side-title"><i class="fas fa-fire"></i> Hot Items</div>
        <?php 
        $left_products = array_slice($side_products, 0, 10);
        foreach ($left_products as $lp): 
            $lp_img = !empty($lp['picture']) ? $lp['picture'] : 'no-image.png';
        ?>
        <a href="index.php?product-detail&product=<?php echo $lp['product_id']; ?>" class="ali-side-product">
            <img src="uploads/<?php echo $lp_img; ?>" alt="<?php echo htmlspecialchars($lp['product_name']); ?>" loading="lazy">
            <div class="ali-side-info">
                <span class="ali-side-name"><?php echo htmlspecialchars(mb_strimwidth($lp['product_name'], 0, 22, '...')); ?></span>
                <span class="ali-side-price"><?php echo number_format($lp['price'] ?? 0, 0); ?> RWF</span>
            </div>
        </a>
        <?php endforeach; ?>
    </aside>

    <!-- Main Content -->
    <div class="ali-home-main">

        <!-- ====== HERO SECTION ====== -->
        <section class="ali-hero">
            <div class="ali-hero-row">
                <div class="ali-hero-banner">
                    <div class="ali-banner-slider" id="bannerSlider">
                        <?php
                        $sql_slider = "SELECT p.product_id, p.product_name, pr.price, pc.category_name,
                                      (SELECT picture FROM product_picture WHERE product_id = p.product_id LIMIT 1) as picture
                                      FROM product p 
                                      LEFT JOIN product_price pr ON pr.product_id = p.product_id 
                                      LEFT JOIN product_category pc ON pc.category_id = p.category_id
                                      ORDER BY RAND() LIMIT 5";
                        $result_slider = $conn->query($sql_slider);
                        $colors = [
                            'linear-gradient(135deg, #667eea, #764ba2)',
                            'linear-gradient(135deg, #ff6b6b, #ff5000)',
                            'linear-gradient(135deg, #4facfe, #00f2fe)',
                            'linear-gradient(135deg, #f093fb, #f5576c)',
                            'linear-gradient(135deg, #43e97b, #38f9d7)'
                        ];
                        $idx = 0;
                        if ($result_slider && $result_slider->num_rows > 0):
                            while ($row = $result_slider->fetch_assoc()):
                                $img = !empty($row['picture']) ? $row['picture'] : 'no-image.png';
                        ?>
                        <div class="ali-slide <?php echo $idx == 0 ? 'active' : ''; ?>" style="background: <?php echo $colors[$idx % 5]; ?>;">
                            <div class="ali-slide-content">
                                <span class="ali-slide-tag">Best Deals</span>
                                <h2><?php echo strtoupper(htmlspecialchars($row['category_name'] ?? 'SHOP NOW')); ?></h2>
                                <p><?php echo htmlspecialchars(mb_strimwidth($row['product_name'] ?? '', 0, 45, '...')); ?></p>
                                <div class="ali-slide-price">From <?php echo number_format($row['price'] ?? 0, 0); ?> RWF</div>
                                <a href="index.php?shop-search&search=<?php echo urlencode($row['category_name'] ?? ''); ?>" class="ali-slide-btn">Shop Now <i class="fas fa-arrow-right"></i></a>
                            </div>
                            <div class="ali-slide-img">
                                <img src="uploads/<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['product_name'] ?? ''); ?>">
                            </div>
                        </div>
                        <?php $idx++; endwhile; endif; ?>
                        <div class="ali-slider-controls">
                            <button onclick="prevSlide()" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
                            <button onclick="nextSlide()" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="ali-slider-dots" id="sliderDots"></div>
                    </div>
                </div>
                <div class="ali-hero-cards">
                    <a href="https://wa.me/250783654454" target="_blank" class="ali-side-card ali-card-green">
                        <i class="fab fa-whatsapp"></i>
                        <div><strong>Order via WhatsApp</strong><span>+250 783 654 454</span></div>
                    </a>
                    <a href="index.php?shop" class="ali-side-card ali-card-orange">
                        <i class="fas fa-fire"></i>
                        <div><strong>Hot Deals</strong><span>Up to 40% OFF</span></div>
                    </a>
                    <a href="index.php?shop" class="ali-side-card ali-card-blue">
                        <i class="fas fa-truck"></i>
                        <div><strong>Fast Delivery</strong><span>Same day in Kigali</span></div>
                    </a>
                </div>
            </div>
        </section>

        <!-- ====== CATEGORIES ====== -->
        <section class="ali-section">
            <div class="ali-container">
                <div class="ali-section-header">
                    <h2><i class="fas fa-th-large"></i> Categories</h2>
                    <a href="index.php?shop">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="ali-categories-wrap">
                    <div class="ali-categories">
                        <?php
                        $sql_cats = "SELECT * FROM product_category ORDER BY category_name ASC";
                        $result_cats = $conn->query($sql_cats);
                        $cat_colors = ['#ff6b6b','#4ecdc4','#45b7d1','#96c93d','#f7b731','#a55eea','#fd9644','#26de81','#eb3b5a','#3867d6'];
                        $c = 0;
                        if ($result_cats && $result_cats->num_rows > 0):
                            while ($cat = $result_cats->fetch_assoc()):
                                $cat_id = $cat['category_id'];
                                $cat_name = $cat['category_name'];
                                $sql_ci = "SELECT pp.picture FROM product_picture pp JOIN product p ON p.product_id = pp.product_id WHERE p.category_id='$cat_id' LIMIT 1";
                                $result_ci = $conn->query($sql_ci);
                                $cat_img = 'no-image.png';
                                if ($result_ci && $row_ci = $result_ci->fetch_assoc()) $cat_img = $row_ci['picture'];
                                $sql_cnt = "SELECT COUNT(*) as cnt FROM product WHERE category_id='$cat_id'";
                                $result_cnt = $conn->query($sql_cnt);
                                $cat_count = $result_cnt ? $result_cnt->fetch_assoc()['cnt'] : 0;
                        ?>
                        <a href="index.php?shop-search&search=<?php echo urlencode($cat_name); ?>" class="ali-cat-item">
                            <div class="ali-cat-icon" style="background:<?php echo $cat_colors[$c % 10]; ?>15;border-color:<?php echo $cat_colors[$c % 10]; ?>;">
                                <img src="uploads/<?php echo $cat_img; ?>" alt="<?php echo htmlspecialchars($cat_name); ?>" loading="lazy">
                            </div>
                            <span class="ali-cat-name"><?php echo htmlspecialchars($cat_name); ?></span>
                            <span class="ali-cat-count"><?php echo $cat_count; ?> items</span>
                        </a>
                        <?php $c++; endwhile; endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- ====== FEATURED PRODUCTS ====== -->
        <section class="ali-section ali-featured-section">
            <div class="ali-container">
                <div class="ali-section-header ali-featured-header">
                    <div class="ali-featured-title">
                        <i class="fas fa-bolt"></i>
                        <h2>Featured Products</h2>
                        <span class="ali-badge">Hot</span>
                    </div>
                    <a href="index.php?shop">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="ali-featured-scroll">
                    <?php
                    $sql_featured = "SELECT p.product_id, p.product_name, p.product_unit, p.product_minimum_order,
                                    pr.price,
                                    (SELECT picture FROM product_picture WHERE product_id = p.product_id LIMIT 1) as picture
                                    FROM product p 
                                    LEFT JOIN product_price pr ON pr.product_id = p.product_id 
                                    ORDER BY RAND() LIMIT 15";
                    $result_featured = $conn->query($sql_featured);
                    $f_idx = 0;
                    if ($result_featured && $result_featured->num_rows > 0):
                        while ($prod = $result_featured->fetch_assoc()):
                            $pid = $prod['product_id'];
                            $pname = $prod['product_name'];
                            $punit = $prod['product_unit'] ?? 'unit';
                            $pprice = $prod['price'] ?? 0;
                            $pimg = !empty($prod['picture']) ? $prod['picture'] : 'no-image.png';
                            $pmin = $prod['product_minimum_order'] ?? 1;
                            $uid = 'feat_' . $pid . '_' . $f_idx;
                    ?>
                    <div class="ali-featured-card">
                        <div class="ali-featured-img">
                            <a href="index.php?product-detail&product=<?php echo $pid; ?>">
                                <img src="uploads/<?php echo $pimg; ?>" alt="<?php echo htmlspecialchars($pname); ?>" loading="lazy">
                            </a>
                            <span class="ali-hot-tag"><i class="fas fa-fire"></i></span>
                        </div>
                        <div class="ali-featured-info">
                            <a href="index.php?product-detail&product=<?php echo $pid; ?>" class="ali-featured-name"><?php echo htmlspecialchars(mb_strimwidth($pname, 0, 35, '...')); ?></a>
                            <div class="ali-featured-price">
                                <strong><?php echo number_format($pprice, 0); ?> RWF</strong>
                                <span>/ <?php echo htmlspecialchars($punit); ?></span>
                            </div>
                            <div class="ali-featured-cart">
                                <input type="number" id="qty_<?php echo $uid; ?>" placeholder="Qty" min="1" class="ali-mini-qty" inputmode="numeric">
                                <button type="button" onclick="addCart('<?php echo $pid; ?>','<?php echo $pprice; ?>','<?php echo $uid; ?>','<?php echo $pmin; ?>')">
                                    <i class="fas fa-cart-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php $f_idx++; endwhile; endif; ?>
                </div>
            </div>
        </section>

        <!-- ====== CATEGORY BANNERS ====== -->
        <section class="ali-section ali-banners-section">
            <div class="ali-container">
                <div class="ali-banners-row">
                    <?php
                    $sql_banner_cats = "SELECT pc.category_id, pc.category_name,
                                       (SELECT picture FROM product_picture pp JOIN product p ON p.product_id = pp.product_id WHERE p.category_id = pc.category_id LIMIT 1) as picture,
                                       (SELECT COUNT(*) FROM product WHERE category_id = pc.category_id) as product_count
                                       FROM product_category pc ORDER BY RAND() LIMIT 3";
                    $result_banner_cats = $conn->query($sql_banner_cats);
                    $banner_colors = [
                        'linear-gradient(135deg,#667eea,#764ba2)',
                        'linear-gradient(135deg,#f093fb,#f5576c)',
                        'linear-gradient(135deg,#4facfe,#00f2fe)'
                    ];
                    $b_idx = 0;
                    if ($result_banner_cats && $result_banner_cats->num_rows > 0):
                        while ($bcat = $result_banner_cats->fetch_assoc()):
                            $bc_img = !empty($bcat['picture']) ? $bcat['picture'] : 'no-image.png';
                    ?>
                    <a href="index.php?shop-search&search=<?php echo urlencode($bcat['category_name']); ?>" class="ali-banner-card" style="background:<?php echo $banner_colors[$b_idx % 3]; ?>;">
                        <div class="ali-banner-text">
                            <span><?php echo $bcat['product_count']; ?> Products</span>
                            <h3><?php echo htmlspecialchars($bcat['category_name']); ?></h3>
                            <p>Shop Now <i class="fas fa-arrow-right"></i></p>
                        </div>
                        <div class="ali-banner-img">
                            <img src="uploads/<?php echo $bc_img; ?>" alt="<?php echo htmlspecialchars($bcat['category_name']); ?>" loading="lazy">
                        </div>
                    </a>
                    <?php $b_idx++; endwhile; endif; ?>
                </div>
            </div>
        </section>

        <!-- ====== PRODUCTS BY CATEGORY ====== -->
        <?php
        $sql_all_cats = "SELECT pc.category_id, pc.category_name FROM product_category pc 
                         WHERE (SELECT COUNT(*) FROM product WHERE category_id = pc.category_id) > 0
                         ORDER BY category_name ASC";
        $result_all_cats = $conn->query($sql_all_cats);
        $section_colors = ['#ff6b6b','#4ecdc4','#45b7d1','#96c93d','#f7b731','#a55eea','#fd9644','#26de81'];
        $sec_idx = 0;
        if ($result_all_cats && $result_all_cats->num_rows > 0):
            while ($sec_cat = $result_all_cats->fetch_assoc()):
                $sec_cat_id = $sec_cat['category_id'];
                $sec_cat_name = $sec_cat['category_name'];
                $sec_color = $section_colors[$sec_idx % 8];
        ?>
        <section class="ali-section ali-category-section">
            <div class="ali-container">
                <div class="ali-section-header" style="border-left:4px solid <?php echo $sec_color; ?>;padding-left:12px;">
                    <h2 style="color:<?php echo $sec_color; ?>;"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($sec_cat_name); ?></h2>
                    <a href="index.php?shop-search&search=<?php echo urlencode($sec_cat_name); ?>">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="ali-products-grid">
                    <?php
                    $sql_cat_prods = "SELECT p.product_id, p.product_name, p.product_unit, p.product_minimum_order,
                                     pr.price,
                                     (SELECT picture FROM product_picture WHERE product_id = p.product_id LIMIT 1) as picture
                                     FROM product p LEFT JOIN product_price pr ON pr.product_id = p.product_id 
                                     WHERE p.category_id = '$sec_cat_id' ORDER BY RAND() LIMIT 6";
                    $result_cat_prods = $conn->query($sql_cat_prods);
                    $cp_idx = 0;
                    if ($result_cat_prods && $result_cat_prods->num_rows > 0):
                        while ($cprod = $result_cat_prods->fetch_assoc()):
                            $cpid = $cprod['product_id'];
                            $cpname = $cprod['product_name'];
                            $cpunit = $cprod['product_unit'] ?? 'unit';
                            $cpprice = $cprod['price'] ?? 0;
                            $cpimg = !empty($cprod['picture']) ? $cprod['picture'] : 'no-image.png';
                            $cpmin = $cprod['product_minimum_order'] ?? 1;
                            $cpuid = 'cat_'.$sec_cat_id.'_'.$cpid.'_'.$cp_idx;
                    ?>
                    <div class="ali-product">
                        <a href="index.php?product-detail&product=<?php echo $cpid; ?>" class="ali-product-img-link">
                            <div class="ali-product-img"><img src="uploads/<?php echo $cpimg; ?>" alt="<?php echo htmlspecialchars($cpname); ?>" loading="lazy"></div>
                        </a>
                        <div class="ali-product-info">
                            <a href="index.php?product-detail&product=<?php echo $cpid; ?>" class="ali-product-name"><?php echo htmlspecialchars(mb_strimwidth($cpname, 0, 40, '...')); ?></a>
                            <div class="ali-product-price">
                                <strong><?php echo number_format($cpprice, 0); ?> RWF</strong>
                                <span>/ <?php echo htmlspecialchars($cpunit); ?></span>
                            </div>
                            <div class="ali-product-cart">
                                <input type="number" id="qty_<?php echo $cpuid; ?>" placeholder="Qty" min="1" class="ali-qty-input" inputmode="numeric">
                                <button type="button" class="ali-cart-btn" onclick="addCart('<?php echo $cpid; ?>','<?php echo $cpprice; ?>','<?php echo $cpuid; ?>','<?php echo $cpmin; ?>')">
                                    <i class="fas fa-cart-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php $cp_idx++; endwhile; endif; ?>
                </div>
            </div>
        </section>
        <?php $sec_idx++; endwhile; endif; ?>

        <!-- ====== ALL PRODUCTS + INFINITE SCROLL ====== -->
        <section class="ali-section ali-all-products-section">
            <div class="ali-container">
                <div class="ali-section-header">
                    <h2><i class="fas fa-store"></i> All Products</h2>
                    <span class="ali-count"><?php echo number_format($total_products); ?> products</span>
                </div>
                <div class="ali-products-grid" id="productsGrid">
                    <?php
                    $sql_products = "SELECT p.product_id, p.product_name, p.product_unit, p.product_minimum_order,
                                    pr.price,
                                    (SELECT picture FROM product_picture WHERE product_id = p.product_id LIMIT 1) as picture,
                                    (SELECT category_name FROM product_category WHERE category_id = p.category_id) as category_name
                                    FROM product p LEFT JOIN product_price pr ON pr.product_id = p.product_id 
                                    ORDER BY p.register_date DESC LIMIT 24";
                    $result_products = $conn->query($sql_products);
                    $p_idx = 0;
                    if ($result_products && $result_products->num_rows > 0):
                        while ($prod = $result_products->fetch_assoc()):
                            $pid = $prod['product_id'];
                            $pname = $prod['product_name'];
                            $punit = $prod['product_unit'] ?? 'unit';
                            $pprice = $prod['price'] ?? 0;
                            $pimg = !empty($prod['picture']) ? $prod['picture'] : 'no-image.png';
                            $pcat = $prod['category_name'] ?? '';
                            $pmin = $prod['product_minimum_order'] ?? 1;
                            $uid = 'all_'.$pid.'_'.$p_idx;
                    ?>
                    <div class="ali-product">
                        <a href="index.php?product-detail&product=<?php echo $pid; ?>" class="ali-product-img-link">
                            <div class="ali-product-img"><img src="uploads/<?php echo $pimg; ?>" alt="<?php echo htmlspecialchars($pname); ?>" loading="lazy"></div>
                        </a>
                        <div class="ali-product-info">
                            <?php if ($pcat): ?><span class="ali-product-cat"><?php echo htmlspecialchars($pcat); ?></span><?php endif; ?>
                            <a href="index.php?product-detail&product=<?php echo $pid; ?>" class="ali-product-name"><?php echo htmlspecialchars(mb_strimwidth($pname, 0, 40, '...')); ?></a>
                            <div class="ali-product-price">
                                <strong><?php echo number_format($pprice, 0); ?> RWF</strong>
                                <span>/ <?php echo htmlspecialchars($punit); ?></span>
                            </div>
                            <div class="ali-product-cart">
                                <input type="number" id="qty_<?php echo $uid; ?>" placeholder="Qty" min="1" class="ali-qty-input" inputmode="numeric">
                                <button type="button" class="ali-cart-btn" onclick="addCart('<?php echo $pid; ?>','<?php echo $pprice; ?>','<?php echo $uid; ?>','<?php echo $pmin; ?>')">
                                    <i class="fas fa-cart-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php $p_idx++; endwhile; endif; ?>
                </div>

                <!-- Infinite scroll sentinel & fallback button -->
                <div id="infiniteScrollArea">
                    <div id="loadingSpinner" class="ali-infinite-loader" style="display:none;">
                        <div class="ali-loader-dots"><span></span><span></span><span></span></div>
                        <span>Loading more products...</span>
                    </div>
                </div>
                <div id="noMoreMsg" style="display:none;" class="ali-no-more">
                    <i class="fas fa-check-circle"></i> You've seen all products!
                </div>
            </div>
        </section>

        <!-- ====== WHY US ====== -->
        <section class="ali-section ali-why">
            <div class="ali-container">
                <div class="ali-why-grid">
                    <div class="ali-why-item">
                        <i class="fas fa-leaf"></i>
                        <h4>Fresh Products</h4>
                        <p>Quality guaranteed</p>
                    </div>
                    <div class="ali-why-item">
                        <i class="fas fa-truck"></i>
                        <h4>Fast Delivery</h4>
                        <p>Same day in Kigali</p>
                    </div>
                    <div class="ali-why-item">
                        <i class="fas fa-wallet"></i>
                        <h4>Best Prices</h4>
                        <p>Affordable rates</p>
                    </div>
                    <div class="ali-why-item">
                        <i class="fas fa-headset"></i>
                        <h4>24/7 Support</h4>
                        <p>Always available</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ====== WHATSAPP CTA ====== -->
        <section class="ali-section ali-cta-section">
            <div class="ali-container">
                <div class="ali-cta-box">
                    <div class="ali-cta-left">
                        <h3><i class="fab fa-whatsapp"></i> Order via WhatsApp</h3>
                        <p>Quick ordering without account</p>
                        <a href="https://wa.me/250783654454" target="_blank" class="ali-cta-btn"><i class="fab fa-whatsapp"></i> Chat Now</a>
                    </div>
                    <div class="ali-cta-right"><span>+250 783 654 454</span></div>
                </div>
            </div>
        </section>
    </div>

    <!-- Right Side Panel (desktop only) -->
    <aside class="ali-side-panel ali-side-right">
        <div class="ali-side-title"><i class="fas fa-star"></i> Best Sellers</div>
        <?php 
        $right_products = array_slice($side_products, 10, 10);
        foreach ($right_products as $rp): 
            $rp_img = !empty($rp['picture']) ? $rp['picture'] : 'no-image.png';
        ?>
        <a href="index.php?product-detail&product=<?php echo $rp['product_id']; ?>" class="ali-side-product">
            <img src="uploads/<?php echo $rp_img; ?>" alt="<?php echo htmlspecialchars($rp['product_name']); ?>" loading="lazy">
            <div class="ali-side-info">
                <span class="ali-side-name"><?php echo htmlspecialchars(mb_strimwidth($rp['product_name'], 0, 22, '...')); ?></span>
                <span class="ali-side-price"><?php echo number_format($rp['price'] ?? 0, 0); ?> RWF</span>
            </div>
        </a>
        <?php endforeach; ?>
    </aside>
</div>

<!-- Floating WhatsApp (mobile) -->
<a href="https://wa.me/250783654454" target="_blank" class="ali-float-wa" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>

<!-- Hidden cart response container -->
<div id="result_response_cart" style="display:none;"></div>

<!-- ====== JAVASCRIPT ====== -->
<script>
(function() {
    'use strict';

    /* ---------- CONFIG ---------- */
    var currentSlide = 0;
    var slides = document.querySelectorAll('.ali-slide');
    var totalSlides = slides.length;
    var offset = 24;
    var limit = 24;
    var totalProducts = <?php echo (int)$total_products; ?>;
    var isLoading = false;
    var allLoaded = (offset >= totalProducts);
    var customerId = '<?php echo addslashes($customer_id_1); ?>';
    var autoSlideTimer;

    /* ---------- SLIDER ---------- */
    function initSliderDots() {
        var dc = document.getElementById('sliderDots');
        if (!dc || totalSlides === 0) return;
        dc.innerHTML = '';
        for (var i = 0; i < totalSlides; i++) {
            var d = document.createElement('span');
            d.className = i === 0 ? 'dot active' : 'dot';
            d.onclick = (function(x) { return function() { goToSlide(x); }; })(i);
            dc.appendChild(d);
        }
    }

    function showSlide(n) {
        if (totalSlides === 0) return;
        if (n >= totalSlides) currentSlide = 0;
        if (n < 0) currentSlide = totalSlides - 1;
        for (var i = 0; i < slides.length; i++) slides[i].classList.remove('active');
        slides[currentSlide].classList.add('active');
        var dots = document.querySelectorAll('#sliderDots .dot');
        for (var i = 0; i < dots.length; i++) dots[i].classList.remove('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    window.nextSlide = function() { currentSlide++; showSlide(currentSlide); resetAutoSlide(); };
    window.prevSlide = function() { currentSlide--; showSlide(currentSlide); resetAutoSlide(); };
    window.goToSlide = function(n) { currentSlide = n; showSlide(currentSlide); resetAutoSlide(); };

    function resetAutoSlide() {
        clearInterval(autoSlideTimer);
        autoSlideTimer = setInterval(function() { currentSlide++; showSlide(currentSlide); }, 4500);
    }

    if (totalSlides > 0) {
        initSliderDots();
        autoSlideTimer = setInterval(function() { currentSlide++; showSlide(currentSlide); }, 4500);
    }

    // Touch/swipe for slider
    var slider = document.getElementById('bannerSlider');
    if (slider) {
        var sx = 0;
        slider.addEventListener('touchstart', function(e) { sx = e.changedTouches[0].screenX; }, {passive: true});
        slider.addEventListener('touchend', function(e) {
            var diff = sx - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 40) { diff > 0 ? window.nextSlide() : window.prevSlide(); }
        }, {passive: true});
    }

    /* ---------- ADD TO CART (works with both jQuery and vanilla) ---------- */
    window.addCart = function(productId, price, uid, minOrder) {
        var qtyEl = document.getElementById('qty_' + uid);
        var qty = qtyEl ? (parseFloat(qtyEl.value) || parseFloat(minOrder) || 1) : (parseFloat(minOrder) || 1);
        var cid = customerId || (document.getElementById('customer_temp_id') ? document.getElementById('customer_temp_id').value : '');

        // Try global add_to_cart from front-script.php first
        if (typeof window.add_to_cart === 'function') {
            try {
                window.add_to_cart(productId, cid, price, qty);
                showToast('Added to cart!');
                if (qtyEl) qtyEl.value = '';
                return;
            } catch(e) { /* fall through to AJAX */ }
        }

        // jQuery AJAX fallback
        if (typeof $ !== 'undefined' && typeof $.ajax === 'function') {
            $.ajax({
                url: 'includes/add_to_cart.php',
                type: 'POST',
                data: { product_id: productId, customer_id: cid, price: price, quantity: qty },
                success: function(resp) {
                    $('#result_response_cart').html(resp);
                    refreshCartUI();
                    showToast('Added to cart!');
                },
                error: function() { showToast('Failed to add. Try again.', true); }
            });
        } else {
            // Vanilla XHR fallback
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'includes/add_to_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('result_response_cart').innerHTML = xhr.responseText;
                    refreshCartUI();
                    showToast('Added to cart!');
                } else {
                    showToast('Failed to add. Try again.', true);
                }
            };
            xhr.onerror = function() { showToast('Network error.', true); };
            xhr.send('product_id=' + encodeURIComponent(productId) +
                     '&customer_id=' + encodeURIComponent(cid) +
                     '&price=' + encodeURIComponent(price) +
                     '&quantity=' + encodeURIComponent(qty));
        }
        if (qtyEl) qtyEl.value = '';
    };

    function refreshCartUI() {
        if (typeof window.get_cart_items === 'function') { window.get_cart_items(); return; }
        if (typeof window.load_cart_items === 'function') { window.load_cart_items(); return; }
        // Manual badge update
        var cid = customerId || (document.getElementById('customer_temp_id') ? document.getElementById('customer_temp_id').value : '');
        if (!cid) return;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'includes/get_cart_count.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var count = parseInt(xhr.responseText) || 0;
                var badges = document.querySelectorAll('[id^="cart_items_count_"]');
                for (var i = 0; i < badges.length; i++) badges[i].textContent = count;
                var bottomBadge = document.querySelectorAll('.gb-bottom-nav-badge');
                for (var i = 0; i < bottomBadge.length; i++) bottomBadge[i].textContent = count;
            }
        };
        xhr.send('customer_id=' + encodeURIComponent(cid));
    }

    /* ---------- TOAST ---------- */
    function showToast(text, isError) {
        var old = document.querySelector('.ali-toast');
        if (old) old.remove();
        var t = document.createElement('div');
        t.className = 'ali-toast' + (isError ? ' ali-toast-error' : '');
        t.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i> ' + text;
        document.body.appendChild(t);
        requestAnimationFrame(function() { t.classList.add('ali-toast-show'); });
        setTimeout(function() { t.classList.remove('ali-toast-show'); }, 2200);
        setTimeout(function() { if (t.parentNode) t.remove(); }, 2700);
    }
    window.showMsg = showToast;

    /* ---------- INFINITE SCROLL ---------- */
    function loadMore() {
        if (isLoading || allLoaded) return;
        isLoading = true;
        var spinner = document.getElementById('loadingSpinner');
        if (spinner) spinner.style.display = 'flex';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'includes/home_load_more.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            isLoading = false;
            if (spinner) spinner.style.display = 'none';
            if (xhr.status === 200 && xhr.responseText.trim() !== '') {
                document.getElementById('productsGrid').insertAdjacentHTML('beforeend', xhr.responseText);
                offset += limit;
                if (offset >= totalProducts) {
                    allLoaded = true;
                    var nm = document.getElementById('noMoreMsg');
                    if (nm) nm.style.display = 'block';
                }
            } else {
                allLoaded = true;
                var nm = document.getElementById('noMoreMsg');
                if (nm) nm.style.display = 'block';
            }
        };
        xhr.onerror = function() {
            isLoading = false;
            if (spinner) spinner.style.display = 'none';
        };
        xhr.send('offset=' + offset + '&limit=' + limit + '&customer_id=' + encodeURIComponent(customerId));
    }
    window.loadMore = loadMore;

    // IntersectionObserver for infinite scroll
    if (totalProducts > 24) {
        var sentinel = document.getElementById('infiniteScrollArea');
        if (sentinel && 'IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                if (entries[0].isIntersecting && !isLoading && !allLoaded) {
                    loadMore();
                }
            }, { rootMargin: '400px' });
            observer.observe(sentinel);
        } else {
            // Fallback: scroll event
            var scrollTimer;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    if (allLoaded || isLoading) return;
                    var scrollPos = window.innerHeight + window.pageYOffset;
                    var docHeight = document.documentElement.scrollHeight;
                    if (scrollPos >= docHeight - 600) loadMore();
                }, 100);
            }, {passive: true});
        }
    }

})();
</script>

<!-- ====== STYLES ====== -->
<style>
/* ============================================================
   HOME.PHP STYLES — Mobile-first, AliExpress-inspired
   ============================================================ */

/* --- Reset for home --- */
.ali-home-full *, .ali-home-full *::before, .ali-home-full *::after { box-sizing: border-box; }

/* --- Layout --- */
.ali-home-full {
    display: flex;
    width: 100%;
    background: #f0f0f0;
    min-height: 100vh;
}
.ali-home-main {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}
.ali-container {
    padding: 0 10px;
}

/* --- Side Panels (desktop only) --- */
.ali-side-panel {
    width: 185px;
    background: #fff;
    padding: 8px;
    flex-shrink: 0;
    overflow-y: auto;
    max-height: 100vh;
    position: sticky;
    top: 0;
    display: none; /* hidden by default, shown on large screens */
}
.ali-side-left { border-right: 1px solid #eee; }
.ali-side-right { border-left: 1px solid #eee; }
.ali-side-title {
    background: linear-gradient(135deg, #ff5000, #ff7043);
    color: #fff;
    padding: 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ali-side-product {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px;
    border-radius: 8px;
    text-decoration: none;
    margin-bottom: 4px;
    background: #fafafa;
    transition: all 0.2s;
}
.ali-side-product:hover {
    background: #fff5f0;
    transform: translateX(2px);
}
.ali-side-product img {
    width: 46px;
    height: 46px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}
.ali-side-info { flex: 1; min-width: 0; }
.ali-side-name {
    display: block;
    font-size: 11px;
    color: #333;
    line-height: 1.3;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ali-side-price {
    font-size: 12px;
    color: #ff5000;
    font-weight: 700;
}

/* ============================================================
   HERO
   ============================================================ */
.ali-hero {
    background: #fff;
    padding: 10px;
}
.ali-hero-row {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.ali-hero-banner { flex: 1; min-width: 0; }
.ali-hero-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

/* Banner Slider */
.ali-banner-slider {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 16 / 7;
    min-height: 160px;
    max-height: 340px;
    user-select: none;
    -webkit-user-select: none;
}
.ali-slide {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    padding: 16px;
    opacity: 0;
    transition: opacity 0.6s ease;
    pointer-events: none;
}
.ali-slide.active {
    opacity: 1;
    z-index: 1;
    pointer-events: auto;
}
.ali-slide-content {
    flex: 1;
    color: #fff;
    z-index: 2;
    text-shadow: 0 1px 3px rgba(0,0,0,0.15);
    min-width: 0;
}
.ali-slide-tag {
    display: inline-block;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(4px);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.ali-slide-content h2 {
    font-size: 16px;
    margin: 0 0 4px;
    font-weight: 800;
    line-height: 1.2;
}
.ali-slide-content p {
    font-size: 12px;
    margin: 0 0 6px;
    opacity: 0.95;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.ali-slide-price {
    font-size: 15px;
    font-weight: 800;
    margin-bottom: 10px;
}
.ali-slide-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    color: #333;
    padding: 8px 16px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 700;
    font-size: 11px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.12);
    transition: all 0.2s;
}
.ali-slide-btn:hover { background: #ff5000; color: #fff; }
.ali-slide-img {
    position: absolute;
    right: 12px;
    bottom: 12px;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ali-slide-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
}

/* Slider Controls */
.ali-slider-controls {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 6px;
    z-index: 5;
    pointer-events: none;
}
.ali-slider-controls button {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.85);
    cursor: pointer;
    font-size: 11px;
    color: #333;
    pointer-events: auto;
    transition: all 0.2s;
    box-shadow: 0 1px 6px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}
.ali-slider-controls button:hover { background: #ff5000; color: #fff; }
.ali-slider-dots {
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 5px;
    z-index: 5;
}
.dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: all 0.3s;
}
.dot.active {
    background: #fff;
    width: 20px;
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
}

/* Side Cards */
.ali-side-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 10px;
    text-decoration: none;
    color: #fff;
    transition: all 0.2s;
}
.ali-side-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.15); color: #fff; }
.ali-side-card i { font-size: 20px; flex-shrink: 0; }
.ali-side-card strong { display: block; font-size: 12px; margin-bottom: 1px; }
.ali-side-card span { font-size: 10px; opacity: 0.9; }
.ali-card-green { background: linear-gradient(135deg, #25d366, #128c7e); }
.ali-card-orange { background: linear-gradient(135deg, #ff9f43, #ff6b6b); }
.ali-card-blue { background: linear-gradient(135deg, #4facfe, #00f2fe); }

/* ============================================================
   SECTIONS
   ============================================================ */
.ali-section {
    background: #fff;
    padding: 14px 0;
    margin-top: 8px;
}
.ali-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding: 0 2px;
}
.ali-section-header h2 {
    font-size: 15px;
    margin: 0;
    color: #222;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
}
.ali-section-header h2 i { color: #ff5000; font-size: 14px; }
.ali-section-header a {
    color: #ff5000;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 3px;
}
.ali-count { font-size: 12px; color: #888; font-weight: 500; }

/* ============================================================
   CATEGORIES
   ============================================================ */
.ali-categories-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    margin: 0 -10px;
    padding: 0 10px;
}
.ali-categories-wrap::-webkit-scrollbar { display: none; }
.ali-categories {
    display: flex;
    gap: 6px;
    padding-bottom: 4px;
}
.ali-cat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    padding: 8px 4px;
    border-radius: 10px;
    transition: all 0.2s;
    min-width: 72px;
    flex-shrink: 0;
}
.ali-cat-item:hover { background: #fff5f0; }
.ali-cat-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: 2px solid;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;
    overflow: hidden;
    flex-shrink: 0;
}
.ali-cat-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.ali-cat-name {
    font-size: 10px;
    color: #333;
    text-align: center;
    font-weight: 600;
    line-height: 1.2;
    max-width: 72px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.ali-cat-count { font-size: 9px; color: #999; margin-top: 1px; }

/* ============================================================
   FEATURED
   ============================================================ */
.ali-featured-section { background: linear-gradient(180deg, #fff5f0, #fff); }
.ali-featured-header {
    background: linear-gradient(135deg, #ff5000, #ff7043) !important;
    margin: -14px -10px 12px -10px;
    padding: 10px 12px !important;
    border-radius: 0;
}
.ali-featured-title {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #fff;
}
.ali-featured-title i { font-size: 16px; }
.ali-featured-title h2 { margin: 0; font-size: 14px; color: #fff !important; font-weight: 700; }
.ali-badge {
    background: #fff;
    color: #ff5000;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
}
.ali-featured-header a { color: #fff !important; }

.ali-featured-scroll {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 6px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    margin: 0 -10px;
    padding-left: 10px;
    padding-right: 10px;
}
.ali-featured-scroll::-webkit-scrollbar { display: none; }

.ali-featured-card {
    min-width: 140px;
    max-width: 160px;
    flex-shrink: 0;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #eee;
    scroll-snap-align: start;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
}
.ali-featured-card:hover { box-shadow: 0 3px 12px rgba(255,80,0,0.12); border-color: #ff5000; }
.ali-featured-img {
    position: relative;
    overflow: hidden;
    background: #f5f5f5;
    aspect-ratio: 1 / 1;
}
.ali-featured-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.ali-hot-tag {
    position: absolute;
    top: 6px;
    left: 6px;
    background: #ff5000;
    color: #fff;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
}
.ali-featured-info {
    padding: 8px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.ali-featured-name {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: 12px;
    color: #333;
    text-decoration: none;
    line-height: 1.35;
    margin-bottom: 4px;
    font-weight: 500;
    flex: 1;
}
.ali-featured-name:hover { color: #ff5000; }
.ali-featured-price { margin-bottom: 6px; }
.ali-featured-price strong { font-size: 14px; color: #ff5000; font-weight: 800; }
.ali-featured-price span { font-size: 10px; color: #999; }
.ali-featured-cart { display: flex; gap: 4px; margin-top: auto; }
.ali-mini-qty {
    width: 42px;
    height: 30px;
    border: 1px solid #ddd;
    border-radius: 6px;
    text-align: center;
    font-size: 12px;
    -moz-appearance: textfield;
    -webkit-appearance: none;
    appearance: none;
}
.ali-mini-qty::-webkit-inner-spin-button,
.ali-mini-qty::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.ali-mini-qty:focus { outline: none; border-color: #ff5000; }
.ali-featured-cart button {
    flex: 1;
    height: 30px;
    background: #ff5000;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    transition: background 0.2s;
}
.ali-featured-cart button:hover { background: #e64500; }
.ali-featured-cart button:active { transform: scale(0.97); }

/* ============================================================
   CATEGORY BANNERS
   ============================================================ */
.ali-banners-section { background: #f0f0f0; padding: 10px 0; }
.ali-banners-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}
.ali-banner-card {
    display: flex;
    align-items: center;
    padding: 14px;
    border-radius: 12px;
    text-decoration: none;
    color: #fff;
    min-height: 90px;
    transition: all 0.25s;
    overflow: hidden;
}
.ali-banner-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.15); color: #fff; }
.ali-banner-text { flex: 1; min-width: 0; z-index: 1; }
.ali-banner-text span { font-size: 10px; opacity: 0.9; font-weight: 500; }
.ali-banner-text h3 {
    margin: 3px 0;
    font-size: 14px;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ali-banner-text p { margin: 0; font-size: 11px; display: flex; align-items: center; gap: 4px; font-weight: 600; }
.ali-banner-img {
    width: 70px;
    height: 70px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ali-banner-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 3px 8px rgba(0,0,0,0.2));
}

/* ============================================================
   PRODUCT GRID (shared for category & all products)
   ============================================================ */
.ali-products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}
.ali-product {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
}
.ali-product:hover { box-shadow: 0 3px 14px rgba(0,0,0,0.08); border-color: #ff5000; }
.ali-product-img-link { display: block; text-decoration: none; }
.ali-product-img {
    background: #f5f5f5;
    overflow: hidden;
    aspect-ratio: 1 / 1;
}
.ali-product-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s;
}
.ali-product:hover .ali-product-img img { transform: scale(1.04); }
.ali-product-info {
    padding: 8px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.ali-product-cat {
    font-size: 10px;
    color: #999;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ali-product-name {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: 12px;
    color: #222;
    text-decoration: none;
    line-height: 1.35;
    margin-bottom: 4px;
    font-weight: 500;
    flex: 1;
}
.ali-product-name:hover { color: #ff5000; }
.ali-product-price { margin