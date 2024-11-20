-- Création de la base de données
CREATE DATABASE IF NOT EXISTS crc;

-- Utilisation de la base de données
USE crc;


-- Création de la table clients
CREATE TABLE IF NOT EXISTS clients (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE clients ADD COLUMN nom VARCHAR(255) NOT NULL;


-- Création de la table consultants
CREATE TABLE IF NOT EXISTS consultants (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Création de la table techniciens
CREATE TABLE IF NOT EXISTS techniciens (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    competences TEXT,
    disponibilite BOOLEAN,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE techniciens ADD COLUMN nom VARCHAR(255) NOT NULL;

-- Création de la table tickets
CREATE TABLE IF NOT EXISTS tickets (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT,
    description TEXT,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20),
    priorite VARCHAR(10),
    categorie VARCHAR(50),
    historique TEXT,
    technician_id BIGINT,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (technician_id) REFERENCES techniciens(id)
);

-- Création de la table actions
CREATE TABLE IF NOT EXISTS actions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT,
    type VARCHAR(50),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    utilisateur_id BIGINT,
    utilisateur_type ENUM('client', 'consultant', 'technicien'),
    FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);

-- Création de la table messages
CREATE TABLE IF NOT EXISTS messages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT,
    contenu TEXT,
    dateEnvoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    envoyePar ENUM('consultant', 'technicien'),
    FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
INSERT INTO roles (name) VALUES ('client'), ('technicien'), ('consultant');

