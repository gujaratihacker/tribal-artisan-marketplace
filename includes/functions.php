<?php
/**
 * Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Check if user is logged in as admin
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require admin login
 */
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        redirect('login.php');
    }
}

/**
 * Format price in INR
 */
function formatPrice($price) {
    return '₹' . number_format($price, 0);
}

/**
 * Format date
 */
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get artisan by ID
 */
function getArtisanById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM artisans WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get all artisans
 */
function getAllArtisans($featured = false) {
    $db = getDB();
    $sql = "SELECT * FROM artisans";
    if ($featured) {
        $sql .= " WHERE is_featured = 1";
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get all products
 */
function getAllProducts($category = null, $featured = false, $limit = null) {
    $db = getDB();
    $sql = "SELECT p.*, a.name as artisan_name 
            FROM products p 
            LEFT JOIN artisans a ON p.artisan_id = a.id 
            WHERE p.is_available = 1";
    
    $params = [];
    
    if ($category && $category !== 'all') {
        $sql .= " AND p.category = ?";
        $params[] = $category;
    }
    
    if ($featured) {
        $sql .= " AND p.is_featured = 1";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get product by ID
 */
function getProductById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, a.name as artisan_name, a.id as artisan_id 
                          FROM products p 
                          LEFT JOIN artisans a ON p.artisan_id = a.id 
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get unread message count
 */
function getUnreadMessageCount() {
    $db = getDB();
    $stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
    return $stmt->fetchColumn();
}

/**
 * Get all categories
 */
function getCategories() {
    return [
        'furniture' => 'Furniture',
        'baskets' => 'Baskets',
        'decor' => 'Home Decor',
        'tools' => 'Tools & Utility',
        'jewelry' => 'Jewelry'
    ];
}

/**
 * Escape output for HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
