<?php
/**
 * Artisan Dashboard - Submit Products
 */
$pageTitle = 'Artisan Dashboard';
require_once 'includes/header.php';

// Check if artisan is registered (by email)
$artisanEmail = $_SESSION['artisan_email'] ?? '';
$artisan = null;

if (empty($artisanEmail) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_email'])) {
    $artisanEmail = sanitize($_POST['email'] ?? '');
    if ($artisanEmail) {
        $_SESSION['artisan_email'] = $artisanEmail;
    }
}

if ($artisanEmail) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM artisans WHERE email = ?");
    $stmt->execute([$artisanEmail]);
    $artisan = $stmt->fetch();
}

// Handle product submission
$formSuccess = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_product']) && $artisan) {
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $tag = sanitize($_POST['tag'] ?? '');
    
    if (empty($name) || empty($category) || empty($description) || $price <= 0) {
        $formError = 'Please fill in all required fields.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO product_submissions (artisan_id, name, category, description, price, tag) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$artisan['id'], $name, $category, $description, $price, $tag]);
            $formSuccess = true;
        } catch (PDOException $e) {
            $formError = 'Failed to submit product. Please try again.';
        }
    }
}

// Get artisan's submitted products
$submittedProducts = [];
if ($artisan) {
    $stmt = $db->prepare("SELECT * FROM product_submissions WHERE artisan_id = ? ORDER BY created_at DESC");
    $stmt->execute([$artisan['id']]);
    $submittedProducts = $stmt->fetchAll();
}
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>Artisan Dashboard</span>
    </div>
    <h1>Artisan Dashboard</h1>
    <p>Submit and manage your products</p>
  </div>
</section>

