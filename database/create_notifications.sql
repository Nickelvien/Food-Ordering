-- Create notifications table for order notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_role` VARCHAR(20) NOT NULL,      -- 'admin' or 'user'
  `user_id` INT NULL,                    -- user id for user-specific notifications
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `related_id` INT NULL,                 -- e.g. order id
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_role (user_role),
  INDEX idx_user_id (user_id),
  INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
