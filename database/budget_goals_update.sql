-- Add sub-categories table
CREATE TABLE IF NOT EXISTS subcategories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_subcategory (user_id, category, subcategory)
);

-- Update budget_goals table
ALTER TABLE budget_goals 
ADD COLUMN subcategory VARCHAR(50) DEFAULT NULL,
ADD COLUMN year INT NOT NULL DEFAULT YEAR(CURRENT_DATE),
ADD COLUMN description TEXT,
ADD COLUMN alert_threshold INT DEFAULT 80,
ADD COLUMN is_recurring BOOLEAN DEFAULT FALSE,
ADD COLUMN recurring_type ENUM('monthly', 'quarterly', 'yearly') DEFAULT 'monthly',
ADD COLUMN last_notification_date DATE DEFAULT NULL;
