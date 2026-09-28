SET NAMES utf8mb4;

DROP TABLE IF EXISTS applications;

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
