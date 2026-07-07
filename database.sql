-- Tribal Crafts Database Schema
-- Run this SQL to create the database and tables

CREATE DATABASE IF NOT EXISTS tribal_crafts;
USE tribal_crafts;

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Artisans table
CREATE TABLE IF NOT EXISTS artisans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(200) NOT NULL,
    specialty VARCHAR(100) NOT NULL,
    bio TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    photo_url VARCHAR(255) DEFAULT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artisan_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    tag VARCHAR(50) DEFAULT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE
);

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    purpose VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
-- Password hash generated with: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwD.0rQjfOqGwqpJOd85sWHsUjEDkqxkRzu', 'admin@tribalcrafts.in');

-- Insert sample artisans
INSERT INTO artisans (name, location, specialty, bio, phone, email, is_featured) VALUES
('Ramu Munda', 'Ranchi, Jharkhand', 'Bamboo Furniture', 'Third-generation bamboo furniture maker. Specializes in chairs, tables, and decorative pieces using traditional Munda techniques passed down for over 80 years.', '+919876543210', 'ramu@example.com', TRUE),
('Sita Oraon', 'Bastar, Chhattisgarh', 'Woven Baskets', 'Expert in weaving intricate bamboo baskets and storage containers. Her geometric patterns are inspired by Oraon tribal motifs and forest flora.', '+919876543211', 'sita@example.com', TRUE),
('Birsa Ho', 'Mayurbhanj, Odisha', 'Bamboo Decor & Tools', 'Creates beautiful bamboo lamps, wall hangings, and traditional farming tools. Known for combining utility with artistic beauty in every piece.', '+919876543212', 'birsa@example.com', TRUE),
('Lakshmi Bhil', 'Banswara, Rajasthan', 'Bamboo Jewelry', 'Creates stunning bamboo earrings, necklaces, and bangles with intricate beadwork. Her designs blend traditional Bhil patterns with contemporary fashion.', '+919876543213', 'lakshmi@example.com', FALSE),
('Kanu Santhal', 'Dumka, Jharkhand', 'Bamboo Musical Instruments', 'Master craftsman of bamboo flutes, drums, and traditional tribal instruments. His flutes are known for their pure tone.', '+919876543214', 'kanu@example.com', FALSE),
('Mali Gond', 'Mandla, Madhya Pradesh', 'Bamboo Home Decor', 'Specializes in bamboo photo frames, mirror frames, and wall clocks. Her Gond art-inspired decorative pieces bring tribal heritage into modern homes.', '+919876543215', 'mali@example.com', FALSE);

-- Insert sample products
INSERT INTO products (artisan_id, name, category, description, price, tag, is_featured) VALUES
(1, 'Bamboo Rocking Chair', 'furniture', 'Hand-curved rocking chair with woven seat. Perfect for verandas and living rooms.', 3500.00, 'Popular', TRUE),
(2, 'Woven Storage Basket Set', 'baskets', 'Set of 3 nesting baskets with traditional Oraon geometric patterns. Great for home organization.', 1200.00, 'New', TRUE),
(3, 'Bamboo Pendant Lamp', 'decor', 'Intricately woven pendant lamp that casts beautiful shadow patterns. Fits standard bulb holders.', 1800.00, NULL, TRUE),
(1, 'Bamboo Stool (Pair)', 'furniture', 'Lightweight yet sturdy pair of bamboo stools. Natural finish with polished surface.', 2200.00, NULL, FALSE),
(3, 'Bamboo Garden Tools Set', 'tools', 'Set of 5 garden tools with bamboo handles. Rake, trowel, weeder, planter, and cultivator.', 950.00, NULL, FALSE),
(2, 'Market Tote Basket', 'baskets', 'Large woven tote with handles. Eco-friendly alternative for shopping. Holds up to 15kg.', 800.00, 'Handwoven', FALSE),
(3, 'Bamboo Wall Art Panel', 'decor', 'Decorative wall panel with tribal motif carving. A statement piece for any room.', 2500.00, NULL, TRUE),
(2, 'Bamboo Kitchen Organizer', 'tools', 'Multi-tier kitchen organizer for utensils and spices. Compact and elegant design.', 1400.00, NULL, FALSE),
(4, 'Bamboo Bead Earrings', 'jewelry', 'Lightweight bamboo earrings with traditional Bhil beadwork. Available in natural and dyed colors.', 350.00, 'Handmade', FALSE),
(6, 'Bamboo Photo Frame', 'decor', 'Handcrafted bamboo photo frame with Gond art-inspired border design. Fits 6x4 inch photos.', 600.00, NULL, FALSE),
(1, 'Bamboo Coffee Table', 'furniture', 'Elegant bamboo coffee table with glass top. Traditional joinery with modern aesthetics.', 4800.00, 'Bestseller', FALSE),
(4, 'Bamboo & Bead Necklace', 'jewelry', 'Statement necklace with bamboo segments and colorful tribal beads. Adjustable length.', 550.00, NULL, FALSE);
-- Additional tables for e-commerce and artisan management
-- Run this after the initial database setup

USE tribal_crafts;

-- Artisan registration requests table
CREATE TABLE IF NOT EXISTS artisan_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(200) NOT NULL,
    specialty VARCHAR(100) NOT NULL,
    bio TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    aadhar_number VARCHAR(20) DEFAULT NULL,
    experience_years INT DEFAULT 0,
    portfolio_url VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Artisan product submissions table
CREATE TABLE IF NOT EXISTS product_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artisan_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    tag VARCHAR(50) DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE
);

-- Shopping cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (session_id, product_id)
);

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (session_id, product_id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    session_id VARCHAR(100) NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    country VARCHAR(100) DEFAULT 'India',
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 0.00,
    tax_amount DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    order_status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    transaction_id VARCHAR(255) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    artisan_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Payment methods configuration
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_international BOOLEAN DEFAULT FALSE,
    config TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default payment methods
INSERT INTO payment_methods (name, code, is_active, is_international) VALUES
('Razorpay (UPI, Cards, Netbanking)', 'razorpay', TRUE, FALSE),
('PayPal (International)', 'paypal', TRUE, TRUE),
('Cash on Delivery', 'cod', TRUE, FALSE);
