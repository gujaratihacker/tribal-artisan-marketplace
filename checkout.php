<?php
/**
 * Checkout Page
 */
$pageTitle = 'Checkout';
require_once 'includes/header.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$sessionId = $_SESSION['session_id'];

// Get cart items
$db = getDB();
$stmt = $db->prepare("
    SELECT c.*, p.name, p.price, a.name as artisan_name
    FROM cart c
    LEFT JOIN products p ON c.product_id = p.id
    LEFT JOIN artisans a ON p.artisan_id = a.id
    WHERE c.session_id = ?
");
$stmt->execute([$sessionId]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal > 2000 ? 0 : 99;
$tax = $subtotal * 0.05;
$total = $subtotal + $shipping + $tax;

$formError = '';

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');
    $country = sanitize($_POST['country'] ?? 'India');
    $paymentMethod = sanitize($_POST['payment_method'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($state) || empty($pincode) || empty($paymentMethod)) {
        $formError = 'Please fill in all required fields.';
    } else {
        // Generate order number
        $orderNumber = 'TC' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        
        try {
            // Create order
            $stmt = $db->prepare("
                INSERT INTO orders (order_number, session_id, customer_name, customer_email, customer_phone, 
                shipping_address, city, state, pincode, country, subtotal, shipping_cost, tax_amount, 
                total_amount, payment_method, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $orderNumber, $sessionId, $name, $email, $phone,
                $address, $city, $state, $pincode, $country,
                $subtotal, $shipping, $tax, $total, $paymentMethod, $notes
            ]);
            
            $orderId = $db->lastInsertId();
            
            // Add order items
            foreach ($cartItems as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, artisan_name, quantity, price, subtotal)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId, $item['product_id'], $item['name'], $item['artisan_name'],
                    $item['quantity'], $item['price'], $itemSubtotal
                ]);
            }
            
            // Clear cart
            $db->prepare("DELETE FROM cart WHERE session_id = ?")->execute([$sessionId]);
            
            // Redirect based on payment method
            if ($paymentMethod === 'razorpay') {
                header("Location: payment_razorpay.php?order=" . $orderNumber);
            } elseif ($paymentMethod === 'paypal') {
                header("Location: payment_paypal.php?order=" . $orderNumber);
            } else {
                // COD - redirect to success
                header("Location: order_success.php?order=" . $orderNumber);
            }
            exit;
            
        } catch (PDOException $e) {
            $formError = 'Failed to place order. Please try again.';
        }
    }
}

// Get payment methods
$paymentMethods = $db->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY id")->fetchAll();
?>

<section class="page-hero" style="padding: 140px 0 60px;">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <a href="cart.php">Cart</a> <span>/</span> <span>Checkout</span>
    </div>
    <h1>Checkout</h1>
    <p>Complete your order</p>
  </div>
</section>

