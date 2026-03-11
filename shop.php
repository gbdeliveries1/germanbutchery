<!-- ====== ALIEXPRESS STYLE SHOP ====== -->
<div class="ali-shop-wrapper">
    <div class="container-fluid ali-container">
        <div class="row">

            <!-- ================= LEFT FILTER SIDEBAR (DESKTOP) ================= -->
            <div class="col-xl-2 col-lg-3 d-none d-lg-block ali-sidebar-col">
                <div class="ali-sidebar-sticky">
                    <div class="ali-category-box">
                        <h4 class="ali-category-title"><i class="fas fa-list-ul"></i> CATEGORIES</h4>
                        <ul class="ali-category-list">
                            <?php
                            $sql_cat = "SELECT * FROM product_category";
                            $result_cat = $conn->query($sql_cat);
                            while ($row_cat = $result_cat->fetch_assoc()) {
                                $category_id = $row_cat['category_id'];
                                $category_name = $row_cat['category_name'];
                                
                                $sql_count = "SELECT COUNT(*) AS cnt FROM product WHERE category_id='$category_id'";
                                $result_count = $conn->query($sql_count);
                                $product_count = $result_count->fetch_assoc()['cnt'];
                            ?>
                            <li class="ali-category-item">
                                <a href="index.php?shop-search&search=<?php echo urlencode($category_name); ?>" class="ali-category-link">
                                    <span class="ali-cat-name"><?php echo htmlspecialchars($category_name); ?></span>
                                    <span class="ali-cat-count">(<?php echo $product_count; ?>)</span>
                                </a>
                                <?php
                                $sql_sub = "SELECT * FROM product_sub_category WHERE category_id='$category_id'";
                                $result_sub = $conn->query($sql_sub);
                                if ($result_sub->num_rows > 0):
                                ?>
                                <ul class="ali-subcategory-list">
                                    <?php while ($row_sub = $result_sub->fetch_assoc()): ?>
                                    <li>
                                        <a href="index.php?shop-search&search=<?php echo urlencode($row_sub['sub_category_name']); ?>">
                                            <?php echo htmlspecialchars($row_sub['sub_category_name']); ?>
                                        </a>
                                    </li>
                                    <?php endwhile; ?>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ================= MAIN SHOP ================= -->
            <div class="col-xl-10 col-lg-9 col-md-12 ali-main-col">
                <div class="shop-p">

                    <?php
                    $pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;
                    if ($pageno < 1) $pageno = 1;
                    
                    $sortby = isset($_GET['sortby']) ? $_GET['sortby'] : 'RAND()';
                    $condition = isset($_GET['condition']) ? $_GET['condition'] : '';

                    $allowed_sorts = [
                        'RAND()',
                        'p.register_date DESC',
                        'p.register_date ASC',
                        'CAST(pr.price AS SIGNED INTEGER) ASC',
                        'CAST(pr.price AS SIGNED INTEGER) DESC',
                        'CAST(p.product_rating AS SIGNED INTEGER) DESC'
                    ];
                    if (!in_array($sortby, $allowed_sorts)) {
                        $sortby = 'RAND()';
                    }

                    $per_page = 24;
                    $offset = ($pageno - 1) * $per_page;

                    $count_sql = "SELECT COUNT(p.product_id) FROM product p JOIN product_price pr ON pr.product_id = p.product_id " . $condition;
                    $count_result = mysqli_query($conn, $count_sql);
                    $total_rows = $count_result ? mysqli_fetch_array($count_result)[0] : 0;
                    $total_pages = $total_rows > 0 ? ceil($total_rows / $per_page) : 1;

                    $customer_id = isset($_SESSION['GBDELIVERING_TEMP_CUSTOMER_USER_2021']) ? $_SESSION['GBDELIVERING_TEMP_CUSTOMER_USER_2021'] : '';
                    ?>

                    <!-- ================= TOOLBAR ================= -->
                    <div class="ali-toolbar">
                        <div class="ali-toolbar-info">
                            <span class="ali-count"><?php echo number_format($total_rows); ?></span> items found 
                            <span class="d-none d-sm-inline">| Page <span class="ali-current-page"><?php echo $pageno; ?></span> of <?php echo $total_pages; ?></span>
                        </div>

                        <div class="ali-toolbar-actions">
                            <!-- Mobile Categories Trigger -->
                            <button class="ali-mobile-filter-btn d-lg-none" onclick="toggleMobileCategories()">
                                <i class="fas fa-filter"></i> Categories
                            </button>

                            <div class="ali-sort-wrapper">
                                <span class="d-none d-sm-inline">Sort:</span>
                                <select class="ali-sort-select" id="sortby_select" onchange="location.href='index.php?shop&sortby='+encodeURIComponent(this.value)+'&condition=<?php echo urlencode($condition); ?>&pageno=1'">
                                    <option value="RAND()" <?php if($sortby === 'RAND()') echo 'selected'; ?>>Best Match</option>
                                    <option value="p.register_date DESC" <?php if($sortby === 'p.register_date DESC') echo 'selected'; ?>>Newest</option>
                                    <option value="CAST(pr.price AS SIGNED INTEGER) ASC" <?php if($sortby === 'CAST(pr.price AS SIGNED INTEGER) ASC') echo 'selected'; ?>>Lowest Price</option>
                                    <option value="CAST(pr.price AS SIGNED INTEGER) DESC" <?php if($sortby === 'CAST(pr.price AS SIGNED INTEGER) DESC') echo 'selected'; ?>>Highest Price</option>
                                    <option value="CAST(p.product_rating AS SIGNED INTEGER) DESC" <?php if($sortby === 'CAST(p.product_rating AS SIGNED INTEGER) DESC') echo 'selected'; ?>>Top Rated</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- ================= PRODUCTS GRID ================= -->
                    <div class="ali-products-grid">

                        <?php
                        $sql = "SELECT p.product_id, p.product_name, p.product_unit, p.short_description, p.product_rating, pr.price 
                                FROM product p 
                                JOIN product_price pr ON pr.product_id = p.product_id 
                                $condition 
                                ORDER BY $sortby 
                                LIMIT $offset, $per_page";

                        $res = $conn->query($sql);

                        if ($res && $res->num_rows > 0):
                            $product_index = 0;
                            while ($row = $res->fetch_assoc()):
                                $product_id = $row['product_id'];
                                $product_name = htmlspecialchars($row['product_name']);
                                $product_unit = htmlspecialchars($row['product_unit']);
                                $short_description = htmlspecialchars($row['short_description']);
                                $product_rating = isset($row['product_rating']) ? (float)$row['product_rating'] : 0;
                                $price = (float)$row['price'];

                                $img_query = $conn->query("SELECT picture FROM product_picture WHERE product_id='$product_id' ORDER BY register_date DESC LIMIT 1");
                                $img = ($img_query && $img_query->num_rows > 0) ? $img_query->fetch_assoc()['picture'] : 'no-image.png';

                                $full_stars = floor($product_rating);
                                $half_star = ($product_rating - $full_stars) >= 0.5;
                                $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                
                                $unique_id = 'prod_' . $product_id . '_' . $product_index;
                        ?>

                        <!-- ===== PRODUCT CARD ===== -->
                        <div class="ali-product-card">
                            <div class="ali-product-image">
                                <a href="index.php?product-detail&product=<?php echo $product_id; ?>">
                                    <img src="uploads/<?php echo $img; ?>" alt="<?php echo $product_name; ?>" loading="lazy" onerror="this.src='assets/images/no-image.png'">
                                </a>
                                <div class="ali-product-actions">
                                    <a href="#quick-look" data-toggle="modal" data-modal="modal" data-product-id="<?php echo $product_id; ?>" class="ali-action-btn" aria-label="Quick Look">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (isset($login_status) && $login_status): ?>
                                        <button onclick="add_to_wishlist('<?php echo $product_id; ?>','<?php echo $customer_id; ?>')" class="ali-action-btn" aria-label="Add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="index.php?sign-in" class="ali-action-btn" aria-label="Sign in to add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="ali-product-info">
                                <div class="ali-product-title">
                                    <a href="index.php?product-detail&product=<?php echo $product_id; ?>">
                                        <?php echo mb_strimwidth($product_name, 0, 45, '...'); ?>
                                    </a>
                                </div>

                                <div class="ali-product-rating">
                                    <div class="ali-stars">
                                        <?php
                                        echo str_repeat('<i class="fas fa-star"></i>', $full_stars);
                                        if ($half_star) echo '<i class="fas fa-star-half-alt"></i>';
                                        echo str_repeat('<i class="far fa-star"></i>', $empty_stars);
                                        ?>
                                    </div>
                                    <span class="ali-sold"><?php echo rand(50, 500); ?> sold</span>
                                </div>

                                <div class="ali-product-price">
                                    <span class="ali-price-main" data-price="<?php echo $price; ?>"><?php echo number_format($price, 0); ?> RWF</span>
                                    <span class="ali-price-unit">/ <?php echo $product_unit; ?></span>
                                </div>

                                <div class="ali-card-footer">
                                    <!-- QUANTITY INPUT -->
                                    <div class="ali-quantity-wrapper">
                                        <button type="button" class="ali-qty-btn" aria-label="Decrease" onclick="decreaseQty('<?php echo $unique_id; ?>')">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" 
                                               id="qty_<?php echo $unique_id; ?>" 
                                               class="ali-qty-input" 
                                               value="" 
                                               placeholder="Qty"
                                               aria-label="Quantity"
                                               oninput="validateQtyInput(this)"
                                               onblur="formatQtyOnBlur(this)">
                                        <button type="button" class="ali-qty-btn" aria-label="Increase" onclick="increaseQty('<?php echo $unique_id; ?>')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <!-- ADD TO CART BUTTON -->
                                    <button type="button" onclick="addToCartWithQty('<?php echo $product_id; ?>','<?php echo $customer_id; ?>','<?php echo $price; ?>','<?php echo $unique_id; ?>')" class="ali-add-cart-btn" aria-label="Add to cart">
                                        <i class="fas fa-shopping-cart"></i> <span class="btn-text">Add</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php 
                            $product_index++;
                            endwhile;
                        else:
                        ?>
                        <div class="ali-no-products">
                            <i class="fas fa-box-open"></i>
                            <h3>No products found</h3>
                            <p>Try adjusting your filters or search terms</p>
                            <a href="index.php?shop" class="ali-reset-btn">Clear Filters</a>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Hidden div for cart response -->
                    <div id="result_response_cart" style="display: none;"></div>

                    <!-- ================= PAGINATION ================= -->
                    <?php if($total_pages > 1): ?>
                    <div class="ali-pagination-wrapper">
                        <ul class="ali-pagination">
                            <li class="<?php if($pageno <= 1) echo 'disabled'; ?> d-none d-sm-block">
                                <a href="<?php echo ($pageno <= 1) ? '#' : 'index.php?shop&pageno=1&sortby='.urlencode($sortby).'&condition='.urlencode($condition); ?>">
                                    <i class="fas fa-angle-double-left"></i> First
                                </a>
                            </li>

                            <li class="<?php if($pageno <= 1) echo 'disabled'; ?>">
                                <a href="<?php echo ($pageno <= 1) ? '#' : 'index.php?shop&pageno='.($pageno-1).'&sortby='.urlencode($sortby).'&condition='.urlencode($condition); ?>">
                                    <i class="fas fa-angle-left"></i> Prev
                                </a>
                            </li>

                            <?php
                            // Mobile pagination shows fewer numbers to fit screen
                            $start_page = max(1, $pageno - 2);
                            $end_page = min($total_pages, $pageno + 2);
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                            <li class="<?php if($i == $pageno) echo 'active'; ?> <?php if(abs($pageno - $i) > 1) echo 'd-none d-sm-block'; ?>">
                                <a href="index.php?shop&pageno=<?php echo $i; ?>&sortby=<?php echo urlencode($sortby); ?>&condition=<?php echo urlencode($condition); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <li class="<?php if($pageno >= $total_pages) echo 'disabled'; ?>">
                                <a href="<?php echo ($pageno >= $total_pages) ? '#' : 'index.php?shop&pageno='.($pageno+1).'&sortby='.urlencode($sortby).'&condition='.urlencode($condition); ?>">
                                    Next <i class="fas fa-angle-right"></i>
                                </a>
                            </li>

                            <li class="<?php if($pageno >= $total_pages) echo 'disabled'; ?> d-none d-sm-block">
                                <a href="index.php?shop&pageno=<?php echo $total_pages; ?>&sortby=<?php echo urlencode($sortby); ?>&condition=<?php echo urlencode($condition); ?>">
                                    Last <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- ================= AD ================= -->
                    <div class="ali-ad-wrapper">
                        <ins class="adsbygoogle"
                             style="display:block;text-align:center"
                             data-ad-client="ca-pub-5745320266901948"
                             data-ad-slot="6630621214"
                             data-ad-format="auto"
                             data-full-width-responsive="true"></ins>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- ====== END SHOP ====== -->

<!-- ====== MOBILE CATEGORIES DRAWER (NEW UX) ====== -->
<div class="ali-mobile-cat-overlay" id="mobileCatOverlay" onclick="toggleMobileCategories()"></div>
<div class="ali-mobile-cat-drawer" id="mobileCatDrawer">
    <div class="ali-drawer-header">
        <h4><i class="fas fa-list-ul"></i> Categories</h4>
        <button class="ali-close-drawer" onclick="toggleMobileCategories()">&times;</button>
    </div>
    <div class="ali-drawer-body">
        <ul class="ali-category-list">
            <?php
            // Re-run query for mobile drawer
            $result_cat_mobile = $conn->query($sql_cat);
            if($result_cat_mobile) {
                while ($row_cat = $result_cat_mobile->fetch_assoc()) {
                    $category_id = $row_cat['category_id'];
                    $category_name = $row_cat['category_name'];
                    
                    $sql_count = "SELECT COUNT(*) AS cnt FROM product WHERE category_id='$category_id'";
                    $product_count = $conn->query($sql_count)->fetch_assoc()['cnt'];
            ?>
            <li class="ali-category-item">
                <a href="index.php?shop-search&search=<?php echo urlencode($category_name); ?>" class="ali-category-link">
                    <span class="ali-cat-name"><?php echo htmlspecialchars($category_name); ?></span>
                    <span class="ali-cat-count"><?php echo $product_count; ?></span>
                </a>
            </li>
            <?php 
                }
            } 
            ?>
        </ul>
    </div>
</div>

<!-- ====== JAVASCRIPT ====== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof gbUpdatePrices === 'function') gbUpdatePrices();
});

