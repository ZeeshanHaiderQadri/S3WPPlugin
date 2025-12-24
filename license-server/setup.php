<?php
/**
 * Simple Database Setup Script - ROOT VERSION
 * Run this ONCE to create tables and seed data
 */

// Configuration - EDIT THESE
$db_host = 'localhost';
$db_port = '3306';
$db_name = 'your_database_name';  // CHANGE THIS
$db_user = 'your_database_user';  // CHANGE THIS
$db_pass = 'your_database_password';  // CHANGE THIS

$admin_name = 'Admin User';
$admin_email = 'admin@yourcompany.com';
$admin_password = 'admin123';  // CHANGE THIS AFTER FIRST LOGIN

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body { font-family: Arial; padding: 40px; background: linear-gradient(135deg, #8B5CF6, #F97316); }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; }
        h1 { color: #8B5CF6; }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .step { padding: 10px; margin: 10px 0; border-left: 4px solid #8B5CF6; padding-left: 15px; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; }
        .btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #8B5CF6, #F97316); color: white; text-decoration: none; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Database Setup</h1>
        
        <?php
        try {
            // Connect to database
            echo "<div class='step'>Connecting to database...</div>";
            $pdo = new PDO(
                "mysql:host=$db_host;port=$db_port;dbname=$db_name",
                $db_user,
                $db_pass
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<div class='success'>✅ Connected to database</div>";
            
            // Disable foreign key checks
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // Create users table
            echo "<div class='step'>Creating users table...</div>";
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                company VARCHAR(255) NULL,
                phone VARCHAR(255) NULL,
                role ENUM('admin', 'customer') DEFAULT 'customer',
                is_active BOOLEAN DEFAULT TRUE,
                email_verified_at TIMESTAMP NULL,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<div class='success'>✅ Users table created</div>";
            
            // Create plans table
            echo "<div class='step'>Creating plans table...</div>";
            $pdo->exec("DROP TABLE IF EXISTS plans");
            $pdo->exec("CREATE TABLE plans (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                description TEXT NULL,
                price DECIMAL(10, 2) NOT NULL DEFAULT 0,
                billing_cycle ENUM('monthly', 'yearly', 'lifetime') DEFAULT 'yearly',
                media_limit INT UNSIGNED NULL,
                max_sites INT UNSIGNED DEFAULT 1,
                features JSON NULL,
                is_active BOOLEAN DEFAULT TRUE,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<div class='success'>✅ Plans table created</div>";
            
            // Create licenses table
            echo "<div class='step'>Creating licenses table...</div>";
            $pdo->exec("CREATE TABLE IF NOT EXISTS licenses (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                plan_id BIGINT UNSIGNED NOT NULL,
                license_key VARCHAR(255) NOT NULL UNIQUE,
                status ENUM('active', 'inactive', 'expired', 'suspended') DEFAULT 'active',
                max_activations INT UNSIGNED DEFAULT 1,
                activated_at TIMESTAMP NULL,
                expires_at TIMESTAMP NULL,
                last_checked_at TIMESTAMP NULL,
                metadata JSON NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<div class='success'>✅ Licenses table created</div>";
            
            // Create license_activations table
            echo "<div class='step'>Creating license_activations table...</div>";
            $pdo->exec("CREATE TABLE IF NOT EXISTS license_activations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                license_id BIGINT UNSIGNED NOT NULL,
                domain VARCHAR(255) NOT NULL,
                site_url VARCHAR(255) NULL,
                ip_address VARCHAR(45) NULL,
                wordpress_version VARCHAR(50) NULL,
                plugin_version VARCHAR(50) NULL,
                activated_at TIMESTAMP NULL,
                last_checked_at TIMESTAMP NULL,
                deactivated_at TIMESTAMP NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (license_id) REFERENCES licenses(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<div class='success'>✅ License activations table created</div>";
            
            // Create media_uploads table
            echo "<div class='step'>Creating media_uploads table...</div>";
            $pdo->exec("CREATE TABLE IF NOT EXISTS media_uploads (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                license_id BIGINT UNSIGNED NOT NULL,
                license_activation_id BIGINT UNSIGNED NULL,
                domain VARCHAR(255) NOT NULL,
                attachment_id BIGINT UNSIGNED NULL,
                file_name VARCHAR(255) NOT NULL,
                file_type VARCHAR(100) NULL,
                file_size BIGINT UNSIGNED NULL,
                s3_key TEXT NULL,
                s3_url TEXT NULL,
                uploaded_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (license_id) REFERENCES licenses(id) ON DELETE CASCADE,
                FOREIGN KEY (license_activation_id) REFERENCES license_activations(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<div class='success'>✅ Media uploads table created</div>";
            
            // Create usage_stats table
            echo "<div class='step'>Creating usage_stats table...</div>";
            $pdo->exec("CREATE TABLE IF NOT EXISTS usage_stats (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                license_id BIGINT UNSIGNED NOT NULL,
                date DATE NOT NULL,
                uploads_count INT UNSIGNED DEFAULT 0,
                total_size BIGINT UNSIGNED DEFAULT 0,
                api_calls INT UNSIGNED DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                UNIQUE KEY unique_license_date (license_id, date),
                FOREIGN KEY (license_id) REFERENCES licenses(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<div class='success'>✅ Usage stats table created</div>";
            
            // Create orders table
            echo "<div class='step'>Creating orders table...</div>";
            $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                plan_id BIGINT UNSIGNED NOT NULL,
                order_number VARCHAR(255) NOT NULL UNIQUE,
                status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
                amount DECIMAL(10, 2) NOT NULL,
                currency VARCHAR(3) DEFAULT 'USD',
                payment_method VARCHAR(50) NULL,
                payment_id VARCHAR(255) NULL,
                customer_email VARCHAR(255) NULL,
                billing_details JSON NULL,
                paid_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<div class='success'>✅ Orders table created</div>";
            
            // Re-enable foreign key checks
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            
            // Insert plans
            echo "<div class='step'>Inserting 8 pricing plans...</div>";
            $plans = [
                ['Free', 'free', 'Perfect for testing and small sites', 0, 2500, 1, 0],
                ['Bronze', 'bronze', 'Great for small businesses', 39, 2000, 1, 1],
                ['Silver', 'silver', 'Ideal for growing sites', 59, 6000, 1, 2],
                ['Gold', 'gold', 'Perfect for medium businesses', 149, 20000, 3, 3],
                ['Platinum', 'platinum', 'For large sites with high traffic', 199, 40000, 5, 4],
                ['Gem', 'gem', 'Perfect for Amazon affiliates with 100K products', 349, 100000, 5, 5],
                ['500K', '500k', 'For massive product catalogs', 799, 500000, 10, 6],
                ['Unlimited', 'unlimited', 'Enterprise solution with unlimited media', 1199, null, 20, 7],
            ];
            
            $stmt = $pdo->prepare("INSERT INTO plans (name, slug, description, price, media_limit, max_sites, sort_order, is_active, created_at, updated_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
            
            foreach ($plans as $plan) {
                $stmt->execute($plan);
            }
            echo "<div class='success'>✅ 8 pricing plans inserted successfully!</div>";
            
            // Create admin user
            echo "<div class='step'>Creating admin user...</div>";
            $hashedPassword = password_hash($admin_password, PASSWORD_BCRYPT);
            
            // Check if admin already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$admin_email]);
            
            if ($stmt->fetch()) {
                echo "<div class='success'>✅ Admin user already exists</div>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at)
                                      VALUES (?, ?, ?, 'admin', 1, NOW(), NOW(), NOW())");
                $stmt->execute([$admin_name, $admin_email, $hashedPassword]);
                echo "<div class='success'>✅ Admin user created</div>";
            }
            
            echo "<hr style='margin: 30px 0;'>";
            echo "<div class='success'>";
            echo "<h2>🎉 Setup Complete!</h2>";
            echo "<p><strong>Everything is ready!</strong></p>";
            echo "<ul style='text-align: left;'>";
            echo "<li>✅ All 7 database tables created</li>";
            echo "<li>✅ 8 pricing plans configured</li>";
            echo "<li>✅ Admin account ready</li>";
            echo "</ul>";
            echo "<p><strong>Admin Login Credentials:</strong></p>";
            echo "<p>Email: <code>$admin_email</code></p>";
            echo "<p>Password: <code>$admin_password</code></p>";
            echo "<p><strong>⚠️ IMPORTANT:</strong> Change your password after first login!</p>";
            echo "<a href='/admin/login' class='btn'>Go to Admin Login</a>";
            echo "</div>";
            
            echo "<div class='error' style='margin-top: 20px;'>";
            echo "<strong>🔒 Security:</strong> Delete this setup.php file after login!";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div class='error'>";
            echo "<strong>❌ Database Error:</strong><br>";
            echo htmlspecialchars($e->getMessage());
            echo "</div>";
            echo "<p>Please check your database credentials at the top of this file.</p>";
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<strong>❌ Error:</strong><br>";
            echo htmlspecialchars($e->getMessage());
            echo "</div>";
        }
        ?>
        
    </div>
</body>
</html>