<section class="section-pad" style="padding-top: 40px;">
  <div class="container">
    
    <?php if ($formError): ?>
      <div style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
        <?php echo e($formError); ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="">
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;">
        
        <!-- Left: Forms -->
        <div>
          <!-- Shipping Information -->
          <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-sm); margin-bottom: 24px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--bark); margin-bottom: 24px;">&#128230; Shipping Information</h3>
            
            <div class="form-group">
              <label>Full Name *</label>
              <input type="text" name="name" required value="<?php echo e($_POST['name'] ?? ''); ?>">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
              <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label>Phone *</label>
                <input type="tel" name="phone" required value="<?php echo e($_POST['phone'] ?? ''); ?>">
              </div>
            </div>
            
            <div class="form-group">
              <label>Shipping Address *</label>
              <textarea name="address" required style="min-height: 80px;"><?php echo e($_POST['address'] ?? ''); ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
              <div class="form-group">
                <label>City *</label>
                <input type="text" name="city" required value="<?php echo e($_POST['city'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label>State *</label>
                <input type="text" name="state" required value="<?php echo e($_POST['state'] ?? ''); ?>">
              </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
              <div class="form-group">
                <label>PIN Code *</label>
                <input type="text" name="pincode" required pattern="[0-9]{6}" maxlength="6" value="<?php echo e($_POST['pincode'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label>Country *</label>
                <select name="country" required>
                  <option value="India" <?php echo ($_POST['country'] ?? 'India') === 'India' ? 'selected' : ''; ?>>India</option>
                  <option value="USA" <?php echo ($_POST['country'] ?? '') === 'USA' ? 'selected' : ''; ?>>USA</option>
                  <option value="UK" <?php echo ($_POST['country'] ?? '') === 'UK' ? 'selected' : ''; ?>>UK</option>
                  <option value="Canada" <?php echo ($_POST['country'] ?? '') === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                  <option value="Australia" <?php echo ($_POST['country'] ?? '') === 'Australia' ? 'selected' : ''; ?>>Australia</option>
                  <option value="Other" <?php echo ($_POST['country'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
              </div>
            </div>
            
            <div class="form-group">
              <label>Order Notes (Optional)</label>
              <textarea name="notes" placeholder="Any special instructions for delivery..."><?php echo e($_POST['notes'] ?? ''); ?></textarea>
            </div>
          </div>
          
          <!-- Payment Method -->
          <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--bark); margin-bottom: 24px;">&#128179; Payment Method</h3>
            
            <?php foreach ($paymentMethods as $method): ?>
            <label style="display: flex; align-items: center; gap: 16px; padding: 20px; border: 2px solid var(--sand-light); border-radius: 12px; margin-bottom: 12px; cursor: pointer; transition: all 0.3s;">
              <input type="radio" name="payment_method" value="<?php echo e($method['code']); ?>" required style="width: 20px; height: 20px;">
              <div style="flex: 1;">
                <div style="font-weight: 600; color: var(--bark); margin-bottom: 4px;"><?php echo e($method['name']); ?></div>
                <div style="font-size: 0.85rem; color: var(--text-light);">
                  <?php if ($method['code'] === 'razorpay'): ?>
                    UPI, Credit/Debit Cards, Netbanking, Wallets
                  <?php elseif ($method['code'] === 'paypal'): ?>
                    International payments accepted
                  <?php elseif ($method['code'] === 'cod'): ?>
                    Pay when you receive your order
                  <?php endif; ?>
                </div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        
        <!-- Right: Order Summary -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-md); position: sticky; top: 100px;">
          <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--bark); margin-bottom: 24px;">Order Summary</h3>
          
          <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
            <?php foreach ($cartItems as $item): ?>
            <div style="display: flex; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--sand-light);">
              <div style="width: 60px; height: 60px; background: var(--sand-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                &#129680;
              </div>
              <div style="flex: 1;">
                <div style="font-weight: 600; font-size: 0.9rem; color: var(--bark);"><?php echo e($item['name']); ?></div>
                <div style="font-size: 0.8rem; color: var(--text-light);">Qty: <?php echo $item['quantity']; ?></div>
              </div>
              <div style="font-weight: 600; color: var(--forest);"><?php echo formatPrice($item['price'] * $item['quantity']); ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          
          <div style="border-top: 2px solid var(--sand-light); padding-top: 20px;">
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
            <div style="display: flex; justify-content: space-between; margin-top: 16px; padding-top: 16px; border-top: 2px solid var(--sand-light);">
              <span style="font-size: 1.1rem; font-weight: 700; color: var(--bark);">Total</span>
              <span style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 900; color: var(--forest);"><?php echo formatPrice($total); ?></span>
            </div>
          </div>
          
          <button type="submit" name="place_order" class="btn-primary" style="width: 100%; margin-top: 24px; padding: 16px;">Place Order &#8594;</button>
          
          <div style="text-align: center; margin-top: 16px; font-size: 0.82rem; color: var(--text-light);">
            &#128274; Your payment information is secure
          </div>
        </div>
        
      </div>
    </form>
    
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
