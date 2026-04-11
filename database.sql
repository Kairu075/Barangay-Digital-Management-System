-- ============================================================
-- BARANGAY SAN MARINO - DATABASE SCHEMA & MOCK DATA
-- ============================================================

CREATE DATABASE IF NOT EXISTS barangay_san_marino CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barangay_san_marino;

-- ============================================================
-- USERS TABLE
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin','secretary','treasurer','captain','resident') NOT NULL DEFAULT 'resident',
    email VARCHAR(100),
    phone VARCHAR(20),
    resident_id INT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- HOUSEHOLDS TABLE
-- ============================================================
CREATE TABLE households (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_no VARCHAR(20) UNIQUE NOT NULL,
    address TEXT NOT NULL,
    purok VARCHAR(50),
    household_head VARCHAR(100),
    contact_no VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- RESIDENTS TABLE
-- ============================================================
CREATE TABLE residents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id VARCHAR(20) UNIQUE NOT NULL,
    household_id INT,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    suffix VARCHAR(10),
    birthdate DATE NOT NULL,
    birthplace VARCHAR(100),
    gender ENUM('Male','Female') NOT NULL,
    civil_status ENUM('Single','Married','Widowed','Separated','Annulled') DEFAULT 'Single',
    nationality VARCHAR(50) DEFAULT 'Filipino',
    religion VARCHAR(50),
    occupation VARCHAR(100),
    monthly_income DECIMAL(10,2) DEFAULT 0,
    educational_attainment VARCHAR(100),
    voter_status TINYINT(1) DEFAULT 0,
    senior_citizen TINYINT(1) DEFAULT 0,
    pwd TINYINT(1) DEFAULT 0,
    solo_parent TINYINT(1) DEFAULT 0,
    email VARCHAR(100),
    contact_no VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (household_id) REFERENCES households(id)
);

-- ============================================================
-- DOCUMENT REQUESTS TABLE
-- ============================================================
CREATE TABLE document_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_no VARCHAR(20) UNIQUE NOT NULL,
    resident_id INT NOT NULL,
    document_type ENUM('Barangay Clearance','Certificate of Residency','Indigency Certificate','Business Clearance','Certificate of Good Moral Character') NOT NULL,
    purpose TEXT,
    status ENUM('Pending','Processing','For Approval','Approved','Released','Rejected') DEFAULT 'Pending',
    amount DECIMAL(10,2) DEFAULT 0,
    payment_status ENUM('Unpaid','Paid') DEFAULT 'Unpaid',
    or_number VARCHAR(50),
    requested_by INT,
    approved_by INT NULL,
    released_by INT NULL,
    notes TEXT,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at DATETIME NULL,
    released_at DATETIME NULL,
    FOREIGN KEY (resident_id) REFERENCES residents(id),
    FOREIGN KEY (requested_by) REFERENCES users(id)
);

-- ============================================================
-- COMPLAINTS TABLE
-- ============================================================
CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_no VARCHAR(20) UNIQUE NOT NULL,
    complainant_id INT NOT NULL,
    respondent_name VARCHAR(100),
    respondent_address TEXT,
    complaint_type ENUM('Noise Complaint','Property Dispute','Physical Assault','Verbal Abuse','Theft','Vandalism','Domestic Violence','Others') NOT NULL,
    description TEXT NOT NULL,
    incident_date DATE,
    incident_location TEXT,
    status ENUM('Pending','Under Investigation','Mediation','Resolved','Dismissed','Escalated') DEFAULT 'Pending',
    assigned_to INT NULL,
    admin_notes TEXT,
    attachment VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (complainant_id) REFERENCES residents(id)
);

-- ============================================================
-- ANNOUNCEMENTS TABLE
-- ============================================================
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('General','Health','Emergency','Events','Programs','Advisory') DEFAULT 'General',
    priority ENUM('Normal','Important','Urgent') DEFAULT 'Normal',
    start_date DATE,
    end_date DATE,
    is_published TINYINT(1) DEFAULT 1,
    views INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- ============================================================
-- TRANSACTIONS TABLE (Financial)
-- ============================================================
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    or_number VARCHAR(50) UNIQUE NOT NULL,
    document_request_id INT,
    resident_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Cash','GCash','Maya','Bank Transfer') DEFAULT 'Cash',
    description TEXT,
    collected_by INT,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_request_id) REFERENCES document_requests(id),
    FOREIGN KEY (resident_id) REFERENCES residents(id),
    FOREIGN KEY (collected_by) REFERENCES users(id)
);

-- ============================================================
-- DOCUMENT FEES TABLE
-- ============================================================
CREATE TABLE document_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_type VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(200)
);

-- ============================================================
-- MOCK DATA - USERS
-- ============================================================
INSERT INTO users (username, password, full_name, role, email, phone) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'admin@bsanmarino.gov.ph', '09171234567'),
('captain_reyes', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hon. Roberto Reyes', 'captain', 'captain@bsanmarino.gov.ph', '09181234567'),
('sec_santos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria Santos', 'secretary', 'secretary@bsanmarino.gov.ph', '09191234567'),
('treas_garcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Garcia', 'treasurer', 'treasurer@bsanmarino.gov.ph', '09201234567'),
('resident1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jose Cruz', 'resident', 'jose@email.com', '09221234567');
-- Default password for all: "password"

-- ============================================================
-- MOCK DATA - HOUSEHOLDS
-- ============================================================
INSERT INTO households (household_no, address, purok, household_head, contact_no) VALUES
('HH-001', '123 Rizal Street', 'Purok 1', 'Jose Cruz', '09221234567'),
('HH-002', '456 Bonifacio Ave', 'Purok 1', 'Maria Dela Cruz', '09231234567'),
('HH-003', '789 Mabini Road', 'Purok 2', 'Pedro Reyes', '09241234567'),
('HH-004', '321 Luna Street', 'Purok 2', 'Ana Mendoza', '09251234567'),
('HH-005', '654 Aguinaldo Blvd', 'Purok 3', 'Carlos Bautista', '09261234567'),
('HH-006', '987 Quezon Ave', 'Purok 3', 'Lina Castillo', '09271234567'),
('HH-007', '147 Magsaysay St', 'Purok 4', 'Ramon Santos', '09281234567'),
('HH-008', '258 Laurel Drive', 'Purok 4', 'Elena Flores', '09291234567');

-- ============================================================
-- MOCK DATA - RESIDENTS
-- ============================================================
INSERT INTO residents (resident_id, household_id, last_name, first_name, middle_name, birthdate, birthplace, gender, civil_status, occupation, monthly_income, voter_status, email, contact_no) VALUES
('RES-2024-0001', 1, 'Cruz', 'Jose', 'Reyes', '1985-03-15', 'Manila', 'Male', 'Married', 'Tricycle Driver', 12000, 1, 'jose@email.com', '09221234567'),
('RES-2024-0002', 1, 'Cruz', 'Maria', 'Santos', '1987-07-22', 'Quezon City', 'Female', 'Married', 'Housewife', 0, 1, 'maria.cruz@email.com', '09221234568'),
('RES-2024-0003', 2, 'Dela Cruz', 'Maria', 'Lim', '1992-11-08', 'Manila', 'Female', 'Single', 'Teacher', 28000, 1, 'maria@email.com', '09231234567'),
('RES-2024-0004', 3, 'Reyes', 'Pedro', 'Bautista', '1978-05-30', 'Bulacan', 'Male', 'Married', 'Carpenter', 15000, 1, 'pedro@email.com', '09241234567'),
('RES-2024-0005', 4, 'Mendoza', 'Ana', 'Garcia', '2000-09-14', 'Manila', 'Female', 'Single', 'Student', 0, 0, 'ana@email.com', '09251234567'),
('RES-2024-0006', 5, 'Bautista', 'Carlos', 'Rivera', '1965-01-20', 'Cavite', 'Male', 'Widowed', 'Retired', 8000, 1, 'carlos@email.com', '09261234567'),
('RES-2024-0007', 6, 'Castillo', 'Lina', 'Ortega', '1990-06-12', 'Laguna', 'Female', 'Married', 'Nurse', 32000, 1, 'lina@email.com', '09271234567'),
('RES-2024-0008', 7, 'Santos', 'Ramon', 'Aquino', '1955-12-05', 'Pampanga', 'Male', 'Married', 'Retired', 5000, 1, 'ramon@email.com', '09281234567'),
('RES-2024-0009', 8, 'Flores', 'Elena', 'Torres', '1983-04-18', 'Batangas', 'Female', 'Married', 'Vendor', 9000, 1, 'elena@email.com', '09291234567'),
('RES-2024-0010', 1, 'Cruz', 'Juan', 'Reyes', '2010-08-25', 'Manila', 'Male', 'Single', 'Student', 0, 0, '', '');

UPDATE residents SET senior_citizen = 1 WHERE birthdate <= DATE_SUB(CURDATE(), INTERVAL 60 YEAR);
UPDATE residents r SET r.pwd = 1 WHERE r.id = 6;

-- ============================================================
-- MOCK DATA - DOCUMENT REQUESTS
-- ============================================================
INSERT INTO document_requests (request_no, resident_id, document_type, purpose, status, amount, payment_status, or_number, requested_by) VALUES
('REQ-2024-0001', 1, 'Barangay Clearance', 'Employment', 'Released', 100.00, 'Paid', 'OR-2024-001', 1),
('REQ-2024-0002', 2, 'Certificate of Residency', 'Bank Account Opening', 'Released', 50.00, 'Paid', 'OR-2024-002', 1),
('REQ-2024-0003', 3, 'Indigency Certificate', 'Scholarship Application', 'Approved', 0.00, 'Paid', NULL, 1),
('REQ-2024-0004', 4, 'Barangay Clearance', 'Business Permit', 'Processing', 100.00, 'Unpaid', NULL, 1),
('REQ-2024-0005', 5, 'Certificate of Residency', 'School Enrollment', 'Pending', 50.00, 'Unpaid', NULL, 1),
('REQ-2024-0006', 7, 'Business Clearance', 'Sari-sari Store', 'Pending', 200.00, 'Unpaid', NULL, 1);

UPDATE document_requests SET approved_by = 2, approved_at = NOW() WHERE status IN ('Approved','Released');
UPDATE document_requests SET released_by = 3, released_at = NOW() WHERE status = 'Released';

-- ============================================================
-- MOCK DATA - COMPLAINTS
-- ============================================================
INSERT INTO complaints (complaint_no, complainant_id, respondent_name, respondent_address, complaint_type, description, incident_date, incident_location, status) VALUES
('CMP-2024-0001', 1, 'Unknown Neighbor', 'Block 3, Purok 1', 'Noise Complaint', 'Loud music every night after 10 PM causing disturbance to neighbors.', '2024-01-15', '123 Rizal Street', 'Resolved'),
('CMP-2024-0002', 3, 'Spidy Sari-Sari Store', '456 Bonifacio Ave', 'Property Dispute', 'Store encroaching on our property boundary by approximately 1 meter.', '2024-01-20', '456 Bonifacio Ave', 'Under Investigation'),
('CMP-2024-0003', 5, 'Marco Villanueva', 'Block 5, Purok 2', 'Verbal Abuse', 'Respondent hurled offensive words at complainant in public.', '2024-02-01', 'Barangay Covered Court', 'Pending'),
('CMP-2024-0004', 7, 'Construction Site Owner', 'Purok 3', 'Noise Complaint', 'Early morning construction noise starting at 4 AM.', '2024-02-10', 'Aguinaldo Blvd', 'Mediation');

-- ============================================================
-- MOCK DATA - ANNOUNCEMENTS
-- ============================================================
INSERT INTO announcements (title, content, category, priority, start_date, end_date, is_published, created_by) VALUES
('Free Vaccination Drive - Anti-Rabies', 'Barangay San Marino Health Center will conduct a FREE Anti-Rabies vaccination for pets and livestock on February 15, 2024. Bring your pets to the Barangay Hall from 8:00 AM to 5:00 PM. This is in partnership with the City Veterinary Office.', 'Health', 'Important', '2024-02-10', '2024-02-15', 1, 2),
('Community Clean-Up Drive', 'All residents are invited to join our monthly Barangay Clean-Up Drive on February 17, 2024 (Saturday) starting at 7:00 AM. Kindly bring your own cleaning materials. Refreshments will be provided.', 'Events', 'Normal', '2024-02-12', '2024-02-17', 1, 2),
('Water Service Interruption Advisory', 'ADVISORY: There will be a scheduled water service interruption on February 20, 2024 from 8:00 AM to 5:00 PM due to maintenance works by Manila Water. Please store enough water for your household needs.', 'Advisory', 'Urgent', '2024-02-18', '2024-02-20', 1, 2),
('Senior Citizens Monthly Benefits Distribution', 'Senior Citizens are reminded to claim their monthly OSCA benefits at the Barangay Hall every last Friday of the month from 8:00 AM to 12:00 NN. Please bring valid ID and OSCA booklet.', 'Programs', 'Normal', '2024-02-01', '2024-12-31', 1, 2),
('Barangay Assembly Meeting', 'All residents are invited to attend the Quarterly Barangay Assembly Meeting on March 1, 2024 at 2:00 PM at the Barangay Covered Court. Agenda: Budget transparency, infrastructure projects update, and community concerns.', 'General', 'Important', '2024-02-20', '2024-03-01', 1, 2);

-- ============================================================
-- MOCK DATA - TRANSACTIONS
-- ============================================================
INSERT INTO transactions (or_number, document_request_id, resident_id, amount, payment_method, description, collected_by) VALUES
('OR-2024-001', 1, 1, 100.00, 'Cash', 'Payment for Barangay Clearance - Employment', 4),
('OR-2024-002', 2, 2, 50.00, 'Cash', 'Payment for Certificate of Residency - Bank Account', 4),
('OR-2024-003', NULL, 6, 200.00, 'GCash', 'Payment for Business Clearance', 4),
('OR-2024-004', NULL, 8, 100.00, 'Cash', 'Payment for Barangay Clearance - Retirement', 4),
('OR-2024-005', NULL, 9, 50.00, 'Cash', 'Payment for Certificate of Residency', 4);

-- ============================================================
-- DOCUMENT FEES
-- ============================================================
INSERT INTO document_fees (document_type, amount, description) VALUES
('Barangay Clearance', 100.00, 'Standard processing fee'),
('Certificate of Residency', 50.00, 'Standard processing fee'),
('Indigency Certificate', 0.00, 'Free of charge'),
('Business Clearance', 200.00, 'Standard processing fee'),
('Certificate of Good Moral Character', 75.00, 'Standard processing fee');
