<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM donationst";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donation Records</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>

<h2>Donation Records</h2>
<table>
    <tr>
        <th>ID</th>
        <th>From Address</th>
        <th>Donation Type</th>
        <th>Amount</th>
        <th>Clothing Pieces</th>
        <th>Donation Date</th>
    </tr>
    <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row["id"]; ?></td>
        <td><?php echo $row["from_address"]; ?></td>
        <td><?php echo $row["donation_type"]; ?></td>
        <td><?php echo $row["amount"] ?? "N/A"; ?></td>
        <td><?php echo $row["clothing_pieces"] ?? "N/A"; ?></td>
        <td><?php echo $row["donation_date"]; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>

<?php
$conn->close();
?>
