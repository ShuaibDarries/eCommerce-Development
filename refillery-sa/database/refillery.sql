-- The Refillery SA — database schema + seed data
-- Import via phpMyAdmin or: mysql -u root < database/refillery.sql

CREATE DATABASE IF NOT EXISTS refillery_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE refillery_db;

-- Customers (passwords below are hashes of 'Password123' for demo login purposes only)
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product catalogue
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(80) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders placed by customers
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('paid','pending','failed') DEFAULT 'pending',
    shipping_address VARCHAR(255) NOT NULL,
    payment_ref VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

-- Line items belonging to each order
CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Seed products (zero-waste catalogue)
INSERT INTO products (name, category, description, price, stock, image) VALUES
('Plastic-Free Kitchen Starter Kit', 'Starter Kits', 'Everything you need to kick the plastic habit: beeswax wraps, bamboo dish brush, loofah sponges and a cotton produce bag set.', 499.00, 25, 'kit-kitchen'),
('Refillable Dish Soap – 500ml', 'Cleaning Refills', 'Concentrated lemon-scented dish soap sold in a returnable glass bottle. Refill pouches available on subscription.', 89.00, 80, 'dish-soap'),
('Laundry Detergent Refill – 1L', 'Cleaning Refills', 'Biodegradable, low-suds laundry liquid safe for grey-water systems. One litre = 20 washes.', 129.00, 60, 'laundry'),
('Shampoo Bar – Rooibos & Shea', 'Personal Care', 'Solid shampoo bar, sulphate-free and plastic-free. Equivalent to two 250ml bottles.', 95.00, 100, 'shampoo-bar'),
('Conditioner Bar – Coconut', 'Personal Care', 'Nourishing solid conditioner bar for normal to dry hair. Lasts up to 60 washes.', 95.00, 90, 'conditioner-bar'),
('Bamboo Toothbrush (4-pack)', 'Personal Care', 'Compostable bamboo-handled toothbrushes with medium plant-based bristles.', 120.00, 150, 'toothbrush'),
('Reusable Cotton Produce Bags (5)', 'Kitchen', 'Organic cotton mesh bags in assorted sizes — replace single-use plastic produce bags.', 150.00, 70, 'produce-bags'),
('Stainless Steel Straw Set', 'Kitchen', 'Six reusable straws with cleaning brush and linen carry pouch.', 99.00, 110, 'straws'),
('Safety Razor – Chrome', 'Personal Care', 'Classic double-edge safety razor. Blades cost cents and last for years.', 349.00, 40, 'razor'),
('Monthly Refill Subscription Box', 'Subscriptions', 'Curated monthly box of home cleaning refills delivered to your door. Cancel anytime.', 299.00, 50, 'subscription');
