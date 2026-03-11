<?php
function getSetting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key=? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) return $row['setting_value'];
    return $default;
}
function setSetting($conn, $key, $value) {
    $stmt = $conn->prepare("INSERT INTO site_settings(setting_key, setting_value) VALUES(?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
    $stmt->bind_param("ss", $key, $value);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_theme'])) {
        setSetting($conn, 'site_name', trim($_POST['site_name']));
        setSetting($conn, 'primary_color', trim($_POST['primary_color']));
        setSetting($conn, 'secondary_color', trim($_POST['secondary_color']));
        setSetting($conn, 'font_family', trim($_POST['font_family']));
        setSetting($conn, 'font_size_base', trim($_POST['font_size_base']));
        setSetting($conn, 'header_font_size', trim($_POST['header_font_size']));
        header("Location: ?page=admin_manager&manage=website&success=Theme settings updated");
        exit;
    }

    if (isset($_POST['save_page'])) {
        $page_id = (int)($_POST['page_id'] ?? 0);
        $slug = trim($_POST['slug']);
        $title = trim($_POST['title']);
        $content = $_POST['content'] ?? '';
        $seo_title = trim($_POST['seo_title'] ?? '');
        $seo_description = trim($_POST['seo_description'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($page_id > 0) {
            $stmt = $conn->prepare("UPDATE site_pages SET slug=?, title=?, content=?, seo_title=?, seo_description=?, is_active=? WHERE page_id=?");
            $stmt->bind_param("sssssii", $slug, $title, $content, $seo_title, $seo_description, $is_active, $page_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO site_pages(slug, title, content, seo_title, seo_description, is_active) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $slug, $title, $content, $seo_title, $seo_description, $is_active);
        }

        if ($stmt->execute()) {
            header("Location: ?page=admin_manager&manage=website&success=Page saved");
        } else {
            header("Location: ?page=admin_manager&manage=website&error=Could not save page (slug may already exist)");
        }
        exit;
    }
}

$siteName = getSetting($conn, 'site_name', 'GB Deliveries');
$primaryColor = getSetting($conn, 'primary_color', '#ff6a00');
$secondaryColor = getSetting($conn, 'secondary_color', '#1f2937');
$fontFamily = getSetting($conn, 'font_family', 'Arial, sans-serif');
$fontSizeBase = getSetting($conn, 'font_size_base', '16');
$headerFontSize = getSetting($conn, 'header_font_size', '32');

$pages = $conn->query("SELECT * FROM site_pages ORDER BY updated_at DESC");
?>

<div class="ali-card">
    <h2>🎨 Website Theme & Typography</h2>
    <form method="post" class="ali-form-grid">
        <input type="hidden" name="save_theme" value="1">
        <label>Site Name <input type="text" name="site_name" value="<?php echo htmlspecialchars($siteName); ?>" required></label>
        <label>Primary Color <input type="color" name="primary_color" value="<?php echo htmlspecialchars($primaryColor); ?>"></label>
        <label>Secondary Color <input type="color" name="secondary_color" value="<?php echo htmlspecialchars($secondaryColor); ?>"></label>
        <label>Font Family
            <select name="font_family">
                <?php
                $fonts = ['Arial, sans-serif','"Helvetica Neue", sans-serif','"Times New Roman", serif','Georgia, serif','Verdana, sans-serif','"Trebuchet MS", sans-serif','"Courier New", monospace'];
                foreach($fonts as $f) {
                    echo '<option value="'.htmlspecialchars($f).'" '.($fontFamily==$f?'selected':'').'>'.htmlspecialchars($f).'</option>';
                }
                ?>
            </select>
        </label>
        <label>Base Font Size (px) <input type="number" name="font_size_base" min="12" max="24" value="<?php echo (int)$fontSizeBase; ?>"></label>
        <label>Header Font Size (px) <input type="number" name="header_font_size" min="18" max="60" value="<?php echo (int)$headerFontSize; ?>"></label>
        <div><button class="ali-btn" type="submit">Save Theme</button></div>
    </form>
</div>

<div class="ali-card">
    <h2>📄 Manage Pages</h2>
    <form method="post" class="ali-form-grid">
        <input type="hidden" name="save_page" value="1">
        <input type="hidden" name="page_id" value="">
        <label>Slug <input type="text" name="slug" placeholder="about-us" required></label>
        <label>Title <input type="text" name="title" placeholder="About Us" required></label>
        <label>SEO Title <input type="text" name="seo_title"></label>
        <label>SEO Description <textarea name="seo_description" rows="2"></textarea></label>
        <label style="grid-column:1/-1;">Content <textarea name="content" rows="8"></textarea></label>
        <label><input type="checkbox" name="is_active" checked> Active</label>
        <div><button class="ali-btn" type="submit">Create Page</button></div>
    </form>

    <hr>
    <h3>Existing Pages</h3>
    <table class="ali-table">
        <thead><tr><th>ID</th><th>Slug</th><th>Title</th><th>Status</th><th>Updated</th></tr></thead>
        <tbody>
        <?php if($pages): while($p = $pages->fetch_assoc()): ?>
            <tr>
                <td><?php echo (int)$p['page_id']; ?></td>
                <td><?php echo htmlspecialchars($p['slug']); ?></td>
                <td><?php echo htmlspecialchars($p['title']); ?></td>
                <td><?php echo $p['is_active'] ? 'Active' : 'Hidden'; ?></td>
                <td><?php echo htmlspecialchars($p['updated_at']); ?></td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>