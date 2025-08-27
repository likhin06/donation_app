<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if request ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No request ID provided.");
}

$request_id = intval($_GET['id']);

// Fetch the donation request details
$request_sql = "SELECT id, reason, address, donation_type, created_at, allocated_amount, status FROM donation_requests WHERE id = $request_id";
$request_result = $conn->query($request_sql);
if ($request_result->num_rows === 0) {
    die("Donation request not found.");
}
$request = $request_result->fetch_assoc();

// ✅ Fetch total available funds from donations
$funds_sql = "SELECT SUM(amount) AS total_funds FROM donationst WHERE amount > 0";
$funds_result = $conn->query($funds_sql);
$total_funds = $funds_result->fetch_assoc()['total_funds'] ?? 0;

// Debugging: Check if total_funds is fetched correctly
// echo "Total Funds: " . $total_funds; exit;

// Handle fund allocation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $allocated_amount = floatval($_POST['allocated_amount']);

    if ($allocated_amount > 0 && $allocated_amount <= $total_funds) {
        // ✅ Update the request as "Funded" and store allocated amount
        $update_request_sql = "UPDATE donation_requests SET status = 'Funded', allocated_amount = $allocated_amount WHERE id = $request_id";
        if ($conn->query($update_request_sql) === TRUE) {
            // ✅ Deduct the allocated amount from total funds
            $deduct_fund_sql = "UPDATE donationst SET amount = amount - $allocated_amount WHERE amount >= $allocated_amount LIMIT 1";
            $conn->query($deduct_fund_sql);

            echo "<script>alert('Funds allocated successfully!'); window.location.href = 'admin_dashboard.php';</script>";
        } else {
            echo "Error updating request: " . $conn->error;
        }
    } else {
        echo "<script>alert('Invalid amount. Ensure it does not exceed available funds.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fund Donation Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: blue;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: green;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: darkgreen;
        }
        .back-btn {
            display: inline-block;
            margin-top: 15px;
            background-color: red;
            text-decoration: none;
            padding: 10px 15px;
            color: white;
            border-radius: 5px;
        }
        .back-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Fund Donation Request</h2>
    <p><strong>Reason:</strong> <?= htmlspecialchars($request['reason']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($request['address']) ?></p>
    <p><strong>Donation Type:</strong> <?= htmlspecialchars($request['donation_type']) ?></p>
    <p><strong>Requested At:</strong> <?= htmlspecialchars($request['created_at']) ?></p>
    
    <!-- ✅ Displaying Total Available Funds -->
    <p><strong>Total Available Funds:</strong> 💰 <?= number_format($total_funds, 2) ?></p>

    <form method="POST">
        <label for="allocated_amount">Allocate Amount (in 💰):</label>
        <input type="number" step="0.01" name="allocated_amount" required>
        <button type="submit">Fund Request</button>
    </form>

    <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
</div>
</body>
</html>