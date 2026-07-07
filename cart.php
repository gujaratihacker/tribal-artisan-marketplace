<?php
/**
 * Shopping Cart Page
 */
$pageTitle = 'Shopping Cart';
require_once 'includes/header.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$sessionId = $_SESSION['session_id'];

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    $db = getDB();
    
    if ($action === 'add' && $productId > 0) {
        // Check if product already in cart
        $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$sessionId, $productId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update quantity
            $newQty = $existing['quantity'] + $quantity;
            $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?")->execute([$newQty, $existing['id']]);
        } else {
            // Add new item
            $db->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)")->execute([$sessionId, $productId, $quantity]);
        }
        
        header("Location: cart.php");
        exit;
    }
    
    if ($action === 'update' && $productId > 0) {
        if ($quantity > 0) {
            $db->prepare("UPDATE cart SET quantity = ? WHERE session_id = ? AND product_id = ?")->execute([$quantity, $sessionId, $productId]);
        } else {
            $db->prepare("DELETE FROM cart WHERE session_id = ? AND product_id = ?")->execute([$sessionId, $productId]);
        }
        
        header("Location: cart.php");
        exit;
    }
    
    if ($action === 'remove' && $productId > 0) {
        $db->prepare("DELETE FROM cart WHERE session_id = ? AND product_id = ?")->execute([$sessionId, $productId]);
        header("Location: cart.php");
        exit;
    }
}

// Get cart items
$db = getDB();
$stmt = $db->prepare("
    SELECT c.*, p.name, p.price, p.image_url, a.name as artisan_name
    FROM cart c
    LEFT JOIN products p ON c.product_id = p.id
    LEFT JOIN artisans a ON p.artisan_id = a.id
    WHERE c.session_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$sessionId]);
$cartItems = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
$totalItems = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}

$shipping = $subtotal > 0 ? ($subtotal > 2000 ? 0 : 99) : 0;
$tax = $subtotal * 0.05; // 5% GST
$total = $subtotal + $shipping + $tax;
?>

<section class="page-hero" style="padding: 140px 0 60px;">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>Shopping Cart</span>
    </div>
    <h1>Shopping Cart</h1>
    <p><?php echo $totalItems; ?> item<?php echo $totalItems !== 1 ? 's' : ''; ?> in your cart</p>
  </div>
</section>

<section class="section-pad" style="padding-top: 40px;">
  <div class="container">
    
    <?php if (empty($cartItems)): ?>
      <div style="text-align: center; padding: 80px 20px;">
        <div style="font-size: 4rem; margin-bottom: 24px;">&#128722;</div>
        <h2 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 16px;">Your cart is empty</h2>
        <p style="color: var(--text-light); margin-bottom: 32px;">Add some beautiful handcrafted products to get started!</p>
        <a href="products.php" class="btn-primary">Browse Products &#8594;</a>
      </div>
    <?php else: ?>
      
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;">
        
        <!-- Cart Items -->
        <div>
          <?php foreach ($cartItems as $item): ?>
          <div style="background: white; padding: 24px; border-radius: 16px; margin-bottom: 16px; box-shadow: var(--shadow-sm); display: flex; gap: 24px; align-items: center;">
            
            <div style="width: 120px; height: 120px; background: var(--sand-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; flex-shrink: 0;">
              &#129680;
            </div>
            
            <div style="flex: 1;">
              <h3 style="font-size: 1.1rem; color: var(--bark); margin-bottom: 4px;"><?php echo e($item['name']); ?></h3>
              <p style="font-size: 0.85rem; color: var(--terracotta); margin-bottom: 12px;">by <?php echo e($item['artisan_name']); ?></p>
              <div style="font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; color: var(--forest);">
                <?php echo formatPrice($item['price']); ?>
              </div>
            </div>
            
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px;">
              <form method="POST" style="display: flex; align-items: center; gap: 8px;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                <button type="submit" name="quantity" value="<?php echo max(0, $item['quantity'] - 1); ?>" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--sand); background: white; cursor: pointer;">-</button>
                <span style="min-width: 30px; text-align: center; font-weight: 600;"><?php echo $item['quantity']; ?></span>
                <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--sand); background: white; cursor: pointer;">+</button>
              </form>
              
              <form method="POST">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                <button type="submit" style="background: none; border: none; color: #dc3545; font-size: 0.85rem; cursor: pointer; text-decoration: underline;">Remove</button>
              </form>
            </div>
            
          </div>
          <?php endforeach; ?>
        </div>
        
        <!-- Order Summary -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-md); position: sticky; top: 100px;">
          <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--bark); margin-bottom: 24px;">Order Summary</h3>
          
          <div style="border-bottom: 1px solid var(--sand-light); padding-bottom: 16px; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
              <span style="color: var(--text-light);">Subtotal</span>
              <span style="font-weight: 600;"><?php echo formatPrice($subtotal); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
              <span style="color: var(--text-light);">Shipping</span>
              <span style="font-weight: 600;"><?php echo $shipping > 0 ? formatPrice($shipping) : 'FREE'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
              <span style="color: var(--text-light);">GST (5%)</span>
              <span style="font-weight: 600;"><?php echo formatPrice($tax); ?></span>
            </div>
          </div>
          
          <div style="display: flex; justify-content: space-between; margin-bottom: 24px;">
            <span style="font-size: 1.1rem; font-weight: 700; color: var(--bark);">Total</span>
            <span style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 900; color: var(--forest);"><?php echo formatPrice($total); ?></span>
          </div>
          
          <?php if ($subtotal < 2000): ?>
          <div style="background: rgba(45,80,22,0.08); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; color: var(--forest);">
            &#127873; Add <?php echo formatPrice(2000 - $subtotal); ?> more for FREE shipping!
          </div>
          <?php endif; ?>
          
          <a href="checkout.php" class="btn-primary" style="display: block; text-align: center; width: 100%;">Proceed to Checkout &#8594;</a>
          <a href="products.php" style="display: block; text-align: center; margin-top: 12px; color: var(--terracotta); font-size: 0.9rem;">Continue Shopping</a>
        </div>
        
      </div>
    
    <?php endif; ?>
    
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
