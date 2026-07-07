<?php
/**
 * Artisan Registration Page
 */
$pageTitle = 'Register as Artisan';
require_once 'includes/header.php';

$formSuccess = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $specialty = sanitize($_POST['specialty'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $aadhar = sanitize($_POST['aadhar'] ?? '');
    $experience = (int)($_POST['experience'] ?? 0);
    $portfolio = sanitize($_POST['portfolio'] ?? '');
    
    if (empty($name) || empty($location) || empty($specialty) || empty($bio) || empty($phone) || empty($email)) {
        $formError = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO artisan_requests (name, location, specialty, bio, phone, email, aadhar_number, experience_years, portfolio_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $location, $specialty, $bio, $phone, $email, $aadhar, $experience, $portfolio]);
            $formSuccess = true;
        } catch (PDOException $e) {
            $formError = 'Failed to submit registration. Please try again.';
        }
    }
}
?>

<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>Artisan Registration</span>
    </div>
    <h1>Register as an Artisan</h1>
    <p>Join our platform and showcase your handmade crafts to buyers across India</p>
  </div>
</section>

<section class="section-pad">
  <div class="container">
    <div style="max-width: 800px; margin: 0 auto;">
      
      <?php if ($formSuccess): ?>
        <div style="background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 32px; border-radius: 16px; text-align: center;">
          <div style="font-size: 3rem; margin-bottom: 16px;">&#10003;</div>
          <h2 style="font-family: 'Playfair Display', serif; margin-bottom: 12px;">Registration Submitted!</h2>
          <p style="line-height: 1.7;">Thank you for registering. Our team will review your application and contact you within 3-5 business days. Once approved, you'll be able to add your products to the marketplace.</p>
          <a href="index.php" class="btn-primary" style="display: inline-block; margin-top: 24px;">Return to Home</a>
        </div>
      <?php else: ?>
        
        <?php if ($formError): ?>
          <div style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
            <?php echo e($formError); ?>
          </div>
        <?php endif; ?>
        
        <div style="background: var(--cream); padding: 24px; border-radius: 12px; margin-bottom: 32px;">
          <h3 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 12px;">&#9432; Before You Register</h3>
          <ul style="color: var(--text-light); line-height: 1.8; padding-left: 20px;">
            <li>You must be a tribal artisan creating handmade products</li>
            <li>Products should be made from bamboo or other natural materials</li>
            <li>You'll need to provide contact details for buyers to reach you</li>
            <li>Our team will verify your application before approval</li>
            <li>Once approved, you can add unlimited products to sell</li>
          </ul>
        </div>
        
        <form method="POST" action="" style="background: white; padding: 40px; border-radius: 16px; box-shadow: var(--shadow-md);">
          <h3 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 24px;">Personal Information</h3>
          
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" required value="<?php echo e($_POST['name'] ?? ''); ?>">
          </div>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
              <label>Phone Number *</label>
              <input type="tel" name="phone" required placeholder="+91 XXXXX XXXXX" value="<?php echo e($_POST['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
              <label>Email Address *</label>
              <input type="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">
            </div>
          </div>
          
          <div class="form-group">
            <label>Village/Town & State *</label>
            <input type="text" name="location" required placeholder="e.g., Ranchi, Jharkhand" value="<?php echo e($_POST['location'] ?? ''); ?>">
          </div>
          
          <div class="form-group">
            <label>Aadhar Number (Optional)</label>
            <input type="text" name="aadhar" placeholder="XXXX XXXX XXXX" value="<?php echo e($_POST['aadhar'] ?? ''); ?>">
            <small style="color: var(--text-light); font-size: 0.82rem;">For verification purposes only</small>
          </div>
          
          <h3 style="font-family: 'Playfair Display', serif; color: var(--bark); margin: 32px 0 24px;">Craft Information</h3>
          
          <div class="form-group">
            <label>Your Specialty *</label>
            <select name="specialty" required>
              <option value="">Select your craft specialty</option>
              <option value="Bamboo Furniture" <?php echo ($_POST['specialty'] ?? '') === 'Bamboo Furniture' ? 'selected' : ''; ?>>Bamboo Furniture</option>
              <option value="Woven Baskets" <?php echo ($_POST['specialty'] ?? '') === 'Woven Baskets' ? 'selected' : ''; ?>>Woven Baskets</option>
              <option value="Bamboo Decor & Tools" <?php echo ($_POST['specialty'] ?? '') === 'Bamboo Decor & Tools' ? 'selected' : ''; ?>>Bamboo Decor & Tools</option>
              <option value="Bamboo Jewelry" <?php echo ($_POST['specialty'] ?? '') === 'Bamboo Jewelry' ? 'selected' : ''; ?>>Bamboo Jewelry</option>
              <option value="Bamboo Musical Instruments" <?php echo ($_POST['specialty'] ?? '') === 'Bamboo Musical Instruments' ? 'selected' : ''; ?>>Bamboo Musical Instruments</option>
              <option value="Bamboo Home Decor" <?php echo ($_POST['specialty'] ?? '') === 'Bamboo Home Decor' ? 'selected' : ''; ?>>Bamboo Home Decor</option>
              <option value="Other" <?php echo ($_POST['specialty'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Years of Experience *</label>
            <input type="number" name="experience" min="0" max="100" required value="<?php echo e($_POST['experience'] ?? ''); ?>">
          </div>
          
          <div class="form-group">
            <label>Tell us about your craft *</label>
            <textarea name="bio" required placeholder="Describe your craft, techniques, and what makes your products unique..." style="min-height: 120px;"><?php echo e($_POST['bio'] ?? ''); ?></textarea>
          </div>
          
          <div class="form-group">
            <label>Portfolio/Website (Optional)</label>
            <input type="url" name="portfolio" placeholder="https://..." value="<?php echo e($_POST['portfolio'] ?? ''); ?>">
            <small style="color: var(--text-light); font-size: 0.82rem;">Link to photos of your work, social media, or website</small>
          </div>
          
          <button type="submit" class="form-submit" style="margin-top: 16px;">Submit Registration &#8594;</button>
        </form>
      
      <?php endif; ?>
    
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
