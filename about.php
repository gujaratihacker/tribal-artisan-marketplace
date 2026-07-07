<?php
/**
 * About Page
 */
$pageTitle = 'About Us';
require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> <span>/</span> <span>About Us</span>
    </div>
    <h1>Our Story</h1>
    <p>Bridging the gap between tribal heritage and the modern world, one handcrafted piece at a time</p>
  </div>
</section>

<!-- FULL ABOUT -->
<section class="about-section section-pad">
  <div class="container">
    <div class="about-inner">
      <div class="about-text">
        <div class="overline">Who We Are</div>
        <h2>Preserving Heritage, Empowering Communities</h2>
        <p>Tribal Crafts was born from a simple belief: the incredible skills of tribal artisans deserve a platform beyond their villages. For generations, these craftspeople have created beautiful, functional products from bamboo and natural materials — yet they've had no way to reach the people who value them most.</p>
        <p>We bridge that gap. By connecting artisans directly with buyers, we ensure fair prices for makers and authentic products for lovers of handcrafted goods. No middlemen, no exploitation — just honest trade rooted in respect.</p>
        <p>Our team travels to remote villages across Jharkhand, Chhattisgarh, Odisha, Madhya Pradesh, and Rajasthan, documenting the work of skilled artisans and helping them present their crafts to a wider audience.</p>
        <div class="about-values">
          <div class="value-item">
            <div class="value-icon">&#127793;</div>
            <div>
              <h4>Sustainable Craft</h4>
              <p>Eco-friendly bamboo and natural materials sourced responsibly</p>
            </div>
          </div>
          <div class="value-item">
            <div class="value-icon">&#128170;</div>
            <div>
              <h4>Fair Livelihoods</h4>
              <p>Direct income to artisan families with no middlemen cuts</p>
            </div>
          </div>
          <div class="value-item">
            <div class="value-icon">&#127912;</div>
            <div>
              <h4>Living Traditions</h4>
              <p>Keeping ancestral skills alive for future generations</p>
            </div>
          </div>
          <div class="value-item">
            <div class="value-icon">&#129309;</div>
            <div>
              <h4>Direct Connection</h4>
              <p>Buyers and artisans connect directly, building real relationships</p>
            </div>
          </div>
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

<!-- MISSION & VISION -->
<section class="section-pad" style="background: var(--warm-white);">
  <div class="container">
    <div class="section-header animate-in">
      <div class="overline">What Drives Us</div>
      <h2>Our Mission & Vision</h2>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
      <div class="animate-in" style="background: var(--cream); padding: 40px; border-radius: var(--radius-lg); border-left: 4px solid var(--terracotta);">
        <h3 style="font-size: 1.4rem; color: var(--bark); margin-bottom: 16px;">Our Mission</h3>
        <p style="color: var(--text-light); line-height: 1.8;">To create a transparent, accessible marketplace where tribal artisans can showcase their handmade bamboo products and connect directly with buyers who appreciate authentic craftsmanship.</p>
      </div>
      <div class="animate-in" style="background: var(--cream); padding: 40px; border-radius: var(--radius-lg); border-left: 4px solid var(--forest);">
        <h3 style="font-size: 1.4rem; color: var(--bark); margin-bottom: 16px;">Our Vision</h3>
        <p style="color: var(--text-light); line-height: 1.8;">An India where every tribal artisan has the opportunity to thrive through their craft. Where ancient skills are preserved, communities are empowered, and handmade products are celebrated.</p>
      </div>
    </div>
  </div>
</section>

<!-- IMPACT NUMBERS -->
<section class="section-pad" style="background: var(--bark); color: var(--cream);">
  <div class="container">
    <div class="section-header animate-in">
      <div class="overline" style="color: var(--terracotta-light);">Our Impact</div>
      <h2 style="color: var(--cream);">Numbers That Matter</h2>
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; text-align: center;" class="animate-in">
      <div>
        <div style="font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 900; color: var(--terracotta-light);">120+</div>
        <p style="color: var(--sand); margin-top: 8px;">Artisans Registered</p>
      </div>
      <div>
        <div style="font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 900; color: var(--terracotta-light);">500+</div>
        <p style="color: var(--sand); margin-top: 8px;">Products Listed</p>
      </div>
      <div>
        <div style="font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 900; color: var(--terracotta-light);">35+</div>
        <p style="color: var(--sand); margin-top: 8px;">Villages Reached</p>
      </div>
      <div>
        <div style="font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 900; color: var(--terracotta-light);">8</div>
        <p style="color: var(--sand); margin-top: 8px;">States Covered</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section-pad" style="background: var(--cream); text-align: center;">
  <div class="container">
    <h2 style="font-size: clamp(1.8rem, 3.5vw, 2.4rem); color: var(--bark); margin-bottom: 16px;">Want to be part of this story?</h2>
    <p style="font-size: 1.05rem; color: var(--text-light); max-width: 500px; margin: 0 auto 32px;">Whether you're an artisan looking to showcase your work, or a buyer who loves handcrafted goods — there's a place for you here.</p>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="contact.php" class="btn-primary">Get in Touch &#8594;</a>
      <a href="products.php" class="btn-secondary">Browse Products</a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
