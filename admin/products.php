<?php
/**
 * Admin - Manage Products
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
requireAdmin();

$db = getDB();

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM products WHERE id = ?")->execute([(int)$_GET['delete']]);
    redirect('products.php');
}

if (isset($_GET['toggle'])) {
    $db->prepare("UPDATE products SET is_available = NOT is_available WHERE id = ?")->execute([(int)$_GET['toggle']]);
    redirect('products.php');
}

$products = $db->query("SELECT p.*, a.name as artisan_name FROM products p LEFT JOIN artisans a ON p.artisan_id = a.id ORDER BY p.created_at DESC")->fetchAll();
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Products — Admin</title>
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
.card { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
table th { text-align: left; padding: 12px 24px; font-size: 0.8rem; font-weight: 600; color: #7A6555; text-transform: uppercase; letter-spacing: 0.05em; background: #FDF6EC; }
table td { padding: 16px 24px; border-top: 1px solid #F0E4D0; font-size: 0.9rem; }
table tr:hover td { background: #FDF6EC; }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s; border: none; cursor: pointer; }
.btn-primary { background: #C4623A; color: white; }
.btn-primary:hover { background: #3E2A1A; }
.btn-danger { background: #dc3545; color: white; }
.btn-sm { padding: 6px 12px; font-size: 0.8rem; }
.badge { display: inline-block; padding: 3px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.badge-active { background: rgba(45,80,22,0.1); color: #2D5016; }
.badge-inactive { background: rgba(220,53,69,0.1); color: #dc3545; }
.actions { display: flex; gap: 8px; }
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
    <div class="top-bar">
      <h1>Manage Products</h1>
      <a href="product_add.php" class="btn btn-primary">+ Add Product</a>
    </div>
    <div class="content-area">
      <div class="card">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Artisan</th>
              <th>Category</th>
              <th>Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
              <td>
                <strong><?php echo e($p['name']); ?></strong>
                <?php if ($p['tag']): ?><br><small style="color:#C4623A;"><?php echo e($p['tag']); ?></small><?php endif; ?>
              </td>
              <td><?php echo e($p['artisan_name']); ?></td>
              <td><?php echo e($categories[$p['category']] ?? $p['category']); ?></td>
              <td><?php echo formatPrice($p['price']); ?></td>
              <td>
                <?php if ($p['is_available']): ?>
                  <span class="badge badge-active">Active</span>
                <?php else: ?>
                  <span class="badge badge-inactive">Inactive</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="actions">
                  <a href="product_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                  <a href="products.php?toggle=<?php echo $p['id']; ?>" class="btn btn-sm" style="background:#2D5016;color:white;">
                    <?php echo $p['is_available'] ? 'Deactivate' : 'Activate'; ?>
                  </a>
                  <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
