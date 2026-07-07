<?php
/**
 * Admin Dashboard
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
requireAdmin();

$db = getDB();

// Get stats
$artisanCount = $db->query("SELECT COUNT(*) FROM artisans")->fetchColumn();
$productCount = $db->query("SELECT COUNT(*) FROM products WHERE is_available = 1")->fetchColumn();
$messageCount = $db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$unreadCount = getUnreadMessageCount();

// New stats for e-commerce features
$pendingRequests = 0;
$pendingSubmissions = 0;
$orderCount = 0;
$totalRevenue = 0;

try {
    $pendingRequests = $db->query("SELECT COUNT(*) FROM artisan_requests WHERE status = 'pending'")->fetchColumn();
    $pendingSubmissions = $db->query("SELECT COUNT(*) FROM product_submissions WHERE status = 'pending'")->fetchColumn();
    $orderCount = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRevenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'completed'")->fetchColumn();
} catch (Exception $e) {
    // Tables might not exist yet
}

// Recent messages
$recentMessages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Admin — Tribal Crafts</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DM Sans', sans-serif; background: #F5F0E8; color: #3E2A1A; }
.admin-layout { display: flex; min-height: 100vh; }

/* Sidebar */
.sidebar {
  width: 260px; background: #3E2A1A; color: #E8D5B7;
  padding: 24px 0; position: fixed; top: 0; bottom: 0;
  display: flex; flex-direction: column;
}
.sidebar-logo {
  font-family: 'Playfair Display', serif;
  font-size: 1.3rem; font-weight: 900; color: #FDF6EC;
  padding: 0 24px 24px; border-bottom: 1px solid rgba(255,255,255,0.08);
  display: flex; align-items: center; gap: 10px;
}
.sidebar-logo .logo-icon {
  width: 34px; height: 34px; background: #C4623A;
  border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 1rem; color: white;
}
.sidebar-nav { flex: 1; padding: 16px 0; }
.sidebar-nav a {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 24px; color: #E8D5B7; font-size: 0.92rem;
  text-decoration: none; transition: all 0.3s;
}
.sidebar-nav a:hover, .sidebar-nav a.active {
  background: rgba(196,98,58,0.15); color: #FDF6EC;
  border-right: 3px solid #C4623A;
}
.sidebar-nav a .icon { font-size: 1.1rem; width: 24px; text-align: center; }
.sidebar-logout {
  padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.08);
}
.sidebar-logout a {
  color: #D98B6E; font-size: 0.88rem; text-decoration: none;
}

