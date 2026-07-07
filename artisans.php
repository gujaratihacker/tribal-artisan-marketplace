<?php
/**
 * Artisans Page
 */
$pageTitle = 'Our Artisans';
require_once 'includes/header.php';

$artisans = getAllArtisans();
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>Artisans</span>
    </div>
    <h1>Meet Our Artisans</h1>
    <p>Skilled craftspeople from tribal communities across India, preserving ancient traditions through their beautiful handmade work</p>
  </div>
</section>

<!-- ALL ARTISANS -->
<section id="artisans" class="section-pad">
  <div class="container">
    <div class="artisans-grid">
      <?php foreach ($artisans as $artisan): ?>
      <div class="artisan-card animate-in">
        <div class="artisan-photo" style="background: linear-gradient(135deg, #D4A574, #C4956A);">&#128104;&#8205;&#127806;</div>
        <div class="artisan-info">
          <h3><?php echo e($artisan['name']); ?></h3>
          <div class="artisan-location">&#128205; <?php echo e($artisan['location']); ?></div>
          <span class="artisan-specialty"><?php echo e($artisan['specialty']); ?></span>
          <p class="artisan-bio"><?php echo e($artisan['bio']); ?></p>
          <div class="artisan-contact">
            <a href="tel:<?php echo e($artisan['phone']); ?>" class="call-btn">&#128222; Call</a>
            <a href="mailto:<?php echo e($artisan['email']); ?>" class="msg-btn">&#9993; Message</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA SECTION -->
<section class="section-pad" style="background: var(--cream); text-align: center;">
  <div class="container">
    <h2 style="font-size: clamp(1.8rem, 3.5vw, 2.4rem); color: var(--bark); margin-bottom: 16px;">Are you a tribal artisan?</h2>
    <p style="font-size: 1.05rem; color: var(--text-light); max-width: 500px; margin: 0 auto 32px;">Join our platform and showcase your crafts to buyers across India. We help you reach new markets and earn fair income.</p>
    <a href="contact.php" class="btn-primary">Register as Artisan &#8594;</a>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
