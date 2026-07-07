<?php
/**
 * Admin - Manage Orders
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
requireAdmin();

$db = getDB();

// Handle status update
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = sanitize($_GET['status']);
    $db->prepare("UPDATE orders SET order_status = ? WHERE id = ?")->execute([$status, $id]);
    redirect('orders.php');
}

$orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders — Admin</title>
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
table td { padding: 16px 24px; border-top: 1px solid #F0E4D0; font-size: 0.9rem; vertical-align: top; }
table tr:hover td { background: #FDF6EC; }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s; border: none; cursor: pointer; }
.btn-primary { background: #C4623A; color: white; }
.btn-sm { padding: 6px 12px; font-size: 0.8rem; }
.badge { display: inline-block; padding: 3px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.badge-pending { background: rgba(200,168,78,0.1); color: #C8A84E; }
.badge-completed { background: rgba(45,80,22,0.1); color: #2D5016; }
.badge-confirmed { background: rgba(45,80,22,0.1); color: #2D5016; }
.badge-processing { background: rgba(0,112,186,0.1); color: #0070ba; }
.badge-shipped { background: rgba(0,112,186,0.1); color: #0070ba; }
.badge-delivered { background: rgba(45,80,22,0.1); color: #2D5016; }
.badge-cancelled { background: rgba(220,53,69,0.1); color: #dc3545; }
.badge-failed { background: rgba(220,53,69,0.1); color: #dc3545; }
select { padding: 6px 12px; border: 1px solid #E8D5B7; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; }
</style>
</head>
<body>
<div class="admin-layout">
  <div class="sidebar">
    <div class="sidebar-logo"><span class="logo-icon">&#9752;</span> Tribal Crafts</div>
    <div class="sidebar-nav">
      <a href="dashboard.php"><span class="icon">&#128202;</span> Dashboard</a>
      <a href="artisans.php"><span class="icon">&#127912;</span> Artisans</a>
      <a href="products.php"><span class="icon">&#128230;</span> Products</a>
      <a href="artisan_requests.php"><span class="icon">&#128221;</span> Artisan Requests</a>
      <a href="product_submissions.php"><span class="icon">&#128229;</span> Product Submissions</a>
      <a href="orders.php" class="active"><span class="icon">&#128179;</span> Orders</a>
      <a href="messages.php"><span class="icon">&#128172;</span> Messages</a>
    </div>
    <div class="sidebar-logout"><a href="logout.php">&#128682; Logout</a></div>
  </div>
  <div class="main-content">
    <div class="top-bar"><h1>Orders</h1></div>
    <div class="content-area">
      <div class="card">
        <table>
          <thead>
            <tr>
              <th>Order #</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Date</th>
              <th>Update Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($orders)): ?>
              <tr><td colspan="7" style="text-align: center; padding: 40px; color: #7A6555;">No orders yet</td></tr>
            <?php else: ?>
              <?php foreach ($orders as $order): ?>
              <tr>
                <td><strong><?php echo e($order['order_number']); ?></strong></td>
                <td>
                  <?php echo e($order['customer_name']); ?><br>
                  <small style="color:#7A6555;"><?php echo e($order['customer_email']); ?></small>
                </td>
                <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                <td>
                  <span class="badge badge-<?php echo $order['payment_status']; ?>"><?php echo ucfirst($order['payment_status']); ?></span>
                  <br><small style="color:#7A6555;"><?php echo e(ucfirst($order['payment_method'])); ?></small>
                </td>
                <td><span class="badge badge-<?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                <td><?php echo formatDate($order['created_at']); ?></td>
                <td>
                  <form method="GET" style="display: flex; gap: 8px;">
                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                      <option value="">Change...</option>
                      <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="confirmed" <?php echo $order['order_status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                      <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                      <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                      <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                      <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                  </form>
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
</body>
</html>
