<?php
// Fetch current settings
$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM site_design_settings WHERE setting_key LIKE 'pc_%'");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
$get_set = function($key, $default) use ($settings) {
    return isset($settings[$key]) ? htmlspecialchars($settings[$key]) : $default;
};
?>

<style>
    .pc-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; font-family: sans-serif; }
    .pc-card h2 { margin-top: 0; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; font-size: 18px; }
    .pc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
    .form-group label { font-weight: 600; font-size: 14px; color: #444; }
    .form-group select, .form-group input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
    .form-group input[type="color"] { width: 100%; height: 42px; border: 1px solid #ccc; border-radius: 6px; cursor: pointer; padding: 2px; }
    .btn-save { background: #ff5000; color: #fff; border: none; padding: 14px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px; width: 100%; transition: 0.3s; }
    .btn-save:hover { background: #e04400; }
    #save-status { display: none; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-align: center; background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
</style>

<div class="pc-card">
    <h2>🛍️ Product Page & Card Customizer</h2>
    <div id="save-status"></div>

    <form id="product-customizer-form">
        <div class="pc-grid">
            
            <!-- 1. Product Card Settings -->
            <div>
                <h3 style="color: #ff5000; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Product Cards (Grid View)</h3>
                
                <div class="form-group">
                    <label>Card Layout Style</label>
                    <select name="pc_card_layout">
                        <option value="elevated" <?php echo $get_set('pc_card_layout', 'elevated') == 'elevated' ? 'selected' : ''; ?>>Elevated (Shadows)</option>
                        <option value="bordered" <?php echo $get_set('pc_card_layout', 'elevated') == 'bordered' ? 'selected' : ''; ?>>Bordered (Lines)</option>
                        <option value="minimal" <?php echo $get_set('pc_card_layout', 'elevated') == 'minimal' ? 'selected' : ''; ?>>Minimal (No borders)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Card Border Radius (px)</label>
                    <input type="number" name="pc_card_radius" value="<?php echo $get_set('pc_card_radius', '8'); ?>" min="0" max="30">
                </div>

                <div class="form-group">
                    <label>Show Product Badges (Hot/Sale)?</label>
                    <select name="pc_badge_display">
                        <option value="show" <?php echo $get_set('pc_badge_display', 'show') == 'show' ? 'selected' : ''; ?>>Yes, show badges</option>
                        <option value="hide" <?php echo $get_set('pc_badge_display', 'show') == 'hide' ? 'selected' : ''; ?>>No, hide them</option>
                    </select>
                </div>
            </div>

            <!-- 2. Colors & Typography -->
            <div>
                <h3 style="color: #ff5000; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Colors & Buttons</h3>

                <div class="form-group">
                    <label>Price Text Color</label>
                    <input type="color" name="pc_price_color" value="<?php echo $get_set('pc_price_color', '#ff5000'); ?>">
                </div>

                <div class="form-group">
                    <label>Add-to-Cart Button Color</label>
                    <input type="color" name="pc_btn_color" value="<?php echo $get_set('pc_btn_color', '#ff5000'); ?>">
                </div>

                <div class="form-group">
                    <label>Add-to-Cart Button Style</label>
                    <select name="pc_btn_style">
                        <option value="solid" <?php echo $get_set('pc_btn_style', 'solid') == 'solid' ? 'selected' : ''; ?>>Solid Block</option>
                        <option value="outline" <?php echo $get_set('pc_btn_style', 'solid') == 'outline' ? 'selected' : ''; ?>>Outline (Ghost)</option>
                        <option value="pill" <?php echo $get_set('pc_btn_style', 'solid') == 'pill' ? 'selected' : ''; ?>>Rounded Pill</option>
                    </select>
                </div>
            </div>

            <!-- 3. Product Detail Page Settings -->
            <div>
                <h3 style="color: #ff5000; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Product Detail Page</h3>

                <div class="form-group">
                    <label>Gallery Thumbnail Position</label>
                    <select name="pc_gallery_layout">
                        <option value="bottom" <?php echo $get_set('pc_gallery_layout', 'bottom') == 'bottom' ? 'selected' : ''; ?>>Below Main Image</option>
                        <option value="left" <?php echo $get_set('pc_gallery_layout', 'bottom') == 'left' ? 'selected' : ''; ?>>Left of Main Image</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Information Tabs Layout</label>
                    <select name="pc_tab_style">
                        <option value="horizontal" <?php echo $get_set('pc_tab_style', 'horizontal') == 'horizontal' ? 'selected' : ''; ?>>Horizontal Tabs</option>
                        <option value="accordion" <?php echo $get_set('pc_tab_style', 'horizontal') == 'accordion' ? 'selected' : ''; ?>>Vertical Accordion</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Related Products Count</label>
                    <input type="number" name="pc_related_count" value="<?php echo $get_set('pc_related_count', '4'); ?>" min="0" max="12">
                </div>
            </div>

        </div>

        <button type="submit" class="btn-save" id="btn-save-pc">💾 Save Product Settings</button>
    </form>
</div>

<script>
document.getElementById('product-customizer-form').addEventListener('submit', function(e) {
    e.preventDefault();
    let btn = document.getElementById('btn-save-pc');
    let status = document.getElementById('save-status');
    btn.innerText = 'Saving...'; btn.disabled = true;

    fetch('ajax_product_customizer.php', {
        method: 'POST', body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        btn.innerText = '💾 Save Product Settings'; btn.disabled = false;
        if(data.status === 'success') {
            status.innerText = '✅ Layout Settings Live!';
            status.style.display = 'block';
            setTimeout(() => status.style.display = 'none', 3000);
        }
    });
});
</script>