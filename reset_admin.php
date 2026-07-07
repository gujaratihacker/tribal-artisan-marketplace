<?php
/**
 * Admin Password Reset Script
 * 
 * This script resets the admin password to 'admin123'
 * 
 * USAGE:
 * 1. Visit this file in your browser: http://yourdomain.com/reset_admin.php
 * 2. The password will be reset
 * 3. DELETE THIS FILE immediately after use for security
 */

require_once 'config/db_connect.php';

$message = '';
$error = '';
$success = false;

try {
    $db = getDB();
    
    // Generate proper bcrypt hash for 'admin123'
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if admin user exists
    $stmt = $db->prepare("SELECT id FROM admins WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Update existing admin password
        $stmt = $db->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
        $stmt->execute([$hash]);
        $message = "Admin password has been reset successfully!";
    } else {
        // Create admin user if it doesn't exist
        $stmt = $db->prepare("INSERT INTO admins (username, password, email) VALUES ('admin', ?, 'admin@tribalcrafts.in')");
        $stmt->execute([$hash]);
        $message = "Admin user created successfully!";
    }
    
    $success = true;
    
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Admin Password — Tribal Crafts</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
  font-family: 'DM Sans', sans-serif;
  background: linear-gradient(135deg, #3E2A1A 0%, #2D5016 100%);
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  padding: 20px;
}
.card {
  background: white;
  border-radius: 20px;
  padding: 48px;
  width: 100%; max-width: 500px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
h1 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  color: #3E2A1A;
  margin-bottom: 16px;
}
.success {
  background: #e8f5e9;
  border: 1px solid #c8e6c9;
  color: #2e7d32;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 24px;
}
.success h2 {
  font-size: 1.2rem;
  margin-bottom: 12px;
}
.error {
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 24px;
}
.credentials {
  background: #f5f0e8;
  padding: 24px;
  border-radius: 12px;
  margin: 24px 0;
}
.credentials h3 {
  font-size: 1rem;
  color: #3E2A1A;
  margin-bottom: 16px;
}
.credentials p {
  font-size: 0.95rem;
  color: #3E2A1A;
  margin-bottom: 8px;
}
.credentials code {
  background: #3E2A1A;
  color: #E8D5B7;
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 600;
}
.warning {
  background: #fff3e0;
  border: 1px solid #ffe0b2;
  color: #e65100;
  padding: 16px;
  border-radius: 8px;
  margin-top: 24px;
  font-size: 0.9rem;
  line-height: 1.6;
}
.warning strong {
  display: block;
  margin-bottom: 8px;
}
.btn {
  display: inline-block;
  padding: 14px 28px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 1rem;
  text-decoration: none;
  transition: all 0.3s;
  margin-top: 16px;
}
.btn-primary {
  background: #C4623A;
  color: white;
}
.btn-primary:hover {
  background: #3E2A1A;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(62,42,26,0.2);
}
</style>
</head>
<body>
<div class="card">
  <h1>Admin Password Reset</h1>
  
  <?php if ($success): ?>
    <div class="success">
      <h2>&#10003; Success!</h2>
      <p><?php echo htmlspecialchars($message); ?></p>
    </div>
    
    <div class="credentials">
      <h3>Your Login Credentials:</h3>
      <p>Username: <code>admin</code></p>
      <p>Password: <code>admin123</code></p>
    </div>
    
    <a href="admin/login.php" class="btn btn-primary">Go to Login Page &#8594;</a>
    
    <div class="warning">
      <strong>&#9888; IMPORTANT SECURITY STEP:</strong>
      Delete this file (<code>reset_admin.php</code>) immediately after logging in. 
      Leaving it accessible is a security risk.
      <br><br>
      Also, consider changing the password after first login for better security.
    </div>
    
  <?php elseif ($error): ?>
    <div class="error">
      <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
      <p style="margin-top: 12px; font-size: 0.9rem;">
        Make sure your database is running and credentials in <code>config/database.php</code> are correct.
      </p>
    </div>
    
  <?php else: ?>
    <p style="color: #7A6555; margin-bottom: 24px;">
      This script will reset the admin password. Please check the database connection settings first.
    </p>
  <?php endif; ?>
</div>
</body>
</html>
