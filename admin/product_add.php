<?php
/**
 * Admin - Add Product
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
requireAdmin();

$error = '';
$db = getDB();
$artisans = $db->query("SELECT id, name FROM artisans ORDER BY name")->fetchAll();
$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artisan_id = (int)($_POST['artisan_id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $tag = sanitize($_POST['tag'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name) || empty($category) || empty($description) || $price <= 0 || $artisan_id <= 0) {
        $error = 'Please fill in all required fields with valid values.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO products (artisan_id, name, category, description, price, tag, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$artisan_id, $name, $category, $description, $price, $tag ?: null, $is_featured]);
            redirect('products.php');
        } catch (PDOException $e) {
            $error = 'Failed to add product: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product — Admin</title>
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
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px 16px; border: 1px solid #E8D5B7; border-radius: 12px; font-family: 'DM Sans', sans-serif; font-size: 0.95rem; transition: all 0.3s; background: white; }
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #C4623A; box-shadow: 0 0 0 3px rgba(196,98,58,0.1); }
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
      <a href="artisans.php"><span class="icon">&#127912;</span> Artisans</a>
      <a href="products.php" class="active"><span class="icon">&#128230;</span> Products</a>
      <a href="messages.php"><span class="icon">&#128172;</span> Messages</a>
    </div>
    <div class="sidebar-logout"><a href="logout.php">&#128682; Logout</a></div>
  </div>
  <div class="main-content">
    <div class="top-bar"><h1>Add New Product</h1></div>
    <div class="content-area">
      <div class="card">
        <?php if ($error): ?><div class="error"><?php echo e($error); ?></div><?php endif; ?>
        <form method="POST" action="">
          <div class="form-group">
            <label>Artisan *</label>
            <select name="artisan_id" required>
              <option value="">Select an artisan</option>
              <?php foreach ($artisans as $a): ?>
                <option value="<?php echo $a['id']; ?>" <?php echo ($_POST['artisan_id'] ?? '') == $a['id'] ? 'selected' : ''; ?>><?php echo e($a['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" required value="<?php echo e($_POST['name'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Category *</label>
            <select name="category" required>
              <option value="">Select category</option>
              <?php foreach ($categories as $key => $label): ?>
                <option value="<?php echo $key; ?>" <?php echo ($_POST['category'] ?? '') === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Description *</label>
            <textarea name="description" required><?php echo e($_POST['description'] ?? ''); ?></textarea>
          </div>
          <div class="form-group">
            <label>Price (INR) *</label>
            <input type="number" name="price" step="0.01" min="0" required value="<?php echo e($_POST['price'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Tag (e.g. Popular, New, Bestseller)</label>
            <input type="text" name="tag" value="<?php echo e($_POST['tag'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <div class="checkbox-group">
              <input type="checkbox" name="is_featured" id="is_featured" <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
              <label for="is_featured" style="margin:0;">Featured product</label>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
