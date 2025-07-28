-- StageManager Database Structure
-- Import this file into phpMyAdmin to create the database

CREATE DATABASE IF NOT EXISTS stagemanager_db;
USE stagemanager_db;

-- Users table for students, companies, and admin
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'company', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Offers table for internship opportunities
CREATE TABLE offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Applications table for student applications
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    offer_id INT NOT NULL,
    cv_path VARCHAR(255),
    applied_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (student_id, offer_id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Administrateur', 'admin@stagemanager.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample company for testing
INSERT INTO users (name, email, password, role) VALUES 
('TechCorp SARL', 'contact@techcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company');

-- Insert sample student for testing
INSERT INTO users (name, email, password, role) VALUES 
('Jean Dupont', 'jean.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

-- Insert sample offers
INSERT INTO offers (company_id, title, description, requirements) VALUES 
(2, 'Stage Développeur Web', 'Stage de 3 mois en développement web avec PHP et MySQL. Vous travaillerez sur des projets réels et apprendrez les meilleures pratiques du développement.', 'Connaissances en HTML, CSS, JavaScript. PHP souhaité mais pas obligatoire.'),
(2, 'Stage Marketing Digital', 'Stage de 6 mois en marketing digital. Vous participerez à la création de campagnes publicitaires et à l\'analyse des performances.', 'Études en marketing ou communication. Maîtrise des réseaux sociaux.');
