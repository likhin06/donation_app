<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM notifications WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Notification ignored successfully"]);
    } else {
        echo json_encode(["error" => "Error deleting notification: " . $conn->error]);
    }
}

$conn->close();
?>