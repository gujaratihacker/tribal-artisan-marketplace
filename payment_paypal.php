<?php
/**
 * PayPal Payment Processing
 */
$pageTitle = 'Payment - PayPal';
require_once 'includes/header.php';

$orderNumber = $_GET['order'] ?? '';
if (empty($orderNumber)) {
    header("Location: cart.php");
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: cart.php");
    exit;
}

// Convert INR to USD (approximate rate)
$amountUSD = round($order['total_amount'] / 83, 2);

// PayPal configuration
// NOTE: Replace with your actual PayPal credentials
$paypalClientId = 'YOUR_PAYPAL_CLIENT_ID';
$paypalSecret = 'YOUR_PAYPAL_SECRET';
$paypalMode = 'sandbox'; // 'sandbox' for testing, 'live' for production
?>

<section class="page-hero" style="padding: 140px 0 60px;">
  <div class="container">
    <h1>Complete Payment</h1>
    <p>Order #<?php echo e($orderNumber); ?></p>
  </div>
</section>

<section class="section-pad" style="padding-top: 40px;">
  <div class="container">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: var(--shadow-md); text-align: center;">
      
      <div style="margin-bottom: 32px;">
        <div style="font-size: 3rem; margin-bottom: 16px;">&#127760;</div>
        <h2 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 8px;">Pay with PayPal</h2>
        <p style="color: var(--text-light);">International payments accepted</p>
      </div>
      
      <div style="background: var(--cream); padding: 24px; border-radius: 12px; margin-bottom: 16px;">
        <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 8px;">Amount to Pay (INR)</div>
        <div style="font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 900; color: var(--forest);">
          <?php echo formatPrice($order['total_amount']); ?>
        </div>
      </div>
      
      <div style="background: rgba(0,112,186,0.08); padding: 16px; border-radius: 12px; margin-bottom: 32px;">
        <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 4px;">Approximate USD</div>
        <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: #0070ba;">
          $<?php echo number_format($amountUSD, 2); ?>
        </div>
      </div>
      
      <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 16px; border-radius: 8px; margin-bottom: 24px; text-align: left; font-size: 0.9rem;">
        <strong>&#9888; Demo Mode:</strong> This is a demo integration. To enable real PayPal payments:
        <ol style="margin-top: 8px; padding-left: 20px; line-height: 1.8;">
          <li>Create a PayPal Business account at <a href="https://www.paypal.com/business" target="_blank" style="color: var(--terracotta);">paypal.com</a></li>
          <li>Go to Developer Dashboard and create an app</li>
          <li>Get your Client ID and Secret</li>
          <li>Update <code>config/payment.php</code> with your credentials</li>
          <li>Integrate PayPal Checkout SDK</li>
        </ol>
      </div>
      
      <!-- PayPal Button (Demo) -->
      <div id="paypal-button-container" style="margin-bottom: 24px;"></div>
      
      <button class="btn-primary" style="width: 100%; padding: 16px; font-size: 1.1rem; background: #0070ba;">
        Pay $<?php echo number_format($amountUSD, 2); ?> with PayPal
      </button>
      
      <div style="margin-top: 24px;">
        <a href="order_success.php?order=<?php echo e($orderNumber); ?>&demo=1" style="color: var(--terracotta); font-size: 0.9rem;">
          Skip to Order Confirmation (Demo) &#8594;
        </a>
      </div>
      
      <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--sand-light); font-size: 0.82rem; color: var(--text-light);">
        &#128274; Secure payment powered by PayPal<br>
        Accepts Visa, MasterCard, Amex, and PayPal balance
      </div>
      
    </div>
  </div>
</section>

<!-- PayPal SDK (Demo) -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypalClientId; ?>&currency=USD"></script>
<script>
paypal.Buttons({
    style: {
        shape: 'pill',
        color: 'gold',
        layout: 'vertical',
        label: 'paypal',
    },
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?php echo $amountUSD; ?>'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Payment successful
            window.location.href = 'order_success.php?order=<?php echo e($orderNumber); ?>&tx=' + details.id;
        });
    }
}).render('#paypal-button-container');
</script>

<?php require_once 'includes/footer.php'; ?>
