<?php
echo "<h2>Searching for Database Connection</h2>";

// Check index.php for database connection
$indexFile = file_get_contents('index.php');
echo "<h3>index.php contents (first 3000 chars):</h3>";
echo "<pre>" . htmlspecialchars(substr($indexFile, 0, 3000)) . "</pre>";

echo "<hr>";

// Check for includes folder in various locations
$searchPaths = array(
    '/home/u496441540/domains/gbdeliveries.com/public_html',
    '/home/u496441540/domains/gbdeliveries.com/public_html/includes',
    '/home/u496441540/domains/gbdeliveries.com/public_html/inc',
    '/home/u496441540/domains/gbdeliveries.com/public_html/config',
    '/home/u496441540/domains/gbdeliveries.com/public_html/user',
);

foreach ($searchPaths as $path) {
    if (is_dir($path)) {
        echo "<h3>Files in: $path</h3>";
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $fullPath = $path . '/' . $file;
                $type = is_dir($fullPath) ? '[DIR]' : '[FILE]';
                echo "$type $file<br>";
            }
        }
        echo "<br>";
    }
}

// Search for files containing mysqli or mysql connection
echo "<h3>Searching for PHP files with database connections...</h3>";

function searchInFile($file, $searchTerms) {
    if (!is_readable($file)) return false;
    $content = file_get_contents($file);
    foreach ($searchTerms as $term) {
        if (stripos($content, $term) !== false) {
            return true;
        }
    }
    return false;
}

$searchTerms = array('mysqli_connect', 'new mysqli', 'mysql_connect', 'PDO(', '$conn');

$rootPath = '/home/u496441540/domains/gbdeliveries.com/public_html';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath));

$found = array();
$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $count++;
        if ($count > 500) break; // Limit search
        
        if (searchInFile($file->getPathname(), $searchTerms)) {
            $found[] = $file->getPathname();
        }
    }
}

echo "<p>Found " . count($found) . " files with potential database connections:</p>";
foreach ($found as $f) {
    echo "📄 " . str_replace($rootPath, '', $f) . "<br>";
}