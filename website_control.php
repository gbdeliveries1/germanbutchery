<?php
// Ensure required tables exist automatically
$conn->query("CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS site_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Helper functions for settings
function getSetting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return $row['setting_value'];
    }
    return $default;
}

function setSetting($conn, $key, $value) {
    $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param("sss", $key, $value, $value);
    $stmt->execute();
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_site_settings'])) {
        setSetting($conn, 'site_name', $_POST['site_name']);
        setSetting($conn, 'site_email', $_POST['site_email']);
        setSetting($conn, 'site_phone', $_POST['site_phone']);
        
        // Theme settings
        setSetting($conn, 'primary_color', $_POST['primary_color']);
        setSetting($conn, 'secondary_color', $_POST['secondary_color']);
        setSetting($conn, 'font_family', $_POST['font_family']);
        setSetting($conn, 'font_size_base', $_POST['font_size_base']);
        setSetting($conn, 'font_size_headings', $_POST['font_size_headings']);

        echo "<script>window.location.href='?page=admin_manager&manage=website_control&success=Settings+Updated';</script>";
        exit;
    }

    if (isset($_POST['save_page'])) {
        $id = isset($_POST['page_id']) ? (int)$_POST['page_id'] : 0;
        $title = $_POST['title'];
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));
        $content = $_POST['content'];
        $status = $_POST['status'];

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE site_pages SET title=?, slug=?, content=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $title, $slug, $content, $status, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO site_pages (title, slug, content, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $slug, $content, $status);
        }
        
        if($stmt->execute()) {
            echo "<script>window.location.href='?page=admin_manager&manage=website_control&success=Page+Saved';</script>";
        } else {
            echo "<script>window.location.href='?page=admin_manager&manage=website_control&error=Error+Saving+Page+(Slug+must+be+unique)';</script>";
        }
        exit;
    }
    
    if (isset($_POST['delete_page'])) {
        $id = (int)$_POST['page_id'];
        $conn->query("DELETE FROM site_pages WHERE id = $id");
        echo "<script>window.location.href='?page=admin_manager&manage=website_control&success=Page+Deleted';</script>";
        exit;
    }
}

// Fetch variables for form
$fonts = [
    'Arial, sans-serif' => 'Arial',
    '"Helvetica Neue", Helvetica, Arial, sans-serif' => 'Helvetica',
    'Georgia, serif' => 'Georgia',
    '"Times New Roman", Times, serif' => 'Times New Roman',
    'Verdana, Geneva, sans-serif' => 'Verdana',
    '"Courier New", Courier, monospace' => 'Courier New',
    'Roboto, sans-serif' => 'Roboto (Web Safe)',
    'Montserrat, sans-serif' => 'Montserrat (Web Safe)'
];
?>

