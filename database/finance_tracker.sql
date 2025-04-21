-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS finance_tracker;
USE finance_tracker;

-- Drop existing tables in reverse order of dependencies
DROP TABLE IF EXISTS budget_goals;
DROP TABLE IF EXISTS debts;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    opening_balance DECIMAL(10,2) DEFAULT 0.00,
    balance_set BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create transactions table
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create budget_goals table
CREATE TABLE budget_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    period_type ENUM('monthly', 'weekly', 'custom') NOT NULL DEFAULT 'monthly',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create debts table
CREATE TABLE debts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('owe', 'owed') NOT NULL,
    person_name VARCHAR(100) NOT NULL,
    date_borrowed DATE NOT NULL,
    due_date DATE,
    status ENUM('active', 'paid') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert admin user
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@example.com', '$2y$10$8FPV6.gX0yQd0dHhpTx1.OYIxWv7zW.PF8CGgvF.tHqBE6TqNgoku', 'admin');

-- Insert test user
INSERT INTO users (username, email, password, role) VALUES 
('test_user', 'test@example.com', '$2y$10$8FPV6.gX0yQd0dHhpTx1.OYIxWv7zW.PF8CGgvF.tHqBE6TqNgoku', 'user');

-- Insert some sample categories for the test user
INSERT INTO transactions (user_id, type, category, amount, description, date) VALUES
(2, 'income', 'salary', 50000.00, 'Monthly salary', CURDATE()),
(2, 'expense', 'groceries', 2000.00, 'Weekly groceries', CURDATE()),
(2, 'expense', 'utilities', 1500.00, 'Electricity bill', CURDATE()),
(2, 'expense', 'rent', 15000.00, 'Monthly rent', CURDATE()),
(2, 'income', 'freelance', 10000.00, 'Web development project', CURDATE());

-- Insert sample budget goals for the test user
INSERT INTO budget_goals (user_id, category, amount, start_date, end_date, period_type) VALUES
(2, 'groceries', 8000.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'monthly'),
(2, 'utilities', 5000.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'monthly'),
(2, 'rent', 15000.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'monthly');

-- Insert sample debts for the test user
INSERT INTO debts (user_id, description, amount, type, person_name, date_borrowed, due_date) VALUES
(2, 'Borrowed for laptop', 1500.00, 'owe', 'John Smith', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 MONTH)),
(2, 'Lent for car repair', 800.00, 'owed', 'Jane Doe', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH));