<section class="section-pad">
  <div class="container">
    
    <?php if (!$artisan): ?>
      <!-- Artisan Verification -->
      <div style="max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: var(--shadow-md); text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 16px;">&#128100;</div>
        <h2 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 16px;">Verify Your Identity</h2>
        <p style="color: var(--text-light); margin-bottom: 24px;">Enter the email you used during registration to access your dashboard.</p>
        
        <form method="POST">
          <input type="hidden" name="verify_email" value="1">
          <div class="form-group">
            <input type="email" name="email" placeholder="Your registered email" required>
          </div>
          <button type="submit" class="btn-primary" style="width: 100%;">Verify & Continue &#8594;</button>
        </form>
        
        <p style="margin-top: 24px; font-size: 0.9rem; color: var(--text-light);">
          Not registered yet? <a href="artisan_register.php" style="color: var(--terracotta); font-weight: 600;">Register here</a>
        </p>
      </div>
    
    <?php else: ?>
      <!-- Artisan Dashboard -->
      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px; align-items: start;">
        
        <!-- Left: Artisan Info -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-sm);">
          <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; background: var(--sand); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 16px;">
              &#128104;&#8205;&#127806;
            </div>
            <h3 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 4px;"><?php echo e($artisan['name']); ?></h3>
            <p style="font-size: 0.9rem; color: var(--text-light);"><?php echo e($artisan['location']); ?></p>
            <span style="display: inline-block; background: rgba(45,80,22,0.08); color: var(--forest); padding: 4px 14px; border-radius: 50px; font-size: 0.82rem; font-weight: 500; margin-top: 8px;">
              <?php echo e($artisan['specialty']); ?>
            </span>
          </div>
          
          <div style="border-top: 1px solid var(--sand-light); padding-top: 20px;">
            <div style="margin-bottom: 12px;">
              <div style="font-size: 0.82rem; color: var(--text-light);">Phone</div>
              <div style="font-weight: 500;"><?php echo e($artisan['phone']); ?></div>
            </div>
            <div style="margin-bottom: 12px;">
              <div style="font-size: 0.82rem; color: var(--text-light);">Email</div>
              <div style="font-weight: 500;"><?php echo e($artisan['email']); ?></div>
            </div>
            <div>
              <div style="font-size: 0.82rem; color: var(--text-light);">Products Submitted</div>
              <div style="font-weight: 500;"><?php echo count($submittedProducts); ?></div>
            </div>
          </div>
        </div>
        
        <!-- Right: Product Submission Form & History -->
        <div>
          <!-- Submit New Product -->
          <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-sm); margin-bottom: 32px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--bark); margin-bottom: 24px;">Submit New Product</h3>
            
            <?php if ($formSuccess): ?>
              <div style="background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                &#10003; Product submitted successfully! It will be reviewed by our team before going live.
              </div>
            <?php endif; ?>
            
            <?php if ($formError): ?>
              <div style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                <?php echo e($formError); ?>
              </div>
            <?php endif; ?>
            
            <form method="POST">
              <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" required value="<?php echo e($_POST['name'] ?? ''); ?>">
              </div>
              
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                  <label>Category *</label>
                  <select name="category" required>
                    <option value="">Select category</option>
                    <option value="furniture" <?php echo ($_POST['category'] ?? '') === 'furniture' ? 'selected' : ''; ?>>Furniture</option>
                    <option value="baskets" <?php echo ($_POST['category'] ?? '') === 'baskets' ? 'selected' : ''; ?>>Baskets</option>
                    <option value="decor" <?php echo ($_POST['category'] ?? '') === 'decor' ? 'selected' : ''; ?>>Home Decor</option>
                    <option value="tools" <?php echo ($_POST['category'] ?? '') === 'tools' ? 'selected' : ''; ?>>Tools & Utility</option>
                    <option value="jewelry" <?php echo ($_POST['category'] ?? '') === 'jewelry' ? 'selected' : ''; ?>>Jewelry</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Price (INR) *</label>
                  <input type="number" name="price" step="0.01" min="0" required value="<?php echo e($_POST['price'] ?? ''); ?>">
                </div>
              </div>
              
              <div class="form-group">
                <label>Description *</label>
                <textarea name="description" required style="min-height: 100px;"><?php echo e($_POST['description'] ?? ''); ?></textarea>
              </div>
              
              <div class="form-group">
                <label>Tag (Optional)</label>
                <input type="text" name="tag" placeholder="e.g., Popular, New, Bestseller" value="<?php echo e($_POST['tag'] ?? ''); ?>">
              </div>
              
              <button type="submit" name="submit_product" class="btn-primary" style="width: 100%;">Submit Product for Review &#8594;</button>
            </form>
          </div>
          
          <!-- Submitted Products History -->
          <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--bark); margin-bottom: 24px;">Your Submitted Products</h3>
            
            <?php if (empty($submittedProducts)): ?>
              <p style="text-align: center; color: var(--text-light); padding: 40px 20px;">You haven't submitted any products yet. Use the form above to submit your first product!</p>
            <?php else: ?>
              <?php foreach ($submittedProducts as $product): ?>
              <div style="padding: 20px; border: 1px solid var(--sand-light); border-radius: 12px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                  <div>
                    <h4 style="font-size: 1.05rem; color: var(--bark); margin-bottom: 4px;"><?php echo e($product['name']); ?></h4>
                    <p style="font-size: 0.85rem; color: var(--text-light);"><?php echo e(ucfirst($product['category'])); ?> | <?php echo formatPrice($product['price']); ?></p>
                  </div>
                  <span style="padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; 
                    <?php if ($product['status'] === 'approved'): ?>
                      background: rgba(45,80,22,0.1); color: #2D5016;
                    <?php elseif ($product['status'] === 'rejected'): ?>
                      background: rgba(220,53,69,0.1); color: #dc3545;
                    <?php else: ?>
                      background: rgba(200,168,78,0.1); color: #C8A84E;
                    <?php endif; ?>">
                    <?php echo ucfirst($product['status']); ?>
                  </span>
                </div>
                <p style="font-size: 0.88rem; color: var(--text-light); line-height: 1.6;"><?php echo e($product['description']); ?></p>
                <?php if ($product['admin_notes']): ?>
                <div style="margin-top: 12px; padding: 12px; background: var(--cream); border-radius: 8px; font-size: 0.85rem;">
                  <strong>Admin Note:</strong> <?php echo e($product['admin_notes']); ?>
                </div>
                <?php endif; ?>
                <div style="margin-top: 12px; font-size: 0.82rem; color: var(--text-light);">
                  Submitted: <?php echo formatDate($product['created_at']); ?>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        
      </div>
    <?php endif; ?>
    
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
