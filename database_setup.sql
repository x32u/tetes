-- Coffee Shop Database Setup
-- Run this in MySQL Workbench

-- Create database
CREATE DATABASE IF NOT EXISTS coffee;
USE coffee;

-- Users table with profile picture
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    profile_picture VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table with image storage (for coffee items)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    subcategory VARCHAR(50),
    image VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table to connect users and products
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample coffee items
INSERT INTO products (name, description, price, category, subcategory, stock_quantity, status) VALUES
('Espresso', 'Rich and bold espresso shot made from premium coffee beans.', 2.50, 'Espresso Drinks', 'Single Shot', 100, 'active'),
('Cappuccino', 'Classic cappuccino with steamed milk foam and a sprinkle of cocoa powder.', 4.25, 'Hot Coffee', 'Milk-based', 75, 'active'),
('Chocolate Croissant', 'Buttery, flaky croissant filled with rich dark chocolate.', 3.50, 'Pastries', 'Sweet', 30, 'active');

-- Insert sample customers
INSERT INTO users (fullname, email, phone, status) VALUES
('John Smith', 'john.smith@email.com', '+1234567890', 'active'),
('Sarah Johnson', 'sarah.j@email.com', '+1234567891', 'active'),
('Mike Brown', 'mike.brown@email.com', '+1234567892', 'active');

-- Create uploads directories (you'll need to create these manually)
-- uploads/
-- uploads/products/
-- uploads/profiles/
