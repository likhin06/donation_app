<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $request_id = $_POST['id'];
    $update_sql = "UPDATE donation_requests SET status = 'Rejected' WHERE id = $request_id";
    
    if ($conn->query($update_sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Request rejected successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to reject request."]);
    }
}

$conn->close();
?>
