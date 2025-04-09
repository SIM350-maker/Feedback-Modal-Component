<?php
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "ratings_db"; 

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);
$rating = isset($data['rating']) ? (int)$data['rating'] : null;

// Validate the rating
if ($rating === null || $rating < 1 || $rating > 10) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid rating value']);
    exit;
}

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $createDbSql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (!$conn->query($createDbSql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($dbname);
    
    // Create table if it doesn't exist
    $createTableSql = "CREATE TABLE IF NOT EXISTS ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rating INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTableSql)) {
        throw new Exception("Error creating table: " . $conn->error);
    }
    
    // Insert the rating
    $stmt = $conn->prepare("INSERT INTO ratings (rating) VALUES (?)");
    $stmt->bind_param("i", $rating);
    
    if (!$stmt->execute()) {
        throw new Exception("Error inserting rating: " . $stmt->error);
    }
    
    // Success response
    echo json_encode(['message' => 'Rating submitted successfully!']);
    
    // Close connections
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>