CREATE DATABASE shopping_system;

USE shopping_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255),
    fullname VARCHAR(100),   -- Kolom fullname
    email VARCHAR(100),
    birthdate DATE,
    gender ENUM('Pria', 'Perempuan'),
    address VARCHAR(255),
    city VARCHAR(50),
    phone_number VARCHAR(20),
    paypal_id VARCHAR(50),
    role ENUM('customer', 'admin') DEFAULT 'customer'
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    category VARCHAR(50),     -- Kolom category
    price DECIMAL(10,2),
    stock INT,
    image VARCHAR(255)        -- Kolom image untuk URL gambar produk
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2),
    payment_method ENUM('prepaid', 'postpaid'),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
);
