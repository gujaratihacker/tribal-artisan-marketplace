<?php
/**
 * Home Page
 */
$pageTitle = 'Home — Handcrafted Bamboo from Village Artisans';
require_once 'includes/header.php';

// Get featured artisans and products
$featuredArtisans = getAllArtisans(true);
$featuredProducts = getAllProducts(null, true, 4);
?>

<!-- HERO -->
<section id="hero">
  <div class="container">
    <div class="hero-inner">
      <div class="hero-text">
        <div class="hero-badge">Empowering Tribal Artisans</div>
        <h1>Handcrafted with <span class="accent">heritage,</span> straight from the village</h1>
        <p>Discover authentic bamboo crafts made by skilled tribal artisans. Connect directly with the makers, support their livelihoods, and bring home pieces that carry generations of tradition.</p>
        <div class="hero-actions">
          <a href="products.php" class="btn-primary">Explore Crafts &#8594;</a>
          <a href="about.php" class="btn-secondary">Our Story</a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat">
            <h3>120+</h3>
            <p>Village Artisans</p>
          </div>
          <div class="hero-stat">
            <h3>500+</h3>
            <p>Unique Products</p>
          </div>
          <div class="hero-stat">
            <h3>35+</h3>
            <p>Villages Reached</p>
          </div>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-img-grid">
          <div class="hero-img-card" style="background: linear-gradient(135deg, #D4A574, #B8845A);">
            &#127805;
            <div class="card-overlay">Bamboo Baskets</div>
          </div>
          <div class="hero-img-card" style="background: linear-gradient(135deg, #8B9E6B, #6B8050);">
            &#127961;
            <div class="card-overlay">Wooden Decor</div>
          </div>
          <div class="hero-img-card" style="background: linear-gradient(135deg, #C4956A, #A67B52);">
            &#127793;
            <div class="card-overlay">Woven Crafts</div>
          </div>
          <div class="hero-img-card" style="background: linear-gradient(135deg, #9E8B6B, #7A6B50);">
            &#127922;
            <div class="card-overlay">Bamboo Furniture</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section id="how-it-works" class="section-pad">
  <div class="container">
    <div class="section-header animate-in">
      <div class="overline">Simple & Transparent</div>
      <h2>How It Works</h2>
      <p>Bridging the gap between village artisans and urban buyers in three simple steps</p>
    </div>
    <div class="steps-grid">
      <div class="step-card animate-in">
        <div class="step-icon">&#127912;</div>
        <div class="step-number">STEP 01</div>
        <h3>Artisans Showcase Crafts</h3>
        <p>Tribal artisans from villages list their handmade bamboo products with photos, descriptions, and their contact information on our platform.</p>
      </div>
      <div class="step-card animate-in">
        <div class="step-icon">&#128269;</div>
        <div class="step-number">STEP 02</div>
        <h3>Buyers Discover & Connect</h3>
        <p>Urban buyers browse the collection, find products they love, and reach out directly to the artisan through call or message — no middlemen.</p>
      </div>
      <div class="step-card animate-in">
        <div class="step-icon">&#129309;</div>
        <div class="step-number">STEP 03</div>
        <h3>Direct Trade, Fair Price</h3>
        <p>Buyer and artisan negotiate directly. The artisan earns fair income for their craft, and the buyer gets an authentic handmade product.</p>
      </div>
    </div>
  </div>
</section>

<!-- FEATURED ARTISANS -->
<section class="section-pad" style="background: var(--cream);">
  <div class="container">
    <div class="section-header animate-in">
      <div class="overline">Meet the Makers</div>
      <h2>Featured Artisans</h2>
      <p>Skilled craftspeople from tribal communities, preserving traditions through their art</p>
    </div>
    <div class="artisans-grid">
      <?php foreach ($featuredArtisans as $artisan): ?>
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
    <div style="text-align: center; margin-top: 48px;">
      <a href="artisans.php" class="btn-primary">View All Artisans &#8594;</a>
    </div>
  </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section-pad" style="background: var(--warm-white);">
  <div class="container">
    <div class="section-header animate-in">
      <div class="overline">Handmade with Love</div>
      <h2>Popular Products</h2>
      <p>Authentic bamboo products crafted by hand, each piece telling a story of tradition</p>
    </div>
    <div class="products-grid">
      <?php foreach ($featuredProducts as $product): ?>
      <div class="product-card animate-in">
        <div class="product-img" style="background: linear-gradient(135deg, #D4A574, #BFA06A);">
          &#129680;
          <?php if ($product['tag']): ?>
          <span class="product-tag"><?php echo e($product['tag']); ?></span>
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
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align: center; margin-top: 48px;">
      <a href="products.php" class="btn-primary">View All Products &#8594;</a>
    </div>
  </div>
</section>

<!-- ABOUT PREVIEW -->
<section id="about" class="section-pad">
  <div class="container">
    <div class="about-inner">
      <div class="about-text">
        <div class="overline">Our Story</div>
        <h2>Preserving Heritage, Empowering Communities</h2>
        <p>Tribal Crafts was born from a simple belief: the incredible skills of tribal artisans deserve a platform beyond their villages. For generations, these craftspeople have created beautiful, functional products from bamboo and natural materials.</p>
        <p>We bridge that gap. By connecting artisans directly with buyers, we ensure fair prices for makers and authentic products for lovers of handcrafted goods.</p>
        <div style="margin-top: 32px;">
          <a href="about.php" class="btn-primary">Read Our Full Story &#8594;</a>
        </div>
      </div>
      <div class="about-visual">
        <div class="about-img">&#127795;</div>
        <div class="about-img">&#127912;</div>
        <div class="about-img">&#127968;</div>
        <div class="about-img">&#128106;</div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
