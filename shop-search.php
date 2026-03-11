<?php
/**
 * Shop Search Page - AliExpress Style with Infinite Scroll
 * Complete Working Version (Redesigned)
 */

// Get search term safely
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_escaped = mysqli_real_escape_string($conn, $search);

// Get sorting parameter
$sortby = isset($_GET['sortby']) ? $_GET['sortby'] : 'p.register_date DESC';

// Validate sort parameter
$valid_sorts = array(
    'p.register_date DESC',
    'p.register_date ASC', 
    'CAST(pr.price AS SIGNED INTEGER) ASC',
    'CAST(pr.price AS SIGNED INTEGER) DESC',
    'CAST(p.product_rating AS SIGNED INTEGER) DESC'
);

if (!in_array($sortby, $valid_sorts)) {
    $sortby = 'p.register_date DESC';
}

// Products per page
$per_page = 20;

// Get customer ID from session
$customer_id = '';
if (isset($_SESSION['GBDELIVERING_TEMP_CUSTOMER_USER_2021'])) {
    $customer_id = $_SESSION['GBDELIVERING_TEMP_CUSTOMER_USER_2021'];
}

// Check login status
$is_logged_in = false;
if (isset($login_status)) {
    $is_logged_in = $login_status;
} elseif (isset($_COOKIE['GBDELIVERING_CUSTOMER_USER_2021'])) {
    $is_logged_in = true;
}

// Count total matching products
$count_query = "SELECT COUNT(DISTINCT p.product_id) as total 
                FROM product p 
                INNER JOIN product_price pr ON pr.product_id = p.product_id 
                LEFT JOIN product_category pc ON pc.category_id = p.category_id 
                LEFT JOIN product_sub_category psc ON psc.sub_category_id = p.sub_category_id 
                WHERE p.product_name LIKE '%$search_escaped%' 
                   OR p.short_description LIKE '%$search_escaped%' 
                   OR pc.category_name LIKE '%$search_escaped%' 
                   OR psc.sub_category_name LIKE '%$search_escaped%'";

$count_result = mysqli_query($conn, $count_query);
$total_products = 0;
if ($count_result && $row = mysqli_fetch_assoc($count_result)) {
    $total_products = (int)$row['total'];
}

// Get categories for filter sidebar
$cat_query = "SELECT category_id, category_name FROM product_category ORDER BY category_name ASC";
$cat_result = mysqli_query($conn, $cat_query);
$categories = array();
if ($cat_result) {
    while ($cat = mysqli_fetch_assoc($cat_result)) {
        // Count products in this category
        $cat_id = $cat['category_id'];
        $cat_count_query = "SELECT COUNT(*) as cnt FROM product WHERE category_id = '$cat_id'";
        $cat_count_result = mysqli_query($conn, $cat_count_query);
        $cat_count = 0;
        if ($cat_count_result && $cc = mysqli_fetch_assoc($cat_count_result)) {
            $cat_count = $cc['cnt'];
        }
        $cat['product_count'] = $cat_count;
        $categories[] = $cat;
    }
}

// Get first page of products
$products_query = "SELECT DISTINCT p.product_id, p.product_name, p.product_unit, p.short_description, p.minimum_order,
                          p.product_rating, pr.price, pc.category_name, psc.sub_category_name 
                   FROM product p 
                   INNER JOIN product_price pr ON pr.product_id = p.product_id 
                   LEFT JOIN product_category pc ON pc.category_id = p.category_id 
                   LEFT JOIN product_sub_category psc ON psc.sub_category_id = p.sub_category_id 
                   WHERE p.product_name LIKE '%$search_escaped%' 
                      OR p.short_description LIKE '%$search_escaped%' 
                      OR pc.category_name LIKE '%$search_escaped%' 
                      OR psc.sub_category_name LIKE '%$search_escaped%' 
                   ORDER BY $sortby 
                   LIMIT 0, $per_page";

$products_result = mysqli_query($conn, $products_query);
$products = array();
if ($products_result) {
    while ($prod = mysqli_fetch_assoc($products_result)) {
        $products[] = $prod;
    }
}
?>

<style>
/* ===== CSS VARIABLES & GLOBALS ===== */
:root {
    --ss-primary: #ff5000;
    --ss-secondary: #ff6a33;
    --ss-dark: #111827;
    --ss-gray-dark: #4b5563;
    --ss-gray: #6b7280;
    --ss-gray-light: #e5e7eb;
    --ss-bg: #f3f4f6;
    --ss-surface: #ffffff;
    --ss-radius: 12px;
}

