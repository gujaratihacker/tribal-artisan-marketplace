<?php
/**
 * Admin - Messages
 */
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
requireAdmin();

$db = getDB();

// Mark as read
if (isset($_GET['read'])) {
    $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([(int)$_GET['read']]);
    redirect('messages.php');
}

// Delete
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([(int)$_GET['delete']]);
    redirect('messages.php');
}

// View single message
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewMessage = null;
if ($viewId) {
    $viewMessage = $db->prepare("SELECT * FROM contact_messages WHERE id = ?")->execute([$viewId]);
    $viewMessage = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $viewMessage->execute([$viewId]);
    $viewMessage = $viewMessage->fetch();
    if ($viewMessage && !$viewMessage['is_read']) {
        $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$viewId]);
    }
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages — Admin</title>
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
.card { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 24px; }
.card-header { padding: 20px 24px; border-bottom: 1px solid #F0E4D0; display: flex; justify-content: space-between; align-items: center; }
.card-header h2 { font-family: 'Playfair Display', serif; font-size: 1.2rem; }
table { width: 100%; border-collapse: collapse; }
table th { text-align: left; padding: 12px 24px; font-size: 0.8rem; font-weight: 600; color: #7A6555; text-transform: uppercase; letter-spacing: 0.05em; background: #FDF6EC; }
table td { padding: 16px 24px; border-top: 1px solid #F0E4D0; font-size: 0.9rem; }
table tr:hover td { background: #FDF6EC; }
table tr.unread td { background: #FDF6EC; font-weight: 500; }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s; border: none; cursor: pointer; }
.btn-primary { background: #C4623A; color: white; }
.btn-primary:hover { background: #3E2A1A; }
.btn-danger { background: #dc3545; color: white; }
.btn-sm { padding: 6px 12px; font-size: 0.8rem; }
.badge { display: inline-block; padding: 3px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.badge-new { background: rgba(196,98,58,0.1); color: #C4623A; }
.badge-read { background: rgba(45,80,22,0.1); color: #2D5016; }
.actions { display: flex; gap: 8px; }
.message-detail { padding: 32px; }
.message-detail .meta { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #F0E4D0; }
.message-detail .meta h3 { font-family: 'Playfair Display', serif; font-size: 1.3rem; margin-bottom: 8px; }
.message-detail .meta p { font-size: 0.9rem; color: #7A6555; margin-bottom: 4px; }
.message-detail .body { font-size: 0.95rem; line-height: 1.8; color: #3E2A1A; }
.empty-state { text-align: center; padding: 60px 24px; color: #7A6555; }
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
      <a href="messages.php" class="active"><span class="icon">&#128172;</span> Messages</a>
    </div>
    <div class="sidebar-logout"><a href="logout.php">&#128682; Logout</a></div>
  </div>
  <div class="main-content">
    <div class="top-bar">
      <h1><?php echo $viewMessage ? 'Message Detail' : 'Contact Messages'; ?></h1>
      <?php if ($viewMessage): ?>
        <a href="messages.php" class="btn btn-primary">&#8592; Back to Messages</a>
      <?php endif; ?>
    </div>
    <div class="content-area">
      <?php if ($viewMessage): ?>
        <!-- Message Detail View -->
        <div class="card">
          <div class="message-detail">
            <div class="meta">
              <h3><?php echo e($viewMessage['name']); ?></h3>
              <p>Email: <?php echo e($viewMessage['email']); ?></p>
              <?php if ($viewMessage['phone']): ?>
                <p>Phone: <?php echo e($viewMessage['phone']); ?></p>
              <?php endif; ?>
              <p>Purpose: <strong><?php echo e(ucfirst($viewMessage['purpose'])); ?></strong></p>
              <p>Date: <?php echo formatDate($viewMessage['created_at']); ?></p>
            </div>
            <div class="body">
              <?php echo nl2br(e($viewMessage['message'])); ?>
            </div>
          </div>
        </div>
      <?php else: ?>
        <!-- Messages List -->
        <div class="card">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Purpose</th>
                <th>Message</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($messages)): ?>
                <tr><td colspan="7" class="empty-state">No messages yet</td></tr>
              <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                <tr class="<?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
                  <td><strong><?php echo e($msg['name']); ?></strong></td>
                  <td><?php echo e($msg['email']); ?></td>
                  <td><?php echo e(ucfirst($msg['purpose'])); ?></td>
                  <td><?php echo e(substr($msg['message'], 0, 50)); ?>...</td>
                  <td><?php echo formatDate($msg['created_at']); ?></td>
                  <td>
                    <?php if ($msg['is_read']): ?>
                      <span class="badge badge-read">Read</span>
                    <?php else: ?>
                      <span class="badge badge-new">New</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="actions">
                      <a href="messages.php?view=<?php echo $msg['id']; ?>" class="btn btn-sm btn-primary">View</a>
                      <a href="messages.php?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this message?')">Delete</a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
