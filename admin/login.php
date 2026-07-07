<?php
/**
 * Admin Login Page
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Tribal Crafts</title>
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
.login-card {
  background: white;
  border-radius: 20px;
  padding: 48px;
  width: 100%; max-width: 420px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.login-card h1 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  color: #3E2A1A;
  margin-bottom: 8px;
}
.login-card p {
  color: #7A6555;
  margin-bottom: 32px;
  font-size: 0.95rem;
}
.form-group { margin-bottom: 20px; }
.form-group label {
  display: block;
  font-size: 0.88rem;
  font-weight: 500;
  color: #3E2A1A;
  margin-bottom: 8px;
}
.form-group input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #E8D5B7;
  border-radius: 12px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  transition: all 0.3s;
}
.form-group input:focus {
  outline: none;
  border-color: #C4623A;
  box-shadow: 0 0 0 3px rgba(196,98,58,0.1);
}
.login-btn {
  width: 100%;
  background: #C4623A;
  color: white;
  padding: 14px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 1rem;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
}
.login-btn:hover { background: #3E2A1A; }
.error {
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 0.9rem;
}
.logo {
  font-family: 'Playfair Display', serif;
  font-size: 1.4rem;
  font-weight: 900;
  color: #3E2A1A;
  margin-bottom: 24px;
  display: flex; align-items: center; gap: 10px;
}
.logo-icon {
  width: 38px; height: 38px;
  background: #C4623A;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; color: white;
}
</style>
</head>
<body>
<div class="login-card">
  <div class="logo">
    <span class="logo-icon">&#9752;</span>
    Tribal Crafts
  </div>
  <h1>Admin Login</h1>
  <p>Sign in to manage your artisan marketplace</p>
  
  <?php if ($error): ?>
    <div class="error"><?php echo e($error); ?></div>
  <?php endif; ?>
  
  <form method="POST" action="">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Enter username" required autofocus>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter password" required>
    </div>
    <button type="submit" class="login-btn">Sign In</button>
  </form>
  <p style="text-align: center; margin-top: 24px; font-size: 0.82rem; color: #999;">
    Default: admin / admin123
  </p>
</div>
</body>
</html>
