<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $requestId = intval($_POST['id']);
    $message = "New donation request received!";

    // Start transaction to ensure both queries execute
    $conn->begin_transaction();

    try {
        // Insert notification
        $sql = "INSERT INTO notifications (request_id, message, status) 
                VALUES ($requestId, '$message', 'unread')";
        $conn->query($sql);

        // Update request status
        $update_sql = "UPDATE donation_requests SET status = 'Notified' WHERE id = $requestId";
        $conn->query($update_sql);

        // Commit transaction
        $conn->commit();
        echo json_encode(["message" => "Notification sent and status updated"]);
    } catch (Exception $e) {
        $conn->rollback(); // Rollback changes on error
        echo json_encode(["error" => "Failed: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}

$conn->close();
?>
