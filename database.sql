CREATE DATABASE IF NOT EXISTS user_management;

USE user_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO users (name, email, phone) VALUES
('John Doe', 'john@example.com', '555-1234'),
('Jane Smith', 'jane@example.com', '555-5678'),
('Bob Johnson', 'bob@example.com', '555-9012');

-- Drop procedures if they exist to avoid errors when running the script multiple times
DROP PROCEDURE IF EXISTS sp_get_all_users;
DROP PROCEDURE IF EXISTS sp_get_user_by_id;
DROP PROCEDURE IF EXISTS sp_add_user;
DROP PROCEDURE IF EXISTS sp_update_user;
DROP PROCEDURE IF EXISTS sp_delete_user;

DELIMITER //

-- Procedure to get all users
CREATE PROCEDURE sp_get_all_users()
BEGIN
    SELECT * FROM users ORDER BY id DESC;
END //

-- Procedure to get a specific user by ID
CREATE PROCEDURE sp_get_user_by_id(IN user_id INT)
BEGIN
    SELECT * FROM users WHERE id = user_id;
END //

-- Procedure to add a new user
CREATE PROCEDURE sp_add_user(
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20)
)
BEGIN
    DECLARE email_exists INT;
    
    -- Check if email already exists
    SELECT COUNT(*) INTO email_exists FROM users WHERE email = p_email;
    
    IF email_exists = 0 THEN
        INSERT INTO users (name, email, phone) VALUES (p_name, p_email, p_phone);
        SELECT LAST_INSERT_ID() AS id;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
    END IF;
END //

-- Procedure to update an existing user
CREATE PROCEDURE sp_update_user(
    IN p_id INT,
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20)
)
BEGIN
    DECLARE email_exists INT;
    
    -- Check if email already exists for another user
    SELECT COUNT(*) INTO email_exists FROM users WHERE email = p_email AND id != p_id;
    
    IF email_exists = 0 THEN
        UPDATE users SET name = p_name, email = p_email, phone = p_phone WHERE id = p_id;
        SELECT ROW_COUNT() AS affected_rows;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists for another user';
    END IF;
END //

-- Procedure to delete a user
CREATE PROCEDURE sp_delete_user(IN p_id INT)
BEGIN
    DELETE FROM users WHERE id = p_id;
    SELECT ROW_COUNT() AS affected_rows;
END //

DELIMITER ;