<style>
    .control-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .control-card h2 { margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; font-size: 18px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 14px; }
    .form-group input[type="text"], .form-group input[type="number"], .form-group select, .form-group textarea {
        width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: inherit;
    }
    .form-group input[type="color"] { border: none; width: 100%; height: 38px; cursor: pointer; border-radius: 4px; }
    .btn-primary { background: #ff4747; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .btn-primary:hover { background: #e03e3e; }
    .btn-danger { background: #dc3545; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .data-table th, .data-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
    .data-table th { background: #f9f9f9; }
</style>

<div class="control-card">
    <h2>⚙️ Global Site & Theme Settings</h2>
    <form method="POST">
        <input type="hidden" name="update_site_settings" value="1">
        
        <div class="form-grid">
            <!-- Site Info -->
            <div class="form-group">
                <label>Site Name</label>
                <input type="text" name="site_name" value="<?php echo htmlspecialchars(getSetting($conn, 'site_name', 'GB Deliveries')); ?>" required>
            </div>
            <div class="form-group">
                <label>Support Email</label>
                <input type="text" name="site_email" value="<?php echo htmlspecialchars(getSetting($conn, 'site_email')); ?>">
            </div>
            <div class="form-group">
                <label>Support Phone</label>
                <input type="text" name="site_phone" value="<?php echo htmlspecialchars(getSetting($conn, 'site_phone')); ?>">
            </div>
            
            <!-- Colors -->
            <div class="form-group">
                <label>Primary Theme Color</label>
                <input type="color" name="primary_color" value="<?php echo htmlspecialchars(getSetting($conn, 'primary_color', '#ff4747')); ?>">
            </div>
            <div class="form-group">
                <label>Secondary Theme Color</label>
                <input type="color" name="secondary_color" value="<?php echo htmlspecialchars(getSetting($conn, 'secondary_color', '#333333')); ?>">
            </div>

            <!-- Typography -->
            <div class="form-group">
                <label>Global Font Family</label>
                <select name="font_family">
                    <?php 
                    $current_font = getSetting($conn, 'font_family', 'Arial, sans-serif');
                    foreach($fonts as $val => $name) {
                        $sel = ($val == $current_font) ? 'selected' : '';
                        echo "<option value='".htmlspecialchars($val)."' $sel>$name</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Base Font Size (px)</label>
                <input type="number" name="font_size_base" value="<?php echo htmlspecialchars(getSetting($conn, 'font_size_base', '14')); ?>" min="10" max="24">
            </div>
            <div class="form-group">
                <label>Headings Font Size (px)</label>
                <input type="number" name="font_size_headings" value="<?php echo htmlspecialchars(getSetting($conn, 'font_size_headings', '24')); ?>" min="16" max="48">
            </div>
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 15px;">💾 Save All Settings</button>
    </form>
</div>

<div class="control-card">
    <h2>📄 Content Management System (Pages)</h2>
    <button type="button" class="btn-primary" onclick="document.getElementById('page-form').style.display='block'; document.getElementById('form-mode').innerText='Create New Page'; document.getElementById('page_id').value=''; document.getElementById('page-form-element').reset();">➕ Create New Page</button>

    <div id="page-form" style="display:none; margin-top: 20px; background: #f9f9f9; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
        <h3 id="form-mode" style="margin-top:0;">Create New Page</h3>
        <form method="POST" id="page-form-element">
            <input type="hidden" name="save_page" value="1">
            <input type="hidden" name="page_id" id="page_id" value="">
            
            <div class="form-group">
                <label>Page Title</label>
                <input type="text" name="title" id="page_title" required onkeyup="document.getElementById('page_slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');">
            </div>
            <div class="form-group">
                <label>URL Slug (e.g., about-us)</label>
                <input type="text" name="slug" id="page_slug" required>
            </div>
            <div class="form-group">
                <label>Visibility Status</label>
                <select name="status" id="page_status">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="form-group">
                <label>Page Content (HTML allowed)</label>
                <textarea name="content" id="page_content" rows="10" style="resize:vertical;" required></textarea>
            </div>
            <button type="submit" class="btn-primary">Save Page</button>
            <button type="button" class="btn-primary" style="background:#888;" onclick="document.getElementById('page-form').style.display='none';">Cancel</button>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>URL Slug</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pages = $conn->query("SELECT * FROM site_pages ORDER BY id DESC");
            if($pages && $pages->num_rows > 0) {
                while($p = $pages->fetch_assoc()) {
                    $json = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                    echo "<tr>
                        <td>{$p['id']}</td>
                        <td><strong>".htmlspecialchars($p['title'])."</strong></td>
                        <td><a href='page.php?slug=".htmlspecialchars($p['slug'])."' target='_blank'>/{$p['slug']}</a></td>
                        <td><span style='padding:3px 8px; border-radius:12px; font-size:12px; background: ".($p['status']=='published'?'#d4edda; color:#155724':'#fff3cd; color:#856404')."'>".ucfirst($p['status'])."</span></td>
                        <td>
                            <button type='button' class='btn-primary' style='padding:5px 10px; font-size:12px;' onclick='editPage($json)'>Edit</button>
                            <form method='POST' style='display:inline;' onsubmit='return confirm(\"Delete this page permanently?\");'>
                                <input type='hidden' name='delete_page' value='1'>
                                <input type='hidden' name='page_id' value='{$p['id']}'>
                                <button type='submit' class='btn-danger'>Delete</button>
                            </form>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No pages created yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
function editPage(data) {
    document.getElementById('page-form').style.display = 'block';
    document.getElementById('form-mode').innerText = 'Edit Page: ' + data.title;
    document.getElementById('page_id').value = data.id;
    document.getElementById('page_title').value = data.title;
    document.getElementById('page_slug').value = data.slug;
    document.getElementById('page_status').value = data.status;
    document.getElementById('page_content').value = data.content;
    window.scrollTo(0, document.getElementById('page-form').offsetTop - 50);
}
</script>