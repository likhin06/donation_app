<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$from_address = $_POST['from_address'];
$donation_type = isset($_POST['donation_type']) ? $_POST['donation_type'] : '';
$amount = isset($_POST['amount']) ? $_POST['amount'] : NULL;
$clothing_pieces = isset($_POST['clothing_pieces']) ? $_POST['clothing_pieces'] : NULL;

$sql = "INSERT INTO donationst (from_address, donation_type, amount, clothing_pieces) 
        VALUES ('$from_address', '$donation_type', '$amount', '$clothing_pieces')";

if ($conn->query($sql) === TRUE) {
    // Redirect to thank you page
    header("Location: thankyou.html");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
