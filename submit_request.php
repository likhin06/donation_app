<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Default for XAMPP
$password = "";      // Default for XAMPP
$dbname = "donation_db"; // Database name
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['requestReason']) && !empty($_POST['requestAddress']) && !empty($_POST['donationType'])) {
        
        $reason = $conn->real_escape_string($_POST['requestReason']);
        $address = $conn->real_escape_string($_POST['requestAddress']);
        $donationType = implode(", ", $_POST['donationType']); // Convert array to string
        
        // Insert data into database
        $sql = "INSERT INTO donation_requests (reason, address, donation_type) VALUES ('$reason', '$address', '$donationType')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Request submitted successfully!'); window.location.href='home.html';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('Please fill all fields and select at least one donation type.'); window.history.back();</script>";
    }
} else {
    echo "Invalid request!";
}

// Close connection
$conn->close();
?>