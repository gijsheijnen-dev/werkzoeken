SET NAMES utf8mb4;

DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)   NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admins_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    ip_address   VARCHAR(45)   NOT NULL,
    attempted_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_login_attempts_ip_time (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (username, password_hash) VALUES
('guest', '$2y$10$1o9PiuYr6eb353tVI5qXFeESIcm2/8rTMEUU73.wtZaS6JV3OzYNq');
