-- 1. إنشاء قاعدة البيانات
CREATE DATABASE user_data;

-- 2. استخدام قاعدة البيانات
USE user_data;

-- 3. إنشاء الطاولة users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    birthdate DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
