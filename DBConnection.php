<?php
// Database credentials
$servername = "localhost"; // Change if using a different server
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "grocery_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($conn ==null) {
    die("Database connection not initialized.");
}


// Close connection (if needed later)
// $conn->close();
?>
