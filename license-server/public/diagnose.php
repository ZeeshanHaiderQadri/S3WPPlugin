<?php
/**
 * Diagnostic Script - Check Installation
 * Visit: https://s3cloudmedia.techknowledgecal.com/diagnose.php
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Installation Diagnostics</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #8B5CF6; }
        .check { padding: 15px; margin: 10px 0; border-radius: 6px; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
        .warning { background: #fef3c7; color: #92400e; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Installation Diagnostics</h1>
        <p>Checking your installation...</p>
";

// Check 1: PHP Version
echo "<div class='check " . (version_compare(PHP_VERSION, '8.1.0', '>=') ? 'success' : 'error') . "'>";
echo "PHP Version: " . PHP_VERSION;
echo version_compare(PHP_VERSION, '8.1.0', '>=') ? " ✅" : " ❌ (Need 8.1+)";
echo "</div>";

// Check 2: Required Extensions
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'curl', 'json'];
foreach ($extensions as $ext) {
    echo "<div class='check " . (extension_loaded($ext) ? 'success' : 'error') . "'>";
    echo "Extension: $ext " . (extension_loaded($ext) ? "✅" : "❌");
    echo "</div>";
}

// Check 3: .env file
$envPath = __DIR__ . '/../.env';
echo "<div class='check " . (file_exists($envPath) ? 'success' : 'error') . "'>";
echo ".env file: " . (file_exists($envPath) ? "✅ Found" : "❌ Missing");
echo "</div>";

// Check 4: vendor folder
$vendorPath = __DIR__ . '/../vendor';
echo "<div class='check " . (is_dir($vendorPath) ? 'success' : 'error') . "'>";
echo "Vendor folder: " . (is_dir($vendorPath) ? "✅ Found" : "❌ Missing - Run composer install");
echo "</div>";

// Check 5: storage permissions
$storagePath = __DIR__ . '/../storage';
$writable = is_writable($storagePath);
echo "<div class='check " . ($writable ? 'success' : 'error') . "'>";
echo "Storage writable: " . ($writable ? "✅" : "❌ Set permissions to 755");
echo "</div>";

// Check 6: .htaccess
$htaccessPath = __DIR__ . '/.htaccess';
echo "<div class='check " . (file_exists($htaccessPath) ? 'success' : 'error') . "'>";
echo ".htaccess file: " . (file_exists($htaccessPath) ? "✅ Found" : "❌ Missing");
echo "</div>";

// Check 7: Document Root
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$expectedPath = '/public';
$isCorrect = strpos($docRoot, $expectedPath) !== false;
echo "<div class='check " . ($isCorrect ? 'success' : 'warning') . "'>";
echo "Document Root: <code>$docRoot</code>";
if (!$isCorrect) {
    echo "<br>⚠️ Should end with <code>/public</code>";
}
echo "</div>";

// Check 8: Database Connection
if (file_exists($envPath)) {
    try {
        require __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        
        $pdo = new PDO(
            'mysql:host=' . env('DB_HOST', 'localhost') . ';dbname=' . env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
        echo "<div class='check success'>Database Connection: ✅ Connected</div>";
        
        // Check tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $requiredTables = ['users', 'plans', 'licenses', 'orders'];
        $missingTables = array_diff($requiredTables, $tables);
        
        if (empty($missingTables)) {
            echo "<div class='check success'>Database Tables: ✅ All tables exist</div>";
        } else {
            echo "<div class='check error'>Database Tables: ❌ Missing: " . implode(', ', $missingTables) . "</div>";
            echo "<div class='check warning'>Run: <code>php artisan migrate --force</code></div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='check error'>Database Connection: ❌ " . $e->getMessage() . "</div>";
    }
}

// Check 9: Routes file
$routesPath = __DIR__ . '/../routes/web.php';
echo "<div class='check " . (file_exists($routesPath) ? 'success' : 'error') . "'>";
echo "Routes file: " . (file_exists($routesPath) ? "✅ Found" : "❌ Missing");
echo "</div>";

// Check 10: Controllers
$controllerPath = __DIR__ . '/../app/Http/Controllers/Admin/DashboardController.php';
echo "<div class='check " . (file_exists($controllerPath) ? 'success' : 'error') . "'>";
echo "Admin Controllers: " . (file_exists($controllerPath) ? "✅ Found" : "❌ Missing");
echo "</div>";

echo "<hr style='margin: 30px 0;'>";
echo "<h2>📋 Next Steps</h2>";

// Provide recommendations
$hasErrors = false;
if (!file_exists($envPath)) {
    echo "<p>❌ Create .env file from .env.example</p>";
    $hasErrors = true;
}
if (!is_dir($vendorPath)) {
    echo "<p>❌ Run: <code>composer install</code></p>";
    $hasErrors = true;
}
if (!$writable) {
    echo "<p>❌ Fix permissions: <code>chmod -R 755 storage bootstrap/cache</code></p>";
    $hasErrors = true;
}
if (!$isCorrect) {
    echo "<p>⚠️ Set Document Root to: <code>/public_html/s3cloudmedia/public</code></p>";
}

if (!$hasErrors) {
    echo "<div class='check success'>";
    echo "<h3>✅ Installation looks good!</h3>";
    echo "<p>Try these URLs:</p>";
    echo "<ul>";
    echo "<li><a href='/'>Landing Page</a></li>";
    echo "<li><a href='/admin/login'>Admin Login</a></li>";
    echo "<li><a href='/api/v1/check' target='_blank'>API Test</a></li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div></body></html>";
?>
