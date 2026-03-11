<?php
if (!isset($conn) || !($conn instanceof mysqli)) {
    throw new RuntimeException('Database connection ($conn) is not initialized.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch products with stock and seller in one query (no N+1)
$rows = [];
$sql = "
    SELECT
        p.product_id,
        p.product_name,
        p.product_unit,
        p.seller_id,
        COALESCE(ps.stock_quantity, 0) AS stock_quantity,
        CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS seller_name
    FROM product p
    LEFT JOIN product_stock ps ON ps.product_id = p.product_id
    LEFT JOIN user u ON u.user_id = p.seller_id
    ORDER BY p.product_name
";
if ($stmt = $conn->prepare($sql)) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    $stmt->close();
}
?>
<div class="content-wrapper">
  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="d-flex justify-content-between flex-wrap">
        <div class="d-flex align-items-end flex-wrap">
          <div class="mr-md-3 mr-xl-5">
            <h2>Products Stock</h2>
            <p class="mb-md-0">All registered.</p>
          </div>
          <div class="d-flex">
            <i class="mdi mdi-home text-muted hover-cursor"></i>
            <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Dashboard&nbsp;/&nbsp;</p>
            <p class="text-primary mb-0 hover-cursor">Products stock</p>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-end flex-wrap">
          <a href="index.php?products">
            <button class="btn btn-primary mt-2 mt-xl-0">
              <i class="mdi mdi-plus"></i> View products
            </button>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Products Stock</p>
          <div class="table-responsive">
            <table id="recent-purchases-listing" class="table table_1">
              <thead>
                <tr>
                  <th style="display:none;"></th>
                  <th>Product &amp; Seller</th>
                  <th>Stock Quantity</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                foreach ($rows as $r):
                  $product_id    = $r['product_id'];
                  $product_name  = $r['product_name'] ?? '';
                  $product_unit  = $r['product_unit'] ?? '';
                  $seller_name   = trim($r['seller_name']) ?: 'Unknown seller';
                  $stock_qty     = (int) ($r['stock_quantity'] ?? 0);

                  $id1 = "Mine{$i}";
                  $id2 = "Mine1{$i}";
                  $id3 = "Mine2{$i}";
                  $id4 = "Mine3{$i}";
                ?>
                <tr id="<?php echo htmlspecialchars($id3); ?>">
                  <td id="<?php echo htmlspecialchars($id1); ?>" style="display:none;"><?php echo htmlspecialchars($product_id); ?></td>
                  <td><?php echo htmlspecialchars($product_name); ?> [<?php echo htmlspecialchars($seller_name); ?>]</td>
                  <td><?php echo $stock_qty; ?> <?php echo htmlspecialchars($product_unit); ?></td>
                </tr>
                <?php
                  $i++;
                endforeach;
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>