/* Main Content */
.main-content { flex: 1; margin-left: 260px; }
.top-bar {
  background: white; padding: 16px 32px;
  display: flex; justify-content: space-between; align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.top-bar h1 { font-family: 'Playfair Display', serif; font-size: 1.4rem; }
.top-bar .admin-info { font-size: 0.88rem; color: #7A6555; }
.content-area { padding: 32px; }

/* Stats Cards */
.stats-grid {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 24px; margin-bottom: 32px;
}
.stat-card {
  background: white; border-radius: 16px; padding: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.stat-card .stat-icon {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; margin-bottom: 16px;
}
.stat-card .stat-value {
  font-family: 'Playfair Display', serif;
  font-size: 2rem; font-weight: 900; color: #3E2A1A;
}
.stat-card .stat-label { font-size: 0.85rem; color: #7A6555; margin-top: 4px; }
.stat-card.artisans .stat-icon { background: rgba(196,98,58,0.1); }
.stat-card.products .stat-icon { background: rgba(45,80,22,0.1); }
.stat-card.messages .stat-icon { background: rgba(200,168,78,0.1); }
.stat-card.unread .stat-icon { background: rgba(196,98,58,0.15); }

/* Tables */
.card {
  background: white; border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden;
}
.card-header {
  padding: 20px 24px; border-bottom: 1px solid #F0E4D0;
  display: flex; justify-content: space-between; align-items: center;
}
.card-header h2 { font-family: 'Playfair Display', serif; font-size: 1.2rem; }
.card-body { padding: 0; }
table { width: 100%; border-collapse: collapse; }
table th {
  text-align: left; padding: 12px 24px;
  font-size: 0.8rem; font-weight: 600;
  color: #7A6555; text-transform: uppercase;
  letter-spacing: 0.05em; background: #FDF6EC;
}
table td {
  padding: 16px 24px; border-top: 1px solid #F0E4D0;
  font-size: 0.9rem;
}
table tr:hover td { background: #FDF6EC; }
.badge {
  display: inline-block; padding: 3px 10px;
  border-radius: 50px; font-size: 0.75rem; font-weight: 600;
}
.badge-new { background: rgba(196,98,58,0.1); color: #C4623A; }
.badge-read { background: rgba(45,80,22,0.1); color: #2D5016; }
.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 50px;
  font-size: 0.85rem; font-weight: 500;
  text-decoration: none; transition: all 0.3s;
}
.btn-primary { background: #C4623A; color: white; }
.btn-primary:hover { background: #3E2A1A; }
.btn-sm { padding: 6px 12px; font-size: 0.8rem; }
.empty-state { text-align: center; padding: 40px; color: #7A6555; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .stats-grid { grid-template-columns: 1fr 1fr; }
}
</style>
</head>
<body>
<div class="admin-layout">
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-logo">
      <span class="logo-icon">&#9752;</span>
      Tribal Crafts
    </div>
    <div class="sidebar-nav">
      <a href="dashboard.php" class="active"><span class="icon">&#128202;</span> Dashboard</a>
      <a href="artisans.php"><span class="icon">&#127912;</span> Artisans</a>
      <a href="products.php"><span class="icon">&#128230;</span> Products</a>
      <a href="artisan_requests.php"><span class="icon">&#128221;</span> Artisan Requests <?php echo $pendingRequests > 0 ? "($pendingRequests)" : ''; ?></a>
      <a href="product_submissions.php"><span class="icon">&#128229;</span> Product Submissions <?php echo $pendingSubmissions > 0 ? "($pendingSubmissions)" : ''; ?></a>
      <a href="orders.php"><span class="icon">&#128179;</span> Orders</a>
      <a href="messages.php"><span class="icon">&#128172;</span> Messages <?php echo $unreadCount > 0 ? "($unreadCount)" : ''; ?></a>
    </div>
    <div class="sidebar-logout">
      <a href="logout.php">&#128682; Logout</a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="top-bar">
      <h1>Dashboard</h1>
      <div class="admin-info">Welcome, <?php echo e($_SESSION['admin_username']); ?></div>
    </div>
    <div class="content-area">
      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
        <div class="stat-card artisans">
          <div class="stat-icon">&#127912;</div>
          <div class="stat-value"><?php echo $artisanCount; ?></div>
          <div class="stat-label">Total Artisans</div>
        </div>
        <div class="stat-card products">
          <div class="stat-icon">&#128230;</div>
          <div class="stat-value"><?php echo $productCount; ?></div>
          <div class="stat-label">Active Products</div>
        </div>
        <div class="stat-card messages">
          <div class="stat-icon">&#128179;</div>
          <div class="stat-value"><?php echo $orderCount; ?></div>
          <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card unread">
          <div class="stat-icon">&#8377;</div>
          <div class="stat-value"><?php echo formatPrice($totalRevenue); ?></div>
          <div class="stat-label">Total Revenue</div>
        </div>
      </div>
      
      <!-- Additional Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 32px;">
        <div class="stat-card" style="border-left: 4px solid #C8A84E;">
          <div class="stat-icon" style="background: rgba(200,168,78,0.1);">&#128221;</div>
          <div class="stat-value"><?php echo $pendingRequests; ?></div>
          <div class="stat-label">Pending Artisan Requests</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #C4623A;">
          <div class="stat-icon" style="background: rgba(196,98,58,0.1);">&#128229;</div>
          <div class="stat-value"><?php echo $pendingSubmissions; ?></div>
          <div class="stat-label">Pending Product Submissions</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #2D5016;">
          <div class="stat-icon" style="background: rgba(45,80,22,0.1);">&#128233;</div>
          <div class="stat-value"><?php echo $unreadCount; ?></div>
          <div class="stat-label">Unread Messages</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card" style="margin-bottom: 32px;">
        <div class="card-header">
          <h2>Quick Actions</h2>
        </div>
        <div style="padding: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="artisan_add.php" class="btn btn-primary">+ Add Artisan</a>
          <a href="product_add.php" class="btn btn-primary">+ Add Product</a>
          <a href="artisan_requests.php" class="btn btn-primary">Review Artisan Requests <?php echo $pendingRequests > 0 ? "($pendingRequests)" : ''; ?></a>
          <a href="product_submissions.php" class="btn btn-primary">Review Product Submissions <?php echo $pendingSubmissions > 0 ? "($pendingSubmissions)" : ''; ?></a>
          <a href="orders.php" class="btn btn-primary">View Orders</a>
          <a href="messages.php" class="btn btn-primary">View Messages</a>
        </div>
      </div>

      <!-- Recent Messages -->
      <div class="card">
        <div class="card-header">
          <h2>Recent Messages</h2>
          <a href="messages.php" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Purpose</th>
                <th>Message</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentMessages)): ?>
                <tr><td colspan="5" class="empty-state">No messages yet</td></tr>
              <?php else: ?>
                <?php foreach ($recentMessages as $msg): ?>
                <tr>
                  <td><strong><?php echo e($msg['name']); ?></strong><br><small style="color:#7A6555;"><?php echo e($msg['email']); ?></small></td>
                  <td><?php echo e(ucfirst($msg['purpose'])); ?></td>
                  <td><?php echo e(substr($msg['message'], 0, 60)); ?>...</td>
                  <td><?php echo formatDate($msg['created_at']); ?></td>
                  <td>
                    <?php if ($msg['is_read']): ?>
                      <span class="badge badge-read">Read</span>
                    <?php else: ?>
                      <span class="badge badge-new">New</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
