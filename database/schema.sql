SET NAMES utf8mb4;

DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS vacancies;
DROP TABLE IF EXISTS companies;

CREATE TABLE companies (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name       VARCHAR(150)  NOT NULL,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_companies_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vacancies (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    company_id    INT UNSIGNED  NOT NULL,
    title         VARCHAR(150)  NOT NULL,
    description   TEXT          NOT NULL,
    location      VARCHAR(100)  NOT NULL,
    tags          VARCHAR(255)  NOT NULL DEFAULT '',
    contact_name  VARCHAR(150)  NOT NULL,
    contact_email VARCHAR(255)  NOT NULL,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_vacancies_title (title),
    KEY idx_vacancies_location (location),
    KEY idx_vacancies_created_at (created_at),
    CONSTRAINT fk_vacancies_company
        FOREIGN KEY (company_id) REFERENCES companies (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE applications (
    id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    vacancy_id       INT UNSIGNED   NOT NULL,
    name             VARCHAR(100)   NOT NULL,
    email            VARCHAR(255)   NOT NULL,
    motivation       VARCHAR(1000)  NULL,
    cv_filename      VARCHAR(64)    NOT NULL,
    cv_original_name VARCHAR(255)   NOT NULL,
    created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_applications_cv_filename (cv_filename),
    CONSTRAINT fk_applications_vacancy
        FOREIGN KEY (vacancy_id) REFERENCES vacancies (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
