SET NAMES utf8mb4;

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
