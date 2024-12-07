<?php
$database = 'BRBMSP';
$username = 'root';
$password = '';
$host = 'localhost';

try {
    $db = new PDO("mysql:host=$host", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database if not exists
    $db->exec("CREATE DATABASE IF NOT EXISTS $database");
    $db->exec("USE $database");

    // 1. Users Table
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            contact_number VARCHAR(11) NOT NULL,
            gender  VARCHAR(50) NOT NULL,
            user_role ENUM('customer', 'owner') NOT NULL,
            date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // 2. Beaches Table
    $db->exec(" 
        CREATE TABLE IF NOT EXISTS beaches (
            beach_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            beach_name VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            location VARCHAR(255),
            latitude DECIMAL(9,6),
            longitude DECIMAL(9,6),
            user_id INT NOT NULL,
            gcash_qr_code VARCHAR(255),
            gcash_name VARCHAR(255),
            gcash_number int(11),
            image VARCHAR(255),
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            INDEX (user_id)
        )
    ");

$db->exec(" 
    CREATE TABLE IF NOT EXISTS beach_gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,         -- Auto increment ID for each image
        beach_id INT NOT NULL,                     -- ID of the beach to associate with the image
        image_url VARCHAR(255) NOT NULL,            -- URL of the image
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Timestamp for when the image was added
        FOREIGN KEY (beach_id) REFERENCES beaches(beach_id) -- Foreign key referencing beaches(beach_id)
            )
        ");

    // 3. Amenities Table
    $db->exec(" 
        CREATE TABLE IF NOT EXISTS amenities (
            amenity_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            beach_id INT NOT NULL,
            amenity_type VARCHAR(100) NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            price_type ENUM('per_night', 'per_day') NOT NULL,
            capacity INT DEFAULT 0,
            image VARCHAR(255),
            availability_status ENUM('available', 'unavailable') DEFAULT 'available',
            FOREIGN KEY (beach_id) REFERENCES beaches(beach_id) ON DELETE CASCADE,
            INDEX (beach_id)
        )
    ");

    // 4. Reservations Table
    $db->exec(" 
        CREATE TABLE IF NOT EXISTS reservations (
            reservation_id INT AUTO_INCREMENT PRIMARY KEY,  
            user_id INT NOT NULL,                         
            beach_id INT NOT NULL,                         
            amenity_id INT NOT NULL,                       
            reservation_date DATETIME NOT NULL,            
            quantity INT NOT NULL,                        
            total_amount DECIMAL(10, 2) NOT NULL,          
            status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL, 
            payment_status ENUM('full', 'partial', 'pending') NOT NULL,  
            payment_method ENUM('GCash', 'PayPal', 'Bank Transfer', 'Cash') NOT NULL,  
            customer_name VARCHAR(255) NOT NULL,           
            customer_address TEXT NOT NULL,                
            contact_number VARCHAR(20) NOT NULL,           
            checkin_date DATE NOT NULL,                    
            checkout_date DATE NOT NULL,                   
            reference_number VARCHAR(255) NOT NULL,        
            gcash_reference INT,
            gcash_name VARCHAR(255),
            gcash_number INT, 
            FOREIGN KEY (user_id) REFERENCES users(user_id),  
            FOREIGN KEY (beach_id) REFERENCES beaches(beach_id),  
            FOREIGN KEY (amenity_id) REFERENCES amenities(amenity_id)  
        )
    ");

    // 5. Reviews Table
    $db->exec(" 
        CREATE TABLE IF NOT EXISTS reviews (
            review_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            beach_id INT NOT NULL,
            rating INT CHECK (rating BETWEEN 1 AND 5),
            review_text TEXT,
            review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            FOREIGN KEY (beach_id) REFERENCES beaches(beach_id) ON DELETE CASCADE,
            INDEX (user_id, beach_id)
        )
    ");

    // 6. Messages Table
    $db->exec(" 
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,   
            receiver_id INT NOT NULL, 
            beach_id INT NOT NULL,    
            message TEXT NOT NULL,    
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            is_read BOOLEAN DEFAULT FALSE,  
            FOREIGN KEY (sender_id) REFERENCES users(user_id),
            FOREIGN KEY (receiver_id) REFERENCES users(user_id),
            FOREIGN KEY (beach_id) REFERENCES beaches(beach_id)  
        )
    ");

    // 7. Transactions Table
    $db->exec(" 
        CREATE TABLE IF NOT EXISTS transactions (
            transaction_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            reservation_id INT NOT NULL,
            user_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payment_method ENUM('gcash', 'cash') DEFAULT 'gcash',
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending',
            FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            INDEX (reservation_id, user_id)
        )
    ");

} catch (PDOException $e) {
    // Log errors instead of using die()
    error_log("Database error: " . $e->getMessage(), 3, "errors.log");
    echo "An error occurred while setting up the database. Please check the logs.";
}
?>
