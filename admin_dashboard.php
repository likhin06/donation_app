<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registered users
$users_sql = "SELECT id, name, email, phone FROM users";
$users_result = $conn->query($users_sql);

// Fetch donor form entries (donations)
$donations_sql = "SELECT id, from_address, to_address, phone_number, quantity, status FROM donations";
$donations_result = $conn->query($donations_sql);

// Fetch donations to trust
$donationst_sql = "SELECT id, from_address, donation_type, amount, clothing_pieces, donation_date FROM donationst";
$donationst_result = $conn->query($donationst_sql);

// Fetch donation requests
$requests_sql = "SELECT id, reason, address, donation_type, status, created_at FROM donation_requests";
$requests_result = $conn->query($requests_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; color: black; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: blue; color: white; }
        .btn { padding: 5px 10px; border: none; cursor: pointer; margin: 2px; }
        .approve-btn { background-color: green; color: white; }
        .notify-btn { background-color: orange; color: white; }
        .fund-btn { background-color: green; color: white; }
        .reject-btn { background-color: red; color: white; }
        .approved-badge { color: limegreen; font-weight: bold; }
        .back-button {
    background-color: blue; /* Change button color to blue */
    color: white; /* White text for contrast */
    padding: 15px 30px; /* Increase padding for bigger size */
    font-size: 18px; /* Increase font size */
    border: none; /* Remove border */
    border-radius: 8px; /* Rounded corners */
    cursor: pointer; /* Show pointer on hover */
    display: inline-block;
    transition: 0.3s; /* Smooth hover effect */
}

.back-button:hover {
    background-color: darkblue; /* Slightly darker blue on hover */
}
    </style>
</head>
<body>

<a href="donation_graph.php" class="graph-btn" style="display: inline-block; margin: 20px; padding: 10px 20px; background-color: green; color: white; text-decoration: none; border-radius: 5px;">
    📊 View Donation Graph
</a>

<h2>Admin Dashboard</h2>

<!-- Registered Users -->
<h3>Registered Users</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
    </tr>
    <?php while ($row = $users_result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= $row['phone'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Donor Form Entries -->
<h3>Donor Form Entries</h3>
<table>
    <tr>
        <th>ID</th>
        <th>From Address</th>
        <th>To Address</th>
        <th>Phone</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Approve</th>
    </tr>
    <?php while ($row = $donations_result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['from_address'] ?></td>
        <td><?= $row['to_address'] ?></td>
        <td><?= $row['phone_number'] ?></td>
        <td><?= $row['quantity'] ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <?php if ($row['status'] === 'Pending'): ?>
                <button class="btn approve-btn" onclick="approveDonation(<?= $row['id'] ?>)">Approve</button>
            <?php else: ?>
                <span class="approved-badge">✅ Approved</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Donations to Trust -->
<h3>Donations to Trust</h3>
<table>
    <tr>
        <th>ID</th>
        <th>From Address</th>
        <th>Donation Type</th>
        <th>Amount</th>
        <th>Clothing Pieces</th>
        <th>Donation Date</th>
    </tr>
    <?php while ($row = $donationst_result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['from_address'] ?></td>
        <td><?= $row['donation_type'] ?></td>
        <td><?= $row['amount'] ? $row['amount'] . " 💰" : "N/A" ?></td>
        <td><?= $row['clothing_pieces'] ? $row['clothing_pieces'] . " 👕" : "N/A" ?></td>
        <td><?= $row['donation_date'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Donation Requests -->
<h3>Donation Requests</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Reason</th>
        <th>Address</th>
        <th>Donation Type</th>
        <th>Status</th>
        <th>Requested At</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $requests_result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['reason'] ?></td>
        <td><?= $row['address'] ?></td>
        <td><?= $row['donation_type'] ?></td>
        <td><?= $row['status'] ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
        <button onclick="notifyDonor(<?= $row['id'] ?>)">Notify Donor</button>
            <button class="btn fund-btn" onclick="fundRequest(<?= $row['id'] ?>)">Fund Them</button>
            <button class="btn reject-btn" onclick="rejectRequest(<?= $row['id'] ?>)">Reject</button>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
function fundRequest(requestId) {
    window.location.href = "fund_request.php?id=" + requestId;
}

function rejectRequest(requestId) {
    if (confirm("Are you sure you want to reject this request?")) {
        fetch("reject_request.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + requestId
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => console.error("Error:", error));
    }
}
function notifyDonor(requestId) {
    fetch('notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + requestId
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            location.reload(); // Refresh admin dashboard
        } else {
            console.error("Error:", data.error);
        }
    })
    .catch(error => console.error("Error:", error));
}


</script>


<button class="back-button" onclick="window.location.href='home.html'">LOG OUT</button>

</body>
</html>

<?php $conn->close(); ?>