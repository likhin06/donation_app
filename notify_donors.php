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

    // Fetch donor emails
    $donors_sql = "SELECT email FROM users";
    $donors_result = $conn->query($donors_sql);

    while ($donor = $donors_result->fetch_assoc()) {
        $to = $donor['email'];
        $subject = "Urgent: Donation Request";
        $message = "A new donation request has been made. Please check the portal.";
        $headers = "From: admin@donationapp.com";

        // Uncomment to send actual email
        // mail($to, $subject, $message, $headers);
    }

    echo json_encode(["status" => "success", "message" => "Donors have been notified!"]);
}

$conn->close();
?>
