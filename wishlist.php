<?php
/**
 * Wishlist Page
 */
$pageTitle = 'My Wishlist';
require_once 'includes/header.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$sessionId = $_SESSION['session_id'];

// Handle wishlist actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    
    $db = getDB();
    
    if ($action === 'add' && $productId > 0) {
        // Check if already in wishlist
        $stmt = $db->prepare("SELECT id FROM wishlist WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$sessionId, $productId]);
        if (!$stmt->fetch()) {
            $db->prepare("INSERT INTO wishlist (session_id, product_id) VALUES (?, ?)")->execute([$sessionId, $productId]);
        }
        header("Location: wishlist.php");
        exit;
    }
    
    if ($action === 'remove' && $productId > 0) {
        $db->prepare("DELETE FROM wishlist WHERE session_id = ? AND product_id = ?")->execute([$sessionId, $productId]);
        header("Location: wishlist.php");
        exit;
    }
    
    if ($action === 'move_to_cart' && $productId > 0) {
        // Add to cart
        $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$sessionId, $productId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $db->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?")->execute([$existing['id']]);
        } else {
            $db->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)")->execute([$sessionId, $productId]);
        }
        
        // Remove from wishlist
        $db->prepare("DELETE FROM wishlist WHERE session_id = ? AND product_id = ?")->execute([$sessionId, $productId]);
        header("Location: wishlist.php");
        exit;
    }
}

// Get wishlist items
$db = getDB();
$stmt = $db->prepare("
    SELECT w.*, p.name, p.price, p.description, p.image_url, p.tag, a.name as artisan_name
    FROM wishlist w
    LEFT JOIN products p ON w.product_id = p.id
    LEFT JOIN artisans a ON p.artisan_id = a.id
    WHERE w.session_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$sessionId]);
$wishlistItems = $stmt->fetchAll();
?>

<section class="page-hero" style="padding: 140px 0 60px;">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>My Wishlist</span>
    </div>
    <h1>My Wishlist</h1>
    <p><?php echo count($wishlistItems); ?> item<?php echo count($wishlistItems) !== 1 ? 's' : ''; ?> saved</p>
  </div>
</section>

<section class="section-pad" style="padding-top: 40px;">
  <div class="container">
    
    <?php if (empty($wishlistItems)): ?>
      <div style="text-align: center; padding: 80px 20px;">
        <div style="font-size: 4rem; margin-bottom: 24px;">&#10084;</div>
        <h2 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 16px;">Your wishlist is empty</h2>
        <p style="color: var(--text-light); margin-bottom: 32px;">Save products you love to your wishlist and buy them later!</p>
        <a href="products.php" class="btn-primary">Browse Products &#8594;</a>
      </div>
    <?php else: ?>
      
      <div class="products-grid">
        <?php foreach ($wishlistItems as $item): ?>
        <div class="product-card">
          <div class="product-img" style="background: linear-gradient(135deg, #D4A574, #BFA06A);">
            &#129680;
            <?php if ($item['tag']): ?>
            <span class="product-tag"><?php echo e($item['tag']); ?></span>
            <?php endif; ?>
          </div>
          <div class="product-details">
            <h3><?php echo e($item['name']); ?></h3>
            <div class="product-artisan-name">by <?php echo e($item['artisan_name']); ?></div>
            <p class="product-desc"><?php echo e(substr($item['description'], 0, 80)); ?>...</p>
            <div class="product-footer">
              <span class="product-price"><?php echo formatPrice($item['price']); ?></span>
            </div>
            <div style="display: flex; gap: 8px; margin-top: 16px;">
              <form method="POST" style="flex: 1;">
                <input type="hidden" name="action" value="move_to_cart">
                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                <button type="submit" class="btn-primary" style="width: 100%; padding: 10px; font-size: 0.85rem;">&#128722; Add to Cart</button>
              </form>
              <form method="POST">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                <button type="submit" style="padding: 10px 16px; border-radius: 50px; background: #fee; color: #c33; border: none; cursor: pointer; font-size: 0.85rem;">&#10005;</button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <div style="text-align: center; margin-top: 48px;">
        <a href="cart.php" class="btn-primary">Go to Cart &#8594;</a>
      </div>
    
    <?php endif; ?>
    
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