// Category Drawer Toggle
function toggleMobileCategories() {
    var overlay = document.getElementById('mobileCatOverlay');
    var drawer = document.getElementById('mobileCatDrawer');
    if(overlay.classList.contains('active')) {
        overlay.classList.remove('active');
        drawer.classList.remove('active');
        document.body.style.overflow = 'auto';
    } else {
        overlay.classList.add('active');
        drawer.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

// Decimal Logic
function increaseQty(uniqueId) {
    var input = document.getElementById('qty_' + uniqueId);
    var currentVal = parseFloat(input.value) || 0;
    var increment = (currentVal % 1 !== 0) ? 0.5 : 1;
    input.value = formatDecimal(currentVal + increment);
}

function decreaseQty(uniqueId) {
    var input = document.getElementById('qty_' + uniqueId);
    var currentVal = parseFloat(input.value) || 0;
    var decrement = (currentVal % 1 !== 0) ? 0.5 : 1;
    var newVal = currentVal - decrement;
    input.value = newVal > 0 ? formatDecimal(newVal) : '';
}

function formatDecimal(num) {
    if (num === '' || isNaN(num)) return '';
    return parseFloat(num.toFixed(3)).toString();
}

function validateQtyInput(input) {
    var value = input.value.replace(/[^0-9.]/g, '');
    var parts = value.split('.');
    input.value = parts.length > 2 ? parts[0] + '.' + parts.slice(1).join('') : value;
}

function formatQtyOnBlur(input) {
    var value = input.value.trim();
    if (value === '' || value === '.') { input.value = ''; return; }
    var num = parseFloat(value);
    input.value = (isNaN(num) || num <= 0) ? '' : formatDecimal(num);
}

// Add to Cart Logic
function addToCartWithQty(productId, customerId, price, uniqueId) {
    var qtyInput = document.getElementById('qty_' + uniqueId);
    var quantityStr = qtyInput.value.trim();
    
    if (quantityStr === '' || quantityStr === '0') {
        showCartNotification('Please enter a quantity', 'warning');
        qtyInput.focus();
        qtyInput.classList.add('ali-qty-error');
        setTimeout(function() { qtyInput.classList.remove('ali-qty-error'); }, 2000);
        return;
    }
    
    var quantity = parseFloat(quantityStr);
    if (isNaN(quantity) || quantity <= 0) {
        showCartNotification('Please enter a valid quantity', 'warning');
        qtyInput.focus();
        return;
    }
    
    if (!customerId || customerId === '') {
        var tempId = document.getElementById('customer_temp_id');
        if (tempId && tempId.value) customerId = tempId.value;
    }
    
    // UI Feedback on button
    var btn = event.currentTarget;
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    if (typeof add_to_cart === 'function') {
        add_to_cart(productId, customerId, price, quantity);
        setTimeout(function() { btn.innerHTML = originalHtml; btn.disabled = false; }, 800);
    } else {
        fallbackAddToCart(productId, customerId, price, quantity, btn, originalHtml);
    }
    
    qtyInput.value = '';
}

function fallbackAddToCart(productId, customerId, price, quantity, btn, originalHtml) {
    $.ajax({
        url: 'action/insert.php',
        type: 'POST',
        data: {
            action: 'ADD_TO_CART',
            product_id: productId,
            customer_id: customerId,
            price: price,
            product_quantity: quantity
        },
        success: function(response) {
            $('#result_response_cart').html(response);
            showCartNotification('Product added to cart!', 'success');
            if (typeof get_cart_items === 'function') get_cart_items();
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        },
        error: function() {
            showCartNotification('Error adding to cart.', 'error');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    });
}

function showCartNotification(message, type) {
    if (typeof Swal !== 'undefined') {
        var icon = type === 'warning' ? 'warning' : (type === 'error' ? 'error' : 'success');
        Swal.fire({ icon: icon, title: message, showConfirmButton: false, timer: 2000, toast: true, position: 'top-end' });
        return;
    }
    // Fallback UI toast logic handled gracefully here if swal missing
}
</script>

<!-- ====== RESPONSIVE CSS ====== -->
<style>
:root {
    --primary: #ff4747;
    --secondary: #ff9f43;
    --ali-orange: #ff5000;
    --ali-red: #e53935;
    --ali-dark: #222;
    --ali-gray: #666;
    --ali-light-gray: #f9f9f9;
    --ali-border: #eee;
    --radius: 12px;
}

body {
    background-color: #f5f6f8;
}

.ali-shop-wrapper {
    padding: 20px 0;
}

.ali-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Sidebar */
.ali-sidebar-col {
    padding-right: 0;
}

.ali-sidebar-sticky {
    position: sticky;
    top: 80px;
    background: #fff;
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.ali-category-title {
    font-size: 16px;
    font-weight: 800;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    color: var(--ali-dark);
}
.ali-category-title i { color: var(--ali-orange); margin-right: 8px; }

.ali-category-list { list-style: none; padding: 0; margin: 0; }
.ali-category-item { margin-bottom: 8px; }
.ali-category-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    background: #fff;
    border-radius: 8px;
    text-decoration: none;
    color: var(--ali-dark);
    font-size: 14px;
    font-weight: 500;
    transition: 0.2s;
    border: 1px solid transparent;
}
.ali-category-link:hover {
    background: #fff5f0;
    color: var(--ali-orange);
    border-color: #ffe0d1;
}
.ali-cat-count {
    background: #f0f0f0;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
    color: var(--ali-gray);
}
.ali-category-link:hover .ali-cat-count { background: var(--ali-orange); color: #fff; }

.ali-subcategory-list {
    list-style: none;
    padding-left: 20px;
    margin-top: 5px;
    border-left: 2px solid #f0f0f0;
    margin-left: 15px;
}
.ali-subcategory-list a {
    display: block;
    padding: 6px 0;
    color: var(--ali-gray);
    font-size: 13px;
    text-decoration: none;
}
.ali-subcategory-list a:hover { color: var(--ali-orange); }

/* Toolbar */
.ali-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 15px 20px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.ali-toolbar-info { font-size: 14px; color: var(--ali-gray); }
.ali-count { font-weight: 700; color: var(--ali-dark); }
.ali-toolbar-actions { display: flex; align-items: center; gap: 15px; }

.ali-sort-select {
    padding: 8px 30px 8px 15px;
    border: 1px solid var(--ali-border);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: var(--ali-dark);
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 12px center;
    appearance: none;
    outline: none;
    cursor: pointer;
}

/* Grid System */
.ali-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}

/* Product Card */
.ali-product-card {
    background: #fff;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    border: 1px solid transparent;
}
.ali-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    border-color: #ffe0d1;
}

.ali-product-image {
    aspect-ratio: 1/1;
    position: relative;
    background: #fff;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ali-product-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: 0.4s;
}
.ali-product-card:hover .ali-product-image img { transform: scale(1.05); }

.ali-product-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    opacity: 0;
    transform: translateX(10px);
    transition: 0.3s;
}
.ali-product-card:hover .ali-product-actions { opacity: 1; transform: translateX(0); }
.ali-action-btn {
    width: 36px;
    height: 36px;
    background: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ali-gray);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    text-decoration: none;
}
.ali-action-btn:hover { background: var(--ali-orange); color: #fff; }

.ali-product-info {
    padding: 15px;
    display: flex;
    flex-direction: column;
    flex: 1;
    background: #fafafa;
}
.ali-product-title { margin-bottom: 8px; }
.ali-product-title a {
    color: var(--ali-dark);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 40px;
}
.ali-product-title a:hover { color: var(--ali-orange); }

.ali-product-rating {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}
.ali-stars { color: #ffc107; font-size: 11px; }
.ali-sold { font-size: 12px; color: var(--ali-gray); }

.ali-product-price { margin-bottom: 15px; }
.ali-price-main { font-size: 18px; font-weight: 800; color: var(--ali-orange); }
.ali-price-unit { font-size: 12px; color: var(--ali-gray); }

.ali-card-footer {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ali-quantity-wrapper {
    display: flex;
    border: 1px solid var(--ali-border);
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
}
.ali-quantity-wrapper:focus-within { border-color: var(--ali-orange); }

.ali-qty-btn {
    width: 32px;
    background: #f0f0f0;
    border: none;
    color: var(--ali-dark);
    font-size: 12px;
    cursor: pointer;
    transition: 0.2s;
}
.ali-qty-btn:hover { background: var(--ali-orange); color: #fff; }

.ali-qty-input {
    flex: 1;
    width: 100%;
    min-width: 0;
    height: 32px;
    border: none;
    text-align: center;
    font-size: 14px;
    font-weight: 700;
    color: var(--ali-dark);
}
.ali-qty-input:focus { outline: none; }

.ali-add-cart-btn {
    width: 100%;
    padding: 10px;
    background: var(--ali-orange);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: 0.2s;
}
.ali-add-cart-btn:hover { background: #e64a19; transform: translateY(-2px); }

/* No Products */
.ali-no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: var(--radius);
}
.ali-no-products i { font-size: 50px; color: #ddd; margin-bottom: 15px; }
.ali-reset-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 24px;
    background: var(--ali-orange);
    color: #fff;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
}

/* Pagination */
.ali-pagination-wrapper { margin-top: 30px; padding-bottom: 20px;}
.ali-pagination {
    display: flex;
    justify-content: center;
    gap: 6px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.ali-pagination li a {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    background: #fff;
    border: 1px solid var(--ali-border);
    border-radius: 8px;
    color: var(--ali-dark);
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: 0.2s;
}
.ali-pagination li a:hover { border-color: var(--ali-orange); color: var(--ali-orange); }
.ali-pagination li.active a { background: var(--ali-orange); border-color: var(--ali-orange); color: #fff; }
.ali-pagination li.disabled a { background: #f5f5f5; color: #ccc; pointer-events: none; }

/* Error Animation */
.ali-qty-error { border: 2px solid #e53935 !important; animation: shake 0.4s; }
@keyframes shake { 0%,100%{transform:translateX(0)} 25%,75%{transform:translateX(-5px)} 50%{transform:translateX(5px)} }

/* =========================================================
   MOBILE RESPONSIVENESS & TOUCH OPTIMIZATION
========================================================= */

/* Mobile Filter Drawer */
.ali-mobile-filter-btn {
    background: #fff;
    border: 1px solid var(--ali-border);
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    color: var(--ali-dark);
    display: flex;
    align-items: center;
    gap: 8px;
}

.ali-mobile-cat-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1040;
    opacity: 0; visibility: hidden; transition: 0.3s;
}
.ali-mobile-cat-overlay.active { opacity: 1; visibility: visible; }

.ali-mobile-cat-drawer {
    position: fixed; bottom: -100%; left: 0; width: 100%; max-height: 85vh;
    background: #fff; border-radius: 20px 20px 0 0; z-index: 1050;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex; flex-direction: column;
}
.ali-mobile-cat-drawer.active { bottom: 0; }

.ali-drawer-header {
    padding: 20px; border-bottom: 1px solid #eee;
    display: flex; justify-content: space-between; align-items: center;
}
.ali-drawer-header h4 { margin: 0; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
.ali-drawer-header i { color: var(--ali-orange); }
.ali-close-drawer { background: none; border: none; font-size: 28px; color: #999; cursor: pointer; line-height: 1; }
.ali-drawer-body { overflow-y: auto; padding: 20px; }

/* Responsive Breakpoints */
@media (max-width: 1200px) {
    .ali-products-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
}

@media (max-width: 992px) {
    .ali-products-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
    .ali-toolbar { position: sticky; top: 60px; z-index: 100; } /* Sticky toolbar on mobile */
}

@media (max-width: 768px) {
    .ali-products-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .ali-product-info { padding: 12px; }
    .ali-product-image { padding: 10px; }
    .ali-product-actions { opacity: 1; transform: none; flex-direction: row; top: auto; bottom: 10px; right: 10px; gap: 5px;}
    .ali-action-btn { width: 30px; height: 30px; font-size: 13px; }
    .ali-price-main { font-size: 16px; }
}

@media (max-width: 576px) {
    .ali-shop-wrapper { padding: 10px 0; margin-top: 0 !important; }
    .ali-toolbar { padding: 10px; flex-direction: column; align-items: stretch; border-radius: 0; margin-bottom: 15px; }
    .ali-toolbar-actions { display: flex; justify-content: space-between; margin-top: 10px; }
    .ali-mobile-filter-btn { flex: 1; justify-content: center; margin-right: 10px; }
    .ali-sort-wrapper { flex: 1; }
    .ali-sort-select { width: 100%; }
    
    .ali-products-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; padding: 0 10px; }
    .ali-product-title a { font-size: 12px; height: 34px; }
    .ali-product-rating { display: none; } /* Hide stars on very small screens to save vertical space */
    .ali-price-main { font-size: 15px; }
    
    /* Bigger touch targets for mobile */
    .ali-qty-btn { width: 36px; height: 36px; font-size: 14px; }
    .ali-qty-input { height: 36px; font-size: 15px; }
    .ali-add-cart-btn { padding: 12px; font-size: 13px; }
    .ali-add-cart-btn .btn-text { display: none; } /* Show only icon on small phones */
    
    .ali-pagination li a { min-width: 32px; height: 32px; font-size: 13px; padding: 0 8px; }
}
</style>