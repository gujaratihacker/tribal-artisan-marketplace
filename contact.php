<?php
/**
 * Contact Page with Form Handling
 */
$pageTitle = 'Contact Us';
require_once 'includes/header.php';

$formSuccess = false;
$formError = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $purpose = sanitize($_POST['purpose'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validate
    if (empty($name) || empty($email) || empty($purpose) || empty($message)) {
        $formError = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } else {
        // Save to database
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, purpose, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $purpose, $message]);
            
            // Send email notification (optional)
            $to = ADMIN_EMAIL;
            $subject = "New Contact Message from $name";
            $body = "Name: $name\nEmail: $email\nPhone: $phone\nPurpose: $purpose\n\nMessage:\n$message";
            $headers = "From: $email\r\nReply-To: $email\r\n";
            
            @mail($to, $subject, $body, $headers);
            
            $formSuccess = true;
        } catch (PDOException $e) {
            $formError = 'Failed to save message. Please try again.';
        }
    }
}
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>Contact</span>
    </div>
    <h1>Get in Touch</h1>
    <p>Have a question, want to connect with an artisan, or register as a seller? We'd love to hear from you.</p>
  </div>
</section>

<!-- CONTACT SECTION -->
<section class="section-pad">
  <div class="container">
    <div class="contact-grid">

      <!-- CONTACT INFO -->
      <div class="contact-info animate-in">
        <h3>Let's Connect</h3>
        <p>Whether you're a buyer looking for a specific craft, an artisan wanting to join our platform, or someone with a question — reach out and we'll get back to you.</p>
        
        <div class="contact-methods">
          <div class="contact-method">
            <div class="contact-method-icon">&#128231;</div>
            <div>
              <h4>Email Us</h4>
              <p><?php echo ADMIN_EMAIL; ?></p>
              <p>We reply within 24 hours</p>
            </div>
          </div>
          <div class="contact-method">
            <div class="contact-method-icon">&#128222;</div>
            <div>
              <h4>Call Us</h4>
              <p>+91 123 456 7890</p>
              <p>Mon-Sat, 9am - 6pm IST</p>
            </div>
          </div>
          <div class="contact-method">
            <div class="contact-method-icon">&#128205;</div>
            <div>
              <h4>Visit Us</h4>
              <p>Tribal Crafts Office</p>
              <p>Main Road, Ranchi, Jharkhand 834001</p>
            </div>
          </div>
          <div class="contact-method">
            <div class="contact-method-icon">&#128172;</div>
            <div>
              <h4>WhatsApp</h4>
              <p>+91 123 456 7890</p>
              <p>Quick responses for urgent queries</p>
            </div>
          </div>
        </div>
      </div>

      <!-- CONTACT FORM -->
      <div class="contact-form animate-in">
        <?php if ($formSuccess): ?>
          <div style="text-align: center; padding: 40px;">
            <div style="font-size: 3rem; margin-bottom: 16px; color: var(--forest);">&#10003;</div>
            <h3 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 8px;">Message Sent!</h3>
            <p style="color: var(--text-light);">Thank you for reaching out. We'll get back to you soon.</p>
          </div>
        <?php else: ?>
          <h3>Send a Message</h3>
          <?php if ($formError): ?>
            <div style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
              <?php echo e($formError); ?>
            </div>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="form-group">
              <label for="name">Your Name *</label>
              <input type="text" id="name" name="name" placeholder="Enter your full name" required value="<?php echo e($_POST['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" placeholder="your@email.com" required value="<?php echo e($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" placeholder="+91 XXXXX XXXXX" value="<?php echo e($_POST['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
              <label for="purpose">Purpose *</label>
              <select id="purpose" name="purpose" required>
                <option value="">Select a reason</option>
                <option value="buyer" <?php echo ($_POST['purpose'] ?? '') === 'buyer' ? 'selected' : ''; ?>>I want to buy products</option>
                <option value="artisan" <?php echo ($_POST['purpose'] ?? '') === 'artisan' ? 'selected' : ''; ?>>I want to register as an artisan</option>
                <option value="question" <?php echo ($_POST['purpose'] ?? '') === 'question' ? 'selected' : ''; ?>>I have a general question</option>
                <option value="partnership" <?php echo ($_POST['purpose'] ?? '') === 'partnership' ? 'selected' : ''; ?>>Partnership / Collaboration</option>
                <option value="other" <?php echo ($_POST['purpose'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="message">Your Message *</label>
              <textarea id="message" name="message" placeholder="Tell us how we can help you..." required><?php echo e($_POST['message'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="form-submit">Send Message &#8594;</button>
          </form>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<!-- FAQ SECTION -->
<section class="section-pad" style="background: var(--cream);">
  <div class="container">
    <div class="section-header animate-in">
      <div class="overline">Common Questions</div>
      <h2>Frequently Asked Questions</h2>
    </div>
    <div style="max-width: 700px; margin: 0 auto;">
      <div class="animate-in" style="background: var(--warm-white); padding: 24px 28px; border-radius: var(--radius); margin-bottom: 16px; border-left: 3px solid var(--terracotta);">
        <h4 style="font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 600; color: var(--bark); margin-bottom: 8px;">How do I contact an artisan directly?</h4>
        <p style="font-size: 0.92rem; color: var(--text-light); line-height: 1.7;">Visit our <a href="artisans.php" style="color: var(--terracotta); font-weight: 500;">Artisans page</a>, find the artisan you'd like to connect with, and use the Call or Message buttons on their profile.</p>
      </div>
      <div class="animate-in" style="background: var(--warm-white); padding: 24px 28px; border-radius: var(--radius); margin-bottom: 16px; border-left: 3px solid var(--terracotta);">
        <h4 style="font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 600; color: var(--bark); margin-bottom: 8px;">How can I register as an artisan?</h4>
        <p style="font-size: 0.92rem; color: var(--text-light); line-height: 1.7;">Fill out the contact form and select "I want to register as an artisan." Our team will reach out to help you set up your profile and list your products.</p>
      </div>
      <div class="animate-in" style="background: var(--warm-white); padding: 24px 28px; border-radius: var(--radius); margin-bottom: 16px; border-left: 3px solid var(--terracotta);">
        <h4 style="font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 600; color: var(--bark); margin-bottom: 8px;">Is there any fee to join the platform?</h4>
        <p style="font-size: 0.92rem; color: var(--text-light); line-height: 1.7;">No. Tribal Crafts is free for both artisans and buyers. We believe in removing barriers, not adding them.</p>
      </div>
      <div class="animate-in" style="background: var(--warm-white); padding: 24px 28px; border-radius: var(--radius); margin-bottom: 16px; border-left: 3px solid var(--terracotta);">
        <h4 style="font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 600; color: var(--bark); margin-bottom: 8px;">Do you handle shipping and delivery?</h4>
        <p style="font-size: 0.92rem; color: var(--text-light); line-height: 1.7;">Shipping is arranged directly between the buyer and artisan. We can help coordinate logistics for larger orders.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
