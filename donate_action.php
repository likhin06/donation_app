<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $notification_id = intval($_POST["id"]);
    
    $sql = "UPDATE notification SET status = 'Donated' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notification_id);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Thank you for your donation!"]);
    } else {
        echo json_encode(["message" => "Failed to update donation status"]);
    }
}
$conn->close();
?>
