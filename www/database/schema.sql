
-- Optimized Database schema for Reino de Habbo
-- MySQL version compatible with shared hosting environments

CREATE DATABASE IF NOT EXISTS reino_habbo 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE reino_habbo;

-- Optimized Users table with indexes
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    habbo_username VARCHAR(50) NOT NULL,
    role ENUM('usuario', 'operador', 'administrador', 'desarrollador') DEFAULT 'usuario',
    verification_code VARCHAR(20) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better performance
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_habbo_username (habbo_username),
    INDEX idx_verification_code (verification_code),
    INDEX idx_is_verified (is_verified),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at),
    INDEX idx_last_login (last_login)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci
COMMENT='Optimized users table with proper indexes';

-- Optimized Ranks table
CREATE TABLE ranks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    level INT NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#FFD700',
    benefits TEXT,
    requirements TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_name (name),
    UNIQUE KEY unique_level (level),
    INDEX idx_is_active (is_active),
    INDEX idx_level (level)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci
COMMENT='Ranks system with optimized structure';

-- User sessions table for better session management
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity),
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci
COMMENT='User sessions for better security and tracking';

-- Insert optimized sample data
INSERT INTO ranks (name, level, description, color, benefits, requirements, is_active) VALUES
('Usuario', 1, 'Miembro básico del reino', '#87CEEB', 'Acceso básico al reino', 'Registro en el reino', TRUE),
('Operador', 2, 'Moderador del reino con permisos especiales', '#32CD32', 'Moderar usuarios y contenido', 'Designación por administrador', TRUE),
('Administrador', 3, 'Administrador del reino con control avanzado', '#FF6347', 'Control completo del reino', 'Designación por desarrollador', TRUE),
('Desarrollador', 4, 'Desarrollador del sistema con acceso total', '#9932CC', 'Acceso total al sistema', 'Acceso de desarrollo', TRUE);

-- Create a view for user statistics (for faster queries)
CREATE VIEW user_stats AS
SELECT 
    r.name as rank_name,
    r.color as rank_color,
    COUNT(u.id) as user_count,
    COUNT(CASE WHEN u.is_verified = TRUE THEN 1 END) as verified_count,
    COUNT(CASE WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_count
FROM ranks r
LEFT JOIN users u ON u.role = LOWER(r.name)
WHERE r.is_active = TRUE
GROUP BY r.id, r.name, r.color
ORDER BY r.level;

-- Create additional indexes for better query performance
ALTER TABLE users ADD INDEX idx_username_verified (username, is_verified);
ALTER TABLE users ADD INDEX idx_habbo_username_verified (habbo_username, is_verified);

-- Add stored procedure for user cleanup
DELIMITER //
CREATE PROCEDURE CleanupUnverifiedUsers()
BEGIN
    DELETE FROM users 
    WHERE is_verified = FALSE 
    AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
END //
DELIMITER ;

-- Optional: Add some initial session configuration (safe for shared environments)
-- These are session-level settings that don't require GLOBAL privileges
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';
SET SESSION time_zone = '+00:00';
SET SESSION autocommit = 1;
