CREATE DATABASE IF NOT EXISTS anagrammes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE anagrammes;

CREATE TABLE utilisateurs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(30) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE verifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT UNSIGNED NULL,
    chaine1 VARCHAR(255) NOT NULL,
    chaine2 VARCHAR(255) NOT NULL,
    resultat BOOLEAN NOT NULL,
    date_verification TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_verifications_utilisateur FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id) ON DELETE CASCADE
);
