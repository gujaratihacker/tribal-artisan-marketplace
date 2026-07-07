<?php
/**
 * Products Page
 */
$pageTitle = 'Products';
require_once 'includes/header.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$sessionId = $_SESSION['session_id'];

// Handle cart/wishlist actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    
    $db = getDB();
    
    if ($action === 'add_to_cart' && $productId > 0) {
        $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$sessionId, $productId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $db->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?")->execute([$existing['id']]);
        } else {
            $db->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)")->execute([$sessionId, $productId]);
        }
        
        header("Location: products.php?category=" . $category . "&added=1");
        exit;
    }
    
    if ($action === 'add_to_wishlist' && $productId > 0) {
        $stmt = $db->prepare("SELECT id FROM wishlist WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$sessionId, $productId]);
        if (!$stmt->fetch()) {
            $db->prepare("INSERT INTO wishlist (session_id, product_id) VALUES (?, ?)")->execute([$sessionId, $productId]);
        }
        header("Location: products.php?category=" . $category . "&wishlisted=1");
        exit;
    }
}

// Get wishlist count for current session
$db = getDB();
$stmt = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE session_id = ?");
$stmt->execute([$sessionId]);
$wishlistCount = $stmt->fetch()['count'];

// Get category filter from URL
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$products = getAllProducts($category);
$categories = getCategories();

// Check if product is in wishlist
function isInWishlist($productId, $sessionId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM wishlist WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$sessionId, $productId]);
    return $stmt->fetch() ? true : false;
}
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>Products</span>
    </div>
    <h1>Bamboo Collection</h1>
    <p>Authentic bamboo products crafted by hand, each piece telling a story of tradition and heritage</p>
  </div>
</section>

<!-- ALL PRODUCTS -->
<section id="products" class="section-pad">
  <div class="container">
    
    <?php if (isset($_GET['added'])): ?>
    <div style="background: rgba(45,80,22,0.1); border: 1px solid rgba(45,80,22,0.3); color: var(--forest); padding: 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
      <span>&#10003; Product added to cart!</span>
      <a href="cart.php" style="color: var(--forest); font-weight: 600;">View Cart &#8594;</a>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['wishlisted'])): ?>
    <div style="background: rgba(196,98,58,0.1); border: 1px solid rgba(196,98,58,0.3); color: var(--terracotta); padding: 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
      <span>&#10084; Product added to wishlist!</span>
      <a href="wishlist.php" style="color: var(--terracotta); font-weight: 600;">View Wishlist &#8594;</a>
    </div>
    <?php endif; ?>
    
    <div class="product-filters animate-in">
      <a href="products.php" class="filter-btn <?php echo $category === 'all' ? 'active' : ''; ?>">All Products</a>
      <?php foreach ($categories as $key => $label): ?>
      <a href="products.php?category=<?php echo $key; ?>" class="filter-btn <?php echo $category === $key ? 'active' : ''; ?>"><?php echo e($label); ?></a>
      <?php endforeach; ?>
    </div>
    <div class="products-grid">
      <?php if (empty($products)): ?>
        <p style="text-align: center; color: var(--text-light); grid-column: 1 / -1; padding: 40px;">No products found in this category.</p>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
        <div class="product-card animate-in" data-category="<?php echo e($product['category']); ?>">
          <div class="product-img" style="background: linear-gradient(135deg, #D4A574, #BFA06A);">
            &#129680;
            <?php if ($product['tag']): ?>
            <span class="product-tag"><?php echo e($product['tag']); ?></span>
            <?php endif; ?>
            
            <?php if (isInWishlist($product['id'], $sessionId)): ?>
            <div style="position: absolute; top: 14px; right: 14px; background: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: var(--shadow-sm);">
              &#10084;
            </div>
            <?php endif; ?>
          </div>
          <div class="product-details">
            <h3><?php echo e($product['name']); ?></h3>
            <div class="product-artisan-name">by <?php echo e($product['artisan_name']); ?></div>
            <p class="product-desc"><?php echo e($product['description']); ?></p>
            <div class="product-footer">
              <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
              <a href="artisans.php" class="product-contact-link">Contact Artisan &#8594;</a>
            </div>
            
            <div style="display: flex; gap: 8px; margin-top: 16px;">
              <form method="POST" style="flex: 1;">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <button type="submit" class="btn-primary" style="width: 100%; padding: 10px; font-size: 0.85rem;">&#128722; Add to Cart</button>
              </form>
              <form method="POST">
                <input type="hidden" name="action" value="add_to_wishlist">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <button type="submit" style="padding: 10px 16px; border-radius: 50px; background: <?php echo isInWishlist($product['id'], $sessionId) ? 'var(--terracotta)' : 'var(--cream)'; ?>; color: <?php echo isInWishlist($product['id'], $sessionId) ? 'white' : 'var(--terracotta)'; ?>; border: 1px solid var(--terracotta); cursor: pointer; font-size: 1rem;" title="<?php echo isInWishlist($product['id'], $sessionId) ? 'In Wishlist' : 'Add to Wishlist'; ?>">
                  &#10084;
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
