use jobdating;

CREATE TABLE IF NOT EXISTS USERS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    promotion VARCHAR(50) DEFAULT NULL,
    speciality VARCHAR(100) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student'
);

CREATE TABLE IF NOT EXISTS companys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    sector VARCHAR(100),
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
);

CREATE TABLE IF NOT EXISTS annonces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    company INT NOT NULL,
    contract ENUM('CDI', 'CDD', 'Internship', 'Freelance') NOT NULL,
    location VARCHAR(100) NOT NULL,
    skills_required TEXT,
    posted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (company) REFERENCES companys(id)
    ON DELETE CASCADE CASCADE
);

CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    annonce_id INT NOT NULL,
    cover_letter TEXT,
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (annonce_id) REFERENCES annonces(id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

--@block
DROP TABLE IF EXISTS annonces;
DROP TABLE IF EXISTS companys;
DROP TABLE IF EXISTS users;


--@block
INSERT INTO users (email, user_name, password_hash, role) VALUES
('admin@jobdating.com', 'admin', '$2y$10$abcdefghijklmnopqrstuvwxy', 'admin'),
('john.doe@example.com', 'johndoe', '$2y$10$abcdefghijklmnopqrstuvwxy', 'student'),
('jane.smith@example.com', 'janesmith', '$2y$10$abcdefghijklmnopqrstuvwxy', 'student'),
('bob.wilson@example.com', 'bobwilson', '$2y$10$abcdefghijklmnopqrstuvwxy', 'student'),
('alice.brown@example.com', 'alicebrown', '$2y$10$abcdefghijklmnopqrstuvwxy', 'student');

INSERT INTO companys (name, sector, address, phone, email) VALUES
('Tech Solutions', 'Information Technology', '123 Tech St, Silicon Valley, CA', '123-456-7890', 'info@techsolutions.com'),
('HealthCare Inc.', 'Healthcare', '456 Health Ave, New York, NY', '987-654-3210', 'info@healthcareinc.com'),
('EduWorld', 'Education', '789 Edu Rd, Boston, MA', '555-123-4567', 'info@eduworld.com'),
('FinancePros', 'Finance', '321 Finance Blvd, Chicago, IL', '444-555-6666', 'info@financepros.com'),
('RetailMart', 'Retail', '654 Retail Ln, Los Angeles, CA', '333-222-1111', 'info@retailmart.com');

INSERT INTO annonces (title, description, company, contract, location, skills_required, expires_at) VALUES
('Software Engineer', 'Develop and maintain web applications.', 1, 'CDI', 'Silicon Valley, CA', 'PHP, JavaScript, SQL', '2024-12-31 23:59:59'),
('Nurse Practitioner', 'Provide healthcare services to patients.', 2, 'CDD', 'New York, NY', 'Nursing License, Patient Care', '2024-11-30 23:59:59'),
('Math Teacher', 'Teach mathematics to high school students.', 3, 'Internship', 'Boston, MA', 'Teaching Certification, Math Skills', '2024-10-31 23:59:59'),
('Financial Analyst', 'Analyze financial data and trends.', 4, 'Freelance', 'Chicago, IL', 'Finance Degree, Analytical Skills', '2024-09-30 23:59:59'),
('Store Manager', 'Oversee daily operations of the retail store.', 5, 'CDI', 'Los Angeles, CA', 'Management Experience, Customer Service', '2024-12-15 23:59:59');
