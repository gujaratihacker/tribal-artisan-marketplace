<?php
/**
 * Header Include File
 * Include this at the top of every page
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/db_connect.php';

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Get cart and wishlist counts
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$sessionId = $_SESSION['session_id'];

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    $cartCount = $stmt->fetch()['count'] ?? 0;
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    $wishlistCount = $stmt->fetch()['count'] ?? 0;
} catch (Exception $e) {
    $cartCount = 0;
    $wishlistCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? e($pageTitle) . ' — ' . SITE_NAME : SITE_NAME . ' — Handcrafted Bamboo from Village Artisans'; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=DM+Sans:wght@300;400;500;600&family=Caveat:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- NAV -->
<nav id="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <span class="logo-icon">&#9752;</span>
      <?php echo SITE_NAME; ?>
    </a>
    <div class="nav-links">
      <a href="index.php" class="<?php echo $currentPage === 'index' ? 'active-link' : ''; ?>">Home</a>
      <a href="artisans.php" class="<?php echo $currentPage === 'artisans' ? 'active-link' : ''; ?>">Artisans</a>
      <a href="products.php" class="<?php echo $currentPage === 'products' ? 'active-link' : ''; ?>">Products</a>
      <a href="about.php" class="<?php echo $currentPage === 'about' ? 'active-link' : ''; ?>">About</a>
      <a href="contact.php" class="<?php echo $currentPage === 'contact' ? 'active-link' : ''; ?>">Contact</a>
      
      <a href="wishlist.php" style="position: relative; font-size: 1.2rem;" title="Wishlist">
        &#10084;
        <?php if ($wishlistCount > 0): ?>
        <span style="position: absolute; top: -8px; right: -8px; background: var(--terracotta); color: white; font-size: 0.7rem; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;"><?php echo $wishlistCount; ?></span>
        <?php endif; ?>
      </a>
      
      <a href="cart.php" style="position: relative; font-size: 1.2rem;" title="Shopping Cart">
        &#128722;
        <?php if ($cartCount > 0): ?>
        <span style="position: absolute; top: -8px; right: -8px; background: var(--terracotta); color: white; font-size: 0.7rem; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;"><?php echo $cartCount; ?></span>
        <?php endif; ?>
      </a>
      
      <a href="artisan_register.php" class="cta-btn">Register as Artisan</a>
    </div>
    <button class="mobile-toggle" onclick="toggleMobileMenu()">&#9776;</button>
  </div>
</nav>