body { background-color: var(--ss-bg); margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
.search-page { padding: 20px 0 60px; }
.search-page * { box-sizing: border-box; }
.container-ss { max-width: 1400px; margin: 0 auto; padding: 0 16px; }

/* ===== SEARCH HEADER (CLEANED UP - NO DUPLICATE SEARCH BOX) ===== */
.search-header {
    background: var(--ss-surface);
    padding: 24px;
    margin-bottom: 20px;
    border-radius: var(--ss-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.search-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--ss-dark);
    margin: 0;
}
.search-title span { color: var(--ss-primary); }

.search-count {
    font-size: 14px;
    color: var(--ss-gray-dark);
    background: var(--ss-bg);
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
}
.search-count strong { color: var(--ss-primary); font-weight: 800; }

/* ===== MOBILE TOOLBAR ===== */
.mobile-bar {
    display: none;
    position: sticky;
    top: 60px; /* Adjusted for standard header */
    z-index: 100;
    background: var(--ss-surface);
    padding: 12px 16px;
    gap: 12px;
    border-bottom: 1px solid var(--ss-gray-light);
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin: -20px -16px 20px -16px;
}

.mobile-bar .filter-btn {
    padding: 10px 16px;
    background: var(--ss-surface);
    border: 1px solid var(--ss-gray-light);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--ss-dark);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.mobile-bar select {
    flex: 1;
    padding: 10px 16px;
    border: 1px solid var(--ss-gray-light);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    background: var(--ss-bg);
    color: var(--ss-dark);
    outline: none;
}

/* ===== LAYOUT ===== */
.page-layout { display: flex; gap: 24px; align-items: flex-start; }

/* ===== SIDEBAR ===== */
.sidebar { width: 260px; flex-shrink: 0; position: sticky; top: 90px; }
.sidebar-box { background: var(--ss-surface); border-radius: var(--ss-radius); padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.sidebar-title { font-size: 16px; font-weight: 800; color: var(--ss-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid var(--ss-bg); display: flex; align-items: center; gap: 10px; }
.sidebar-title i { color: var(--ss-primary); }

.cat-list { list-style: none; padding: 0; margin: 0; }
.cat-list li { margin-bottom: 8px; }
.cat-list a {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 12px; background: var(--ss-surface);
    border-radius: 8px; color: var(--ss-dark); text-decoration: none;
    font-size: 14px; font-weight: 500; transition: 0.2s;
    border: 1px solid transparent;
}
.cat-list a:hover { background: #fff5f0; color: var(--ss-primary); border-color: #ffe0d1; }
.cat-list a span { color: var(--ss-gray); font-size: 12px; background: var(--ss-bg); padding: 2px 8px; border-radius: 20px; }
.cat-list a:hover span { background: var(--ss-primary); color: #fff; }

/* ===== MAIN CONTENT ===== */
.main-content { flex: 1; min-width: 0; }

/* Sort Bar (Desktop) */
.sort-bar {
    background: var(--ss-surface);
    padding: 12px 16px;
    border-radius: var(--ss-radius);
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.sort-bar .result-text { font-size: 14px; color: var(--ss-gray-dark); }
.sort-bar .result-text strong { color: var(--ss-dark); font-weight: 700; }
.sort-tabs { display: flex; gap: 8px; }
.sort-tabs button {
    padding: 8px 16px; background: var(--ss-bg); border: 1px solid transparent;
    border-radius: 8px; font-size: 13px; font-weight: 600; color: var(--ss-gray-dark);
    cursor: pointer; transition: 0.2s;
}
.sort-tabs button:hover { background: var(--ss-gray-light); color: var(--ss-dark); }
.sort-tabs button.active { background: var(--ss-primary); color: #fff; }

/* ===== PRODUCTS GRID (Matches shop.php exactly) ===== */
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }

/* Product Card */
.product-card {
    background: var(--ss-surface);
    border-radius: var(--ss-radius);
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    position: relative;
    border: 1px solid transparent;
}
.product-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: #ffe0d1; z-index: 2; }

.product-card .image-wrap { aspect-ratio: 1/1; position: relative; background: #fff; padding: 15px; display: flex; align-items: center; justify-content: center; }
.product-card .image-wrap img { max-width: 100%; max-height: 100%; object-fit: contain; transition: 0.4s; }
.product-card:hover .image-wrap img { transform: scale(1.05); }

.product-card .actions {
    position: absolute; top: 10px; right: 10px; display: flex; flex-direction: column; gap: 8px;
    opacity: 0; transform: translateX(10px); transition: 0.3s;
}
.product-card:hover .actions { opacity: 1; transform: translateX(0); }
.product-card .actions button, .product-card .actions a {
    width: 36px; height: 36px; background: #fff; border: none; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; color: var(--ss-gray-dark);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; text-decoration: none; transition: 0.2s;
}
.product-card .actions button:hover, .product-card .actions a:hover { background: var(--ss-primary); color: #fff; }

.product-card .info { padding: 15px; display: flex; flex-direction: column; flex: 1; background: #fafafa; }
.product-card .category { font-size: 11px; font-weight: 600; color: var(--ss-gray); margin-bottom: 6px; text-transform: uppercase; }
.product-card .name { font-size: 14px; font-weight: 600; color: var(--ss-dark); text-decoration: none; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 40px; margin-bottom: 8px; }
.product-card .name:hover { color: var(--ss-primary); }

.product-card .rating { display: flex; align-items: center; gap: 6px; margin-bottom: 10px; }
.product-card .rating .stars { color: #ffc107; font-size: 11px; }
.product-card .rating .sold { font-size: 12px; color: var(--ss-gray); }

.product-card .price { margin-bottom: 15px; }
.product-card .price .amount { font-size: 18px; font-weight: 800; color: var(--ss-primary); }
.product-card .price .unit { font-size: 12px; color: var(--ss-gray); }

/* Decimal Quantity Wrapper */
.product-card .qty-row { display: flex; border: 1px solid var(--ss-gray-light); border-radius: 8px; background: #fff; overflow: hidden; margin-bottom: 10px; height: 36px; }
.product-card .qty-row:focus-within { border-color: var(--ss-primary); }
.product-card .qty-row button { width: 36px; background: var(--ss-bg); border: none; color: var(--ss-dark); font-size: 14px; cursor: pointer; transition: 0.2s; }
.product-card .qty-row button:hover { background: var(--ss-gray-light); }
.product-card .qty-row input { flex: 1; width: 100%; min-width: 0; border: none; text-align: center; font-size: 14px; font-weight: 700; color: var(--ss-dark); outline: none; }

.product-card .add-btn {
    width: 100%; padding: 10px; background: var(--ss-primary); color: #fff; border: none;
    border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; margin-top: auto;
}
.product-card .add-btn:hover { background: var(--ss-secondary); transform: translateY(-2px); }

/* ===== NO RESULTS ===== */
.no-results { grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--ss-surface); border-radius: var(--ss-radius); }
.no-results i { font-size: 60px; color: var(--ss-gray-light); margin-bottom: 20px; }
.no-results h3 { font-size: 20px; font-weight: 800; color: var(--ss-dark); margin-bottom: 10px; }
.no-results p { color: var(--ss-gray); margin-bottom: 24px; }
.no-results .suggestions { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
.no-results .suggestions a { padding: 10px 16px; background: var(--ss-bg); border-radius: 8px; color: var(--ss-dark); font-weight: 600; text-decoration: none; font-size: 13px; transition: 0.2s; }
.no-results .suggestions a:hover { background: var(--ss-primary); color: white; }

/* ===== LOADING & TOAST ===== */
.loading-box, .end-box { text-align: center; padding: 30px; display: none; color: var(--ss-gray-dark); font-weight: 600; }
.loading-box.show, .end-box.show { display: block; }
.loading-box i { font-size: 24px; color: var(--ss-primary); animation: spin 1s linear infinite; margin-right: 8px; }
.end-box i { color: #10b981; margin-right: 8px; font-size: 18px; }
@keyframes spin { to { transform: rotate(360deg); } }

.toast { position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px); background: #10b981; color: white; padding: 14px 24px; border-radius: 8px; font-size: 15px; font-weight: 600; z-index: 9999; transition: transform 0.3s; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.toast.show { transform: translateX(-50%) translateY(0); }
.toast.warning { background: #f59e0b; }
.toast.error { background: #ef4444; }

/* ===== MOBILE FILTER DRAWER ====== */
.drawer-bg { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1040; opacity: 0; visibility: hidden; transition: 0.3s; }
.drawer-bg.show { opacity: 1; visibility: visible; }
.drawer { position: fixed; bottom: -100%; left: 0; width: 100%; max-height: 85vh; background: var(--ss-surface); border-radius: 20px 20px 0 0; z-index: 1050; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; }
.drawer.show { bottom: 0; }
.drawer-head { padding: 20px; border-bottom: 1px solid var(--ss-gray-light); display: flex; justify-content: space-between; align-items: center; }
.drawer-head h4 { margin: 0; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 10px; color: var(--ss-dark); }
.drawer-head h4 i { color: var(--ss-primary); }
.drawer-head button { background: none; border: none; font-size: 28px; color: var(--ss-gray); cursor: pointer; line-height: 1; }
.drawer-body { flex: 1; padding: 20px; overflow-y: auto; }

/* ===== RESPONSIVE ===== */
@media (max-width: 1200px) {
    .products-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
}
@media (max-width: 991px) {
    .sidebar { display: none; }
    .mobile-bar { display: flex; }
    .sort-bar { display: none; }
    .products-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
}
@media (max-width: 767px) {
    .products-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .search-header { padding: 16px; margin-bottom: 16px; flex-direction: column; align-items: flex-start; gap: 8px;}
    .search-title { font-size: 18px; }
    .product-card .info { padding: 12px; }
    .product-card .name { font-size: 13px; height: 38px; }
    .product-card .price .amount { font-size: 16px; }
    .product-card .actions { opacity: 1; transform: none; flex-direction: row; top: auto; bottom: 10px; right: 10px; gap: 5px;}
    .product-card .actions button, .product-card .actions a { width: 32px; height: 32px; font-size: 13px; }
}
@media (max-width: 480px) {
    .product-card .qty-row { height: 38px; }
    .product-card .qty-row button { width: 38px; font-size: 16px; }
    .product-card .add-btn { padding: 12px; font-size: 13px; }
    .product-card .rating { display: none; } /* Save space on small phones */
}
</style>

<!-- HTML CONTENT -->
<div class="search-page">

    <!-- Mobile Toolbar -->
    <div class="mobile-bar">
        <button class="filter-btn" onclick="openDrawer()">
            <i class="fas fa-filter"></i> Categories
        </button>
        <select onchange="changeSort(this.value)">
            <option value="p.register_date DESC" <?php if($sortby=='p.register_date DESC') echo 'selected'; ?>>Newest</option>
            <option value="CAST(pr.price AS SIGNED INTEGER) ASC" <?php if($sortby=='CAST(pr.price AS SIGNED INTEGER) ASC') echo 'selected'; ?>>Price: Low to High</option>
            <option value="CAST(pr.price AS SIGNED INTEGER) DESC" <?php if($sortby=='CAST(pr.price AS SIGNED INTEGER) DESC') echo 'selected'; ?>>Price: High to Low</option>
            <option value="CAST(p.product_rating AS SIGNED INTEGER) DESC" <?php if($sortby=='CAST(p.product_rating AS SIGNED INTEGER) DESC') echo 'selected'; ?>>Top Rated</option>
        </select>
    </div>

    <!-- Drawer Background & Filter Drawer -->
    <div class="drawer-bg" id="drawerBg" onclick="closeDrawer()"></div>
    <div class="drawer" id="drawer">
        <div class="drawer-head">
            <h4><i class="fas fa-list-ul"></i> Categories</h4>
            <button onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body">
            <ul class="cat-list">
                <?php foreach($categories as $cat): ?>
                <li>
                    <a href="index.php?shop-search&search=<?php echo urlencode($cat['category_name']); ?>">
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                        <span><?php echo $cat['product_count']; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="container-ss">
        
        <!-- Search Header (Redundant Search Box Removed!) -->
        <div class="search-header">
            <h1 class="search-title">Results for "<span><?php echo htmlspecialchars($search); ?></span>"</h1>
            <div class="search-count"><strong><?php echo number_format($total_products); ?></strong> products found</div>
        </div>

        <!-- Page Layout -->
        <div class="page-layout">
            
            <!-- Sidebar (Desktop) -->
            <div class="sidebar">
                <div class="sidebar-box">
                    <div class="sidebar-title"><i class="fas fa-list-ul"></i> Categories</div>
                    <ul class="cat-list">
                        <?php foreach($categories as $cat): ?>
                        <li>
                            <a href="index.php?shop-search&search=<?php echo urlencode($cat['category_name']); ?>">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                                <span><?php echo $cat['product_count']; ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                
                <!-- Sort Bar (Desktop) -->
                <div class="sort-bar">
                    <div class="result-text">Showing <strong><?php echo number_format(min($per_page, $total_products)); ?></strong> of <strong><?php echo number_format($total_products); ?></strong> items</div>
                    <div class="sort-tabs">
                        <button class="<?php if($sortby=='p.register_date DESC') echo 'active'; ?>" onclick="changeSort('p.register_date DESC')">Newest</button>
                        <button class="<?php if($sortby=='CAST(pr.price AS SIGNED INTEGER) ASC') echo 'active'; ?>" onclick="changeSort('CAST(pr.price AS SIGNED INTEGER) ASC')">Price: Low to High</button>
                        <button class="<?php if($sortby=='CAST(pr.price AS SIGNED INTEGER) DESC') echo 'active'; ?>" onclick="changeSort('CAST(pr.price AS SIGNED INTEGER) DESC')">Price: High to Low</button>
                        <button class="<?php if($sortby=='CAST(p.product_rating AS SIGNED INTEGER) DESC') echo 'active'; ?>" onclick="changeSort('CAST(p.product_rating AS SIGNED INTEGER) DESC')">Top Rated</button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
                    
                    <?php if(count($products) > 0): ?>
                        <?php 
                        $idx = 0;
                        foreach($products as $prod): 
                            $pid = $prod['product_id'];
                            $pname = htmlspecialchars($prod['product_name']);
                            $punit = htmlspecialchars($prod['product_unit'] ?: 'unit');
                            $pprice = $prod['price'] ?: 0;
                            $prating = floatval($prod['product_rating'] ?: 0);
                            $pcat = htmlspecialchars($prod['sub_category_name'] ?: $prod['category_name'] ?: '');
                            
                            // Get dynamically saved min order, fallback to 1
                            $pmin = isset($prod['minimum_order']) ? (float)$prod['minimum_order'] : 1;
                            if($pmin <= 0) $pmin = 1;

                            // Get image
                            $img_q = mysqli_query($conn, "SELECT picture FROM product_picture WHERE product_id='$pid' ORDER BY register_date DESC LIMIT 1");
                            $pimg = 'no-image.png';
                            if($img_q && $img_row = mysqli_fetch_assoc($img_q)) {
                                $pimg = $img_row['picture'];
                            }
                            
                            // Stars
                            $full = floor($prating);
                            $half = ($prating - $full) >= 0.5 ? 1 : 0;
                            $empty = 5 - $full - $half;
                            
                            $uid = 'p' . $pid . '_' . $idx;
                        ?>
                        <div class="product-card">
                            <div class="image-wrap">
                                <a href="index.php?product-detail&product=<?php echo $pid; ?>">
                                    <img src="uploads/<?php echo $pimg; ?>" alt="<?php echo $pname; ?>" loading="lazy" onerror="this.src='assets/images/no-image.png'">
                                </a>
                                <div class="actions">
                                    <a href="#quick-look" data-toggle="modal" data-product-id="<?php echo $pid; ?>" aria-label="Quick view"><i class="fas fa-eye"></i></a>
                                    <?php if($is_logged_in): ?>
                                    <button onclick="add_to_wishlist('<?php echo $pid; ?>','<?php echo $customer_id; ?>')" aria-label="Add to wishlist"><i class="far fa-heart"></i></button>
                                    <?php else: ?>
                                    <a href="index.php?sign-in" aria-label="Sign in to wishlist"><i class="far fa-heart"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info">
                                <?php if($pcat): ?><div class="category"><?php echo $pcat; ?></div><?php endif; ?>
                                <a href="index.php?product-detail&product=<?php echo $pid; ?>" class="name"><?php echo $pname; ?></a>
                                
                                <div class="rating">
                                    <span class="stars">
                                        <?php 
                                        echo str_repeat('<i class="fas fa-star"></i>', $full);
                                        if($half) echo '<i class="fas fa-star-half-alt"></i>';
                                        echo str_repeat('<i class="far fa-star"></i>', $empty);
                                        ?>
                                    </span>
                                    <span class="sold"><?php echo rand(20, 300); ?> sold</span>
                                </div>
                                
                                <div class="price">
                                    <span class="amount" data-price="<?php echo $pprice; ?>"><?php echo number_format($pprice, 0); ?> RWF</span>
                                    <span class="unit">/ <?php echo $punit; ?></span>
                                </div>
                                
                                <!-- Decimal Supported Quantity Input -->
                                <div class="qty-row">
                                    <button type="button" onclick="decreaseQty('<?php echo $uid; ?>')"><i class="fas fa-minus"></i></button>
                                    <input type="text" id="qty_<?php echo $uid; ?>" value="<?php echo $pmin; ?>" placeholder="Qty" oninput="validateQtyInput(this)" onblur="formatQtyOnBlur(this, <?php echo $pmin; ?>)">
                                    <button type="button" onclick="increaseQty('<?php echo $uid; ?>')"><i class="fas fa-plus"></i></button>
                                </div>
                                
                                <button class="add-btn" onclick="addToCartWithQty('<?php echo $pid; ?>','<?php echo $customer_id; ?>','<?php echo $pprice; ?>','<?php echo $uid; ?>')">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                        <?php 
                        $idx++;
                        endforeach; 
                        ?>
                    <?php else: ?>
                        <div class="no-results">
                            <i class="fas fa-box-open"></i>
                            <h3>No products found</h3>
                            <p>Try searching for something else or browse our categories.</p>
                            <div class="suggestions">
                                <?php 
                                $sug_q = mysqli_query($conn, "SELECT category_name FROM product_category ORDER BY RAND() LIMIT 5");
                                if($sug_q):
                                    while($sug = mysqli_fetch_assoc($sug_q)):
                                ?>
                                <a href="index.php?shop-search&search=<?php echo urlencode($sug['category_name']); ?>"><?php echo htmlspecialchars($sug['category_name']); ?></a>
                                <?php endwhile; endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>

                <!-- Loading & End States -->
                <div class="loading-box" id="loadingBox">
                    <i class="fas fa-spinner"></i> Loading more products...
                </div>
                <div class="end-box" id="endBox">
                    <i class="fas fa-check-circle"></i> You've seen all results for this search!
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<!-- Hidden Cart Response -->
<div id="result_response_cart" style="display:none;"></div>

<script>
// --- Configuration ---
var searchTerm = '<?php echo addslashes($search); ?>';
var sortBy = '<?php echo addslashes($sortby); ?>';
var perPage = <?php echo $per_page; ?>;
var totalProducts = <?php echo $total_products; ?>;
var customerId = '<?php echo $customer_id; ?>';
var currentPage = 1;
var isLoading = false;
var hasMore = totalProducts > perPage;
var productIndex = <?php echo count($products); ?>;

// Initialize Currency Formatting globally if available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof gbUpdatePrices === 'function') gbUpdatePrices();
});

// --- Drawer logic ---
function openDrawer() {
    document.getElementById('drawer').classList.add('show');
    document.getElementById('drawerBg').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    document.getElementById('drawer').classList.remove('show');
    document.getElementById('drawerBg').classList.remove('show');
    document.body.style.overflow = '';
}

// --- Sort Navigation ---
function changeSort(val) {
    window.location.href = 'index.php?shop-search&search=' + encodeURIComponent(searchTerm) + '&sortby=' + encodeURIComponent(val);
}

// --- Decimal Quantity Logic (Copied from shop.php for consistency) ---
function increaseQty(uniqueId) {
    var input = document.getElementById('qty_' + uniqueId);
    var currentVal = parseFloat(input.value) || 0;
    var increment = (currentVal % 1 !== 0 || currentVal < 1) ? 0.5 : 1;
    input.value = formatDecimal(currentVal + increment);
}

function decreaseQty(uniqueId) {
    var input = document.getElementById('qty_' + uniqueId);
    var currentVal = parseFloat(input.value) || 0;
    var decrement = (currentVal % 1 !== 0 || currentVal <= 1) ? 0.5 : 1;
    var newVal = currentVal - decrement;
    if (newVal > 0) {
        input.value = formatDecimal(newVal);
    }
}

function formatDecimal(num) {
    if (num === '' || isNaN(num)) return '';
    return parseFloat(num.toFixed(3)).toString();
}

function validateQtyInput(input) {
    var value = input.value.replace(/[^0-9.]/g, '');
    var parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }
    input.value = value;
}

function formatQtyOnBlur(input, minOrder) {
    var value = input.value.trim();
    if (value === '' || value === '.') { 
        input.value = minOrder || 1; 
        return; 
    }
    var num = parseFloat(value);
    if (isNaN(num) || num <= 0) {
        input.value = minOrder || 1;
        return;
    }
    input.value = formatDecimal(num);
}

// --- Add to Cart Logic ---
function addToCartWithQty(productId, custId, price, uniqueId) {
    var qtyInput = document.getElementById('qty_' + uniqueId);
    var quantityStr = qtyInput.value.trim();
    
    if (quantityStr === '' || quantityStr === '0') {
        showToast('Please enter a valid quantity', 'warning');
        return;
    }
    
    var quantity = parseFloat(quantityStr);
    
    // Check if customer is logged in or temp session exists
    if (!custId || custId === '') {
        var tempId = document.getElementById('customer_temp_id');
        if (tempId && tempId.value) custId = tempId.value;
    }
    
    // Provide Button Feedback
    var btn = event.currentTarget;
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    // Use global main.js function if exists, else fallback
    if (typeof add_to_cart === 'function') {
        add_to_cart(productId, custId, price, quantity);
        setTimeout(function() { 
            btn.innerHTML = originalHtml; 
            btn.disabled = false; 
            showToast('Added to cart!', 'success');
        }, 800);
    } else {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'action/insert.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            if(xhr.status === 200) {
                document.getElementById('result_response_cart').innerHTML = xhr.responseText;
                if(typeof get_cart_items === 'function') get_cart_items();
                showToast('Added to cart!', 'success');
            }
        };
        xhr.send('action=ADD_TO_CART&product_id=' + productId + '&customer_id=' + custId + '&price=' + price + '&product_quantity=' + quantity);
    }
}

// --- Toast Notifications ---
function showToast(msg, type) {
    var t = document.getElementById('toast');
    t.className = 'toast ' + (type || '');
    t.innerHTML = '<i class="fas fa-' + (type === 'warning' ? 'exclamation-circle' : type === 'error' ? 'times-circle' : 'check-circle') + '"></i> ' + msg;
    t.classList.add('show');
    setTimeout(function() { t.classList.remove('show'); }, 3000);
}

// --- Infinite Scroll ---
function loadMore() {
    if(isLoading || !hasMore) return;
    
    isLoading = true;
    currentPage++;
    document.getElementById('loadingBox').classList.add('show');
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'includes/search_load_more.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        isLoading = false;
        document.getElementById('loadingBox').classList.remove('show');
        
        if(xhr.status === 200) {
            var response = xhr.responseText.trim();
            if(response && response !== 'no_more' && response !== '') {
                document.getElementById('productsGrid').insertAdjacentHTML('beforeend', response);
                productIndex = document.querySelectorAll('.product-card').length;
                
                // Re-apply currency converter to new items
                if (typeof gbUpdatePrices === 'function') gbUpdatePrices();
                
                if(currentPage * perPage >= totalProducts) {
                    hasMore = false;
                    document.getElementById('endBox').classList.add('show');
                }
            } else {
                hasMore = false;
                document.getElementById('endBox').classList.add('show');
            }
        }
    };
    xhr.onerror = function() {
        isLoading = false;
        document.getElementById('loadingBox').classList.remove('show');
    };
    xhr.send('search=' + encodeURIComponent(searchTerm) + '&sortby=' + encodeURIComponent(sortBy) + '&page=' + currentPage + '&per_page=' + perPage + '&customer_id=' + encodeURIComponent(customerId) + '&index=' + productIndex);
}

// Scroll listener for Infinite Load
var scrollTimer;
window.addEventListener('scroll', function() {
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(function() {
        if(isLoading || !hasMore) return;
        var scrollY = window.scrollY || window.pageYOffset;
        var windowH = window.innerHeight;
        var docH = document.documentElement.scrollHeight;
        if(scrollY + windowH >= docH - 600) {
            loadMore();
        }
    }, 150);
});

// Show end message if no more products on initial load
if(totalProducts <= perPage && totalProducts > 0) {
    document.getElementById('endBox').classList.add('show');
}
</script>