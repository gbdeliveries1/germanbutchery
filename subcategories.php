<?php
// Sub Categories Section

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['add_subcategory'])) {
        $name = $conn->real_escape_string(trim($_POST['sub_category_name']));
        $catId = $conn->real_escape_string($_POST['category_id']);
        if($name && $catId) {
            $id = md5(time().rand(1000,9999));
            $now = date('Y-m-d H:i:s');
            $conn->query("INSERT INTO product_sub_category (sub_category_id, category_id, sub_category_name, register_date) VALUES ('$id', '$catId', '$name', '$now')");
            header("Location: ?page=admin_manager&manage=subcategories&success=Sub category added!");
            exit;
        }
    }
    if(isset($_POST['edit_subcategory'])) {
        $id = $conn->real_escape_string($_POST['sub_category_id']);
        $name = $conn->real_escape_string(trim($_POST['sub_category_name']));
        $catId = $conn->real_escape_string($_POST['category_id']);
        if($id && $name) {
            $conn->query("UPDATE product_sub_category SET sub_category_name='$name', category_id='$catId' WHERE sub_category_id='$id'");
            header("Location: ?page=admin_manager&manage=subcategories&success=Sub category updated!");
            exit;
        }
    }
    if(isset($_POST['delete_subcategory'])) {
        $id = $conn->real_escape_string($_POST['sub_category_id']);
        $conn->query("DELETE FROM product_sub_category WHERE sub_category_id='$id'");
        header("Location: ?page=admin_manager&manage=subcategories&success=Sub category deleted!");
        exit;
    }
}

// Get categories for dropdown
$categories = [];
$r = $conn->query("SELECT category_id, category_name FROM product_category ORDER BY category_name");
if($r) while($row = $r->fetch_assoc()) $categories[] = $row;

// Get sub categories
$subcategories = [];
$r = $conn->query("SELECT sc.*, c.category_name,
                   (SELECT COUNT(*) FROM product WHERE sub_category_id=sc.sub_category_id) as product_count
                   FROM product_sub_category sc 
                   LEFT JOIN product_category c ON sc.category_id = c.category_id
                   ORDER BY c.category_name, sc.sub_category_name");
if($r) while($row = $r->fetch_assoc()) $subcategories[] = $row;
?>

<style>
.sec-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:15px}
.sec-title{margin:0;font-size:20px;font-weight:600;color:#1a1a2e;display:flex;align-items:center;gap:10px}
.sec-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.sec-search{padding:10px 15px;border:1px solid #ddd;border-radius:8px;font-size:13px;width:220px}
.sec-search:focus{outline:none;border-color:#ff6000}
.sec-select{padding:10px 15px;border:1px solid #ddd;border-radius:8px;font-size:13px;background:#fff}
.sec-btn{display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 0.2s}
.sec-btn-primary{background:#ff6000;color:#fff}
.sec-btn-primary:hover{background:#e55500}
.sec-btn-secondary{background:#f0f0f0;color:#333}
.sec-btn-success{background:#27ae60;color:#fff}
.sec-btn-danger{background:#e74c3c;color:#fff}
.sec-btn-sm{padding:6px 12px;font-size:12px}
.sec-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:20px}
.sec-card-header{padding:18px 20px;border-bottom:1px solid #f0f0f0}
.sec-card-title{margin:0;font-size:15px;font-weight:600;color:#333}
.sec-table{width:100%;border-collapse:collapse}
.sec-table th{background:#fafafa;padding:12px 15px;text-align:left;font-size:11px;font-weight:600;color:#666;text-transform:uppercase;border-bottom:2px solid #f0f0f0}
.sec-table td{padding:12px 15px;border-bottom:1px solid #f5f5f5;font-size:13px;color:#333}
.sec-table tr:hover{background:#fafafa}
.sec-table-empty{text-align:center;padding:40px;color:#999}
.sec-badge{display:inline-block;padding:4px 10px;border-radius:15px;font-size:11px;font-weight:600}
.sec-badge-blue{background:#e3f2fd;color:#1976d2}
.sec-badge-purple{background:#f3e5f5;color:#7b1fa2}
.sec-form{background:#f8f9fa;border:2px dashed #ddd;border-radius:12px;padding:20px;margin-bottom:20px;display:none}
.sec-form.show{display:block}
.sec-form h3{margin:0 0 20px;font-size:16px;color:#333}
.sec-form-row{margin-bottom:15px}
.sec-form-row label{display:block;margin-bottom:6px;font-size:13px;font-weight:600;color:#333}
.sec-form-row input,.sec-form-row select{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-size:14px}
.sec-form-row input:focus,.sec-form-row select:focus{outline:none;border-color:#ff6000}
.sec-form-inline{display:flex;gap:15px;flex-wrap:wrap}
.sec-form-inline .sec-form-row{flex:1;min-width:200px;margin-bottom:0}
.sec-form-actions{margin-top:20px;display:flex;gap:10px}
.sec-modal{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center}
.sec-modal.show{display:flex}
.sec-modal-content{background:#fff;padding:25px;border-radius:12px;width:100%;max-width:450px;margin:20px}
.sec-modal-title{margin:0 0 20px;font-size:18px;font-weight:600}
</style>

<div class="sec-header">
    <h1 class="sec-title">📂 Sub Categories</h1>
    <div class="sec-actions">
        <input type="text" class="sec-search" placeholder="🔍 Search..." oninput="filterTable(this.value)">
        <select class="sec-select" onchange="filterByCategory(this.value)">
            <option value="">All Categories</option>
            <?php foreach($categories as $c): ?>
            <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="sec-btn sec-btn-primary" onclick="toggleForm('addForm')">+ Add Sub Category</button>
    </div>
</div>

<div class="sec-form" id="addForm">
    <h3>➕ Add New Sub Category</h3>
    <form method="POST">
        <div class="sec-form-inline">
            <div class="sec-form-row">
                <label>Parent Category *</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach($categories as $c): ?>
                    <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sec-form-row">
                <label>Sub Category Name *</label>
                <input type="text" name="sub_category_name" required placeholder="Enter sub category name">
            </div>
        </div>
        <div class="sec-form-actions">
            <button type="submit" name="add_subcategory" class="sec-btn sec-btn-success">✓ Save</button>
            <button type="button" class="sec-btn sec-btn-secondary" onclick="toggleForm('addForm')">Cancel</button>
        </div>
    </form>
</div>

<div class="sec-card">
    <div class="sec-card-header">
        <h2 class="sec-card-title">All Sub Categories (<?php echo count($subcategories); ?>)</h2>
    </div>
    <table class="sec-table" id="dataTable">
        <thead>
            <tr>
                <th>Sub Category</th>
                <th>Parent Category</th>
                <th>Products</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($subcategories)): ?>
            <tr><td colspan="4" class="sec-table-empty">📂 No sub categories yet</td></tr>
            <?php else: foreach($subcategories as $sc): ?>
            <tr data-cat="<?php echo $sc['category_id']; ?>">
                <td><strong><?php echo htmlspecialchars($sc['sub_category_name']); ?></strong></td>
                <td><span class="sec-badge sec-badge-purple"><?php echo htmlspecialchars($sc['category_name']); ?></span></td>
                <td><span class="sec-badge sec-badge-blue"><?php echo $sc['product_count']; ?></span></td>
                <td>
                    <button class="sec-btn sec-btn-sm sec-btn-secondary" onclick="editItem('<?php echo $sc['sub_category_id']; ?>', '<?php echo htmlspecialchars($sc['sub_category_name'], ENT_QUOTES); ?>', '<?php echo $sc['category_id']; ?>')">✏️</button>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this sub category?')">
                        <input type="hidden" name="sub_category_id" value="<?php echo $sc['sub_category_id']; ?>">
                        <button type="submit" name="delete_subcategory" class="sec-btn sec-btn-sm sec-btn-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="sec-modal" id="editModal">
    <div class="sec-modal-content">
        <h3 class="sec-modal-title">✏️ Edit Sub Category</h3>
        <form method="POST">
            <input type="hidden" name="sub_category_id" id="editId">
            <div class="sec-form-row">
                <label>Parent Category</label>
                <select name="category_id" id="editCat" required>
                    <?php foreach($categories as $c): ?>
                    <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sec-form-row">
                <label>Sub Category Name</label>
                <input type="text" name="sub_category_name" id="editName" required>
            </div>
            <div class="sec-form-actions">
                <button type="submit" name="edit_subcategory" class="sec-btn sec-btn-primary">Save</button>
                <button type="button" class="sec-btn sec-btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleForm(id){document.getElementById(id).classList.toggle('show')}
function filterTable(val){var rows=document.querySelectorAll('#dataTable tbody tr');val=val.toLowerCase();rows.forEach(function(r){r.style.display=r.textContent.toLowerCase().includes(val)?'':'none'})}
function filterByCategory(cat){var rows=document.querySelectorAll('#dataTable tbody tr');rows.forEach(function(r){r.style.display=(!cat||r.dataset.cat===cat)?'':'none'})}
function editItem(id,name,cat){document.getElementById('editId').value=id;document.getElementById('editName').value=name;document.getElementById('editCat').value=cat;document.getElementById('editModal').classList.add('show')}
function closeModal(){document.getElementById('editModal').classList.remove('show')}
</script>