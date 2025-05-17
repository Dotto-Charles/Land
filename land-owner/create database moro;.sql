create database moro;
use moro;

  --Stores users (landowners, government officials, surveyors, buyers, admin).

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    second_name VARCHAR(255)  NULL,
    last_name VARCHAR(255) NOT NULL,
    sex ENUM('male', 'female') NOT NULL,
    national_id VARCHAR(50) UNIQUE NOT NULL,
    phone_number VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    confirm_password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'landowner', 'buyer', 'surveyor', 'lawyer','government_official') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


 --Stores registered land details.

CREATE TABLE land_parcels (
    land_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL, 
    land_title_no VARCHAR(50) UNIQUE NOT NULL,
    land_size DECIMAL(10,2) NOT NULL, -- in squared meters
    land_use ENUM('Residential', 'Commercial', 'Agricultural', 'Industrial') NOT NULL,
    region VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    ward VARCHAR(100) NOT NULL,
    village VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,8) NOT NULL, 
    longitude DECIMAL(11,8) NOT NULL, 
    registration_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE
);

  --Stores land verification requests by buyers or officials
CREATE TABLE land_verifications (
    verification_id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    land_id INT NOT NULL,
    verification_status ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
    verified_by INT DEFAULT NULL, -- Official verifying the request
    verified_at TIMESTAMP NULL,
    FOREIGN KEY (requester_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (land_id) REFERENCES land_parcels(land_id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL
);

  --Handles land sale & ownership change.
CREATE TABLE land_transfers (
    transfer_id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    buyer_id INT NOT NULL,
    land_id INT NOT NULL,
    sale_price DECIMAL(12,2) NOT NULL, 
    transfer_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    approved_by INT DEFAULT NULL, -- Government official who approves the transfer
    transfer_date TIMESTAMP NULL,
    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (land_id) REFERENCES land_parcels(land_id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL
);


  --Stores payment transactions for land registration, verification, and transfer.
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payer_id INT NOT NULL,
    land_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_type ENUM('Registration', 'Verification', 'Transfer'),
    transaction_id VARCHAR(50) UNIQUE NOT NULL, -- Mpesa Transaction ID
    payment_status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (land_id) REFERENCES land_parcels(land_id) ON DELETE CASCADE
);


  --Stores system notifications for users.
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    notification_type ENUM('SMS', 'Email') NOT NULL,
    status ENUM('Sent', 'Pending', 'Failed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

  --Handles land dispute records reported by users.
CREATE TABLE land_disputes (
    dispute_id INT AUTO_INCREMENT PRIMARY KEY,
    land_id INT NOT NULL,
    complainant_id INT NOT NULL,
    dispute_details TEXT NOT NULL,
    dispute_status ENUM('Pending', 'Resolved', 'Rejected') DEFAULT 'Pending',
    resolved_by INT DEFAULT NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (land_id) REFERENCES land_parcels(land_id) ON DELETE CASCADE,
    FOREIGN KEY (complainant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(user_id) ON DELETE SET NULL
);





-- Regions table
CREATE TABLE regions (
    region_id INT AUTO_INCREMENT,
    region_name VARCHAR(255) PRIMARY KEY NOT NULL
);

-- Districts table
CREATE TABLE districts (
    district_id INT AUTO_INCREMENT PRIMARY KEY,
    district_name VARCHAR(255) NOT NULL,
    region_id INT,
    FOREIGN KEY (region_id) REFERENCES regions(region_id) ON DELETE CASCADE
);

-- Wards table
CREATE TABLE wards (
    ward_id INT AUTO_INCREMENT PRIMARY KEY,
    ward_name VARCHAR(255) NOT NULL,
    district_id INT,
    FOREIGN KEY (district_id) REFERENCES districts(district_id) ON DELETE CASCADE
);

-- Villages table
CREATE TABLE villages (
    village_id INT AUTO_INCREMENT PRIMARY KEY,
    ward_id INT NOT NULL,  -- This references the ward
    village_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ward_id) REFERENCES wards(ward_id) ON DELETE CASCADE
);


CREATE TABLE land_title_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    land_title_no VARCHAR(100) DEFAULT NULL,
    request_status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
