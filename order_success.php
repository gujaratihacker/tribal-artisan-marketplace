<?php
/**
 * Order Success Page
 */
$pageTitle = 'Order Confirmed';
require_once 'includes/header.php';

$orderNumber = $_GET['order'] ?? '';
$transactionId = $_GET['tx'] ?? '';
$isDemo = isset($_GET['demo']);

if (empty($orderNumber)) {
    header("Location: index.php");
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

// Update order status if payment was successful
if (!$isDemo && !empty($transactionId)) {
    $db->prepare("UPDATE orders SET payment_status = 'completed', transaction_id = ?, order_status = 'confirmed' WHERE order_number = ?")
       ->execute([$transactionId, $orderNumber]);
    $order['payment_status'] = 'completed';
    $order['order_status'] = 'confirmed';
} elseif ($isDemo) {
    // Demo mode - mark as COD
    $db->prepare("UPDATE orders SET payment_status = 'pending', order_status = 'confirmed' WHERE order_number = ?")
       ->execute([$orderNumber]);
    $order['payment_status'] = 'pending';
    $order['order_status'] = 'confirmed';
}

// Get order items
$stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order['id']]);
$orderItems = $stmt->fetchAll();
?>

<section class="section-pad">
  <div class="container">
    <div style="max-width: 700px; margin: 0 auto; text-align: center;">
      
      <div style="background: white; padding: 48px; border-radius: 20px; box-shadow: var(--shadow-md); margin-bottom: 32px;">
        
        <div style="width: 80px; height: 80px; background: rgba(45,80,22,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 24px;">
          &#10003;
        </div>
        
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--bark); margin-bottom: 12px;">
          Order Confirmed!
        </h1>
        <p style="color: var(--text-light); font-size: 1.05rem; margin-bottom: 32px;">
          Thank you for your order. We've received your order and will process it shortly.
        </p>
        
        <div style="background: var(--cream); padding: 24px; border-radius: 12px; margin-bottom: 32px; text-align: left;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
              <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 4px;">Order Number</div>
              <div style="font-weight: 700; color: var(--bark); font-size: 1.1rem;"><?php echo e($order['order_number']); ?></div>
            </div>
            <div>
              <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 4px;">Order Date</div>
              <div style="font-weight: 600; color: var(--bark);"><?php echo formatDate($order['created_at']); ?></div>
            </div>
            <div>
              <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 4px;">Payment Status</div>
              <div style="font-weight: 600; color: <?php echo $order['payment_status'] === 'completed' ? 'var(--forest)' : 'var(--terracotta)'; ?>;">
                <?php echo ucfirst($order['payment_status']); ?>
              </div>
            </div>
            <div>
              <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 4px;">Total Amount</div>
              <div style="font-family: 'Playfair Display', serif; font-weight: 900; color: var(--forest); font-size: 1.3rem;">
                <?php echo formatPrice($order['total_amount']); ?>
              </div>
            </div>
          </div>
        </div>
        
        <?php if ($transactionId): ?>
        <div style="background: rgba(45,80,22,0.08); padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem;">
          <strong>Transaction ID:</strong> <?php echo e($transactionId); ?>
        </div>
        <?php endif; ?>
        
        <div style="text-align: left; margin-bottom: 32px;">
          <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--bark); margin-bottom: 16px;">Order Items</h3>
          
          <?php foreach ($orderItems as $item): ?>
          <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--sand-light);">
            <div>
              <div style="font-weight: 600; color: var(--bark);"><?php echo e($item['product_name']); ?></div>
              <div style="font-size: 0.85rem; color: var(--text-light);">by <?php echo e($item['artisan_name']); ?> | Qty: <?php echo $item['quantity']; ?></div>
            </div>
            <div style="font-weight: 600; color: var(--forest);"><?php echo formatPrice($item['subtotal']); ?></div>
          </div>
          <?php endforeach; ?>
          
          <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid var(--sand-light);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span style="color: var(--text-light);">Subtotal</span>
              <span><?php echo formatPrice($order['subtotal']); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span style="color: var(--text-light);">Shipping</span>
              <span><?php echo $order['shipping_cost'] > 0 ? formatPrice($order['shipping_cost']) : 'FREE'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span style="color: var(--text-light);">GST</span>
              <span><?php echo formatPrice($order['tax_amount']); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--sand-light);">
              <span style="font-weight: 700; font-size: 1.1rem;">Total</span>
              <span style="font-family: 'Playfair Display', serif; font-weight: 900; font-size: 1.3rem; color: var(--forest);"><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
          </div>
        </div>
        
        <div style="background: var(--cream); padding: 20px; border-radius: 12px; text-align: left; margin-bottom: 32px;">
          <h4 style="font-size: 1rem; color: var(--bark); margin-bottom: 12px;">&#128230; Shipping Address</h4>
          <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
            <?php echo e($order['customer_name']); ?><br>
            <?php echo e($order['shipping_address']); ?><br>
            <?php echo e($order['city']); ?>, <?php echo e($order['state']); ?> - <?php echo e($order['pincode']); ?><br>
            <?php echo e($order['country']); ?><br>
            Phone: <?php echo e($order['customer_phone']); ?>
          </p>
        </div>
        
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
          <a href="products.php" class="btn-primary">Continue Shopping</a>
          <a href="index.php" class="btn-secondary">Back to Home</a>
        </div>
        
      </div>
      
      <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-sm); text-align: left;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--bark); margin-bottom: 16px;">What's Next?</h3>
        <div style="display: grid; gap: 16px;">
          <div style="display: flex; gap: 16px; align-items: start;">
            <div style="width: 40px; height: 40px; background: var(--cream); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--terracotta); flex-shrink: 0;">1</div>
            <div>
              <div style="font-weight: 600; color: var(--bark); margin-bottom: 4px;">Order Confirmation Email</div>
              <div style="font-size: 0.9rem; color: var(--text-light);">You'll receive an email confirmation shortly with your order details.</div>
            </div>
          </div>
          <div style="display: flex; gap: 16px; align-items: start;">
            <div style="width: 40px; height: 40px; background: var(--cream); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--terracotta); flex-shrink: 0;">2</div>
            <div>
              <div style="font-weight: 600; color: var(--bark); margin-bottom: 4px;">Artisan Preparation</div>
              <div style="font-size: 0.9rem; color: var(--text-light);">Our artisans will carefully prepare your handcrafted products.</div>
            </div>
          </div>
          <div style="display: flex; gap: 16px; align-items: start;">
            <div style="width: 40px; height: 40px; background: var(--cream); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--terracotta); flex-shrink: 0;">3</div>
            <div>
              <div style="font-weight: 600; color: var(--bark); margin-bottom: 4px;">Shipping & Delivery</div>
              <div style="font-size: 0.9rem; color: var(--text-light);">Your order will be shipped and delivered within 5-7 business days.</div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
