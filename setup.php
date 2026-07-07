<?php
/**
 * Setup Script - Run this once to initialize the database
 * DELETE THIS FILE after setup for security
 */

require_once 'config/db_connect.php';

$message = '';
$error = '';

try {
    $db = getDB();
    
    // Check if tables exist
    $stmt = $db->query("SHOW TABLES LIKE 'admins'");
    if ($stmt->rowCount() > 0) {
        $message = 'Database is already set up. Tables exist.';
    } else {
        // Read and execute SQL file
        $sql = file_get_contents('database.sql');
        
        // Split by semicolons and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && strpos($statement, '--') !== 0) {
                // Skip comment-only lines
                $lines = explode("\n", $statement);
                $cleanStatement = '';
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if (!empty($trimmed) && strpos($trimmed, '--') !== 0) {
                        $cleanStatement .= $line . "\n";
                    }
                }
                $cleanStatement = trim($cleanStatement);
                if (!empty($cleanStatement)) {
                    $db->exec($cleanStatement);
                }
            }
        }
        
        $message = 'Database setup complete! Tables created and sample data inserted.';
    }
    
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup — Tribal Crafts</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
  font-family: 'DM Sans', sans-serif;
  background: linear-gradient(135deg, #3E2A1A 0%, #2D5016 100%);
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
}
.setup-card {
  background: white; border-radius: 20px; padding: 48px;
  width: 100%; max-width: 520px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #3E2A1A; margin-bottom: 16px; }
.success { background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 16px; border-radius: 12px; margin-bottom: 24px; }
.error-box { background: #fee; border: 1px solid #fcc; color: #c33; padding: 16px; border-radius: 12px; margin-bottom: 24px; }
.info { background: #f5f0e8; padding: 20px; border-radius: 12px; margin-bottom: 24px; }
.info h3 { font-size: 1rem; margin-bottom: 12px; color: #3E2A1A; }
.info p { font-size: 0.9rem; color: #7A6555; margin-bottom: 8px; line-height: 1.6; }
.info code { background: #3E2A1A; color: #E8D5B7; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; }
.btn {
  display: inline-block; padding: 12px 24px; border-radius: 50px;
  font-weight: 600; font-size: 0.95rem; text-decoration: none;
  transition: all 0.3s;
}
.btn-primary { background: #C4623A; color: white; }
.btn-primary:hover { background: #3E2A1A; }
.warning { background: #fff3e0; border: 1px solid #ffe0b2; color: #e65100; padding: 12px; border-radius: 8px; margin-top: 16px; font-size: 0.88rem; }
</style>
</head>
<body>
<div class="setup-card">
  <h1>Database Setup</h1>
  
  <?php if ($error): ?>
    <div class="error-box">
      <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
      <p style="margin-top: 8px; font-size: 0.88rem;">
        Make sure MySQL is running and the database credentials in <code>config/database.php</code> are correct.
      </p>
    </div>
  <?php endif; ?>
  
  <?php if ($message): ?>
    <div class="success"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  
  <div class="info">
    <h3>Setup Instructions</h3>
    <p>1. Make sure MySQL/MariaDB is running on your server</p>
    <p>2. Update database credentials in <code>config/database.php</code></p>
    <p>3. Run <code>database.sql</code> in your MySQL client, or refresh this page</p>
    <p>4. Default admin login: <code>admin</code> / <code>admin123</code></p>
  </div>
  
  <?php if ($message && !$error): ?>
    <a href="admin/login.php" class="btn btn-primary">Go to Admin Login &#8594;</a>
    <div class="warning">
      <strong>Security:</strong> Delete this <code>setup.php</code> file after setup is complete.
    </div>
  <?php endif; ?>
</div>
</body>
</html>
