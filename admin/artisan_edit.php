<?php
/**
 * Admin - Edit Artisan
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$artisan = getArtisanById($id);
if (!$artisan) redirect('artisans.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $specialty = sanitize($_POST['specialty'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name) || empty($location) || empty($specialty) || empty($bio) || empty($phone) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("UPDATE artisans SET name=?, location=?, specialty=?, bio=?, phone=?, email=?, is_featured=? WHERE id=?");
            $stmt->execute([$name, $location, $specialty, $bio, $phone, $email, $is_featured, $id]);
            redirect('artisans.php');
        } catch (PDOException $e) {
            $error = 'Failed to update: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Artisan — Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DM Sans', sans-serif; background: #F5F0E8; color: #3E2A1A; }
.admin-layout { display: flex; min-height: 100vh; }
.sidebar { width: 260px; background: #3E2A1A; color: #E8D5B7; padding: 24px 0; position: fixed; top: 0; bottom: 0; display: flex; flex-direction: column; }
.sidebar-logo { font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 900; color: #FDF6EC; padding: 0 24px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; gap: 10px; }
.sidebar-logo .logo-icon { width: 34px; height: 34px; background: #C4623A; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: white; }
.sidebar-nav { flex: 1; padding: 16px 0; }
.sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #E8D5B7; font-size: 0.92rem; text-decoration: none; transition: all 0.3s; }
.sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(196,98,58,0.15); color: #FDF6EC; border-right: 3px solid #C4623A; }
.sidebar-nav a .icon { font-size: 1.1rem; width: 24px; text-align: center; }
.sidebar-logout { padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.08); }
.sidebar-logout a { color: #D98B6E; font-size: 0.88rem; text-decoration: none; }
.main-content { flex: 1; margin-left: 260px; }
.top-bar { background: white; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.top-bar h1 { font-family: 'Playfair Display', serif; font-size: 1.4rem; }
.content-area { padding: 32px; }
.card { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 32px; max-width: 700px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 0.88rem; font-weight: 500; color: #3E2A1A; margin-bottom: 8px; }
.form-group input, .form-group textarea { width: 100%; padding: 12px 16px; border: 1px solid #E8D5B7; border-radius: 12px; font-family: 'DM Sans', sans-serif; font-size: 0.95rem; transition: all 0.3s; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: #C4623A; box-shadow: 0 0 0 3px rgba(196,98,58,0.1); }
.form-group textarea { resize: vertical; min-height: 100px; }
.checkbox-group { display: flex; align-items: center; gap: 10px; }
.checkbox-group input { width: auto; }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 12px 24px; border-radius: 50px; font-size: 0.9rem; font-weight: 600; text-decoration: none; transition: all 0.3s; border: none; cursor: pointer; }
.btn-primary { background: #C4623A; color: white; }
.btn-primary:hover { background: #3E2A1A; }
.btn-secondary { background: #E8D5B7; color: #3E2A1A; }
.error { background: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
.form-actions { display: flex; gap: 12px; margin-top: 24px; }
</style>
</head>
<body>
<div class="admin-layout">
  <div class="sidebar">
    <div class="sidebar-logo"><span class="logo-icon">&#9752;</span> Tribal Crafts</div>
    <div class="sidebar-nav">
      <a href="dashboard.php"><span class="icon">&#128202;</span> Dashboard</a>
      <a href="artisans.php" class="active"><span class="icon">&#127912;</span> Artisans</a>
      <a href="products.php"><span class="icon">&#128230;</span> Products</a>
      <a href="messages.php"><span class="icon">&#128172;</span> Messages</a>
    </div>
    <div class="sidebar-logout"><a href="logout.php">&#128682; Logout</a></div>
  </div>
  <div class="main-content">
    <div class="top-bar"><h1>Edit Artisan: <?php echo e($artisan['name']); ?></h1></div>
    <div class="content-area">
      <div class="card">
        <?php if ($error): ?><div class="error"><?php echo e($error); ?></div><?php endif; ?>
        <form method="POST" action="">
          <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" required value="<?php echo e($artisan['name']); ?>">
          </div>
          <div class="form-group">
            <label>Location *</label>
            <input type="text" name="location" required value="<?php echo e($artisan['location']); ?>">
          </div>
          <div class="form-group">
            <label>Specialty *</label>
            <input type="text" name="specialty" required value="<?php echo e($artisan['specialty']); ?>">
          </div>
          <div class="form-group">
            <label>Bio *</label>
            <textarea name="bio" required><?php echo e($artisan['bio']); ?></textarea>
          </div>
          <div class="form-group">
            <label>Phone *</label>
            <input type="tel" name="phone" required value="<?php echo e($artisan['phone']); ?>">
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required value="<?php echo e($artisan['email']); ?>">
          </div>
          <div class="form-group">
            <div class="checkbox-group">
              <input type="checkbox" name="is_featured" id="is_featured" <?php echo $artisan['is_featured'] ? 'checked' : ''; ?>>
              <label for="is_featured" style="margin:0;">Featured on homepage</label>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Artisan</button>
            <a href="artisans.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
