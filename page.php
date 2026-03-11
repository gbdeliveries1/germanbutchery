<?php
// Make sure you include your database connection here
require 'db_connect.php'; // Adjust path to your DB connection file

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    die("Page not found.");
}

$stmt = $conn->prepare("SELECT title, content, status FROM site_pages WHERE slug = ? LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("HTTP/1.0 404 Not Found");
    die("<h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p>");
}

$page = $result->fetch_assoc();

if ($page['status'] !== 'published') {
    die("<h1>Access Denied</h1><p>This page is currently unpublished.</p>");
}

// Fetch site name for the browser tab
$site_name_query = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name'");
$site_name = ($site_name_query && $site_name_query->num_rows > 0) ? $site_name_query->fetch_assoc()['setting_value'] : 'Website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']) . ' - ' . htmlspecialchars($site_name); ?></title>
    
    <!-- Load custom fonts, colors, and sizes -->
    <?php include 'includes/apply_theme.php'; ?>

    <style>
        .page-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .page-header {
            border-bottom: 2px solid var(--site-primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .page-content {
            line-height: 1.6;
        }
        /* Style content injected via HTML editor */
        .page-content img { max-width: 100%; height: auto; }
        .page-content p { margin-bottom: 15px; }
    </style>
</head>
<body style="background-color: #f4f4f4; margin: 0;">

    <!-- Optional: Include your main site header here -->
    <!-- <?php include 'header.php'; ?> -->

    <div class="page-container">
        <div class="page-header">
            <h1><?php echo htmlspecialchars($page['title']); ?></h1>
        </div>
        
        <div class="page-content">
            <!-- Output raw content (since it accepts HTML from the admin) -->
            <?php echo $page['content']; ?>
        </div>
    </div>

    <!-- Optional: Include your main site footer here -->
    <!-- <?php include 'footer.php'; ?> -->

</body>
</html>