<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total registered users
$user_count_sql = "SELECT COUNT(*) as total_users FROM users";
$user_count_result = $conn->query($user_count_sql);
$user_count = $user_count_result->fetch_assoc()['total_users'];

// Fetch total donations
$donation_count_sql = "SELECT COUNT(*) as total_donations FROM donationst";
$donation_count_result = $conn->query($donation_count_sql);
$donation_count = $donation_count_result->fetch_assoc()['total_donations'];

// Fetch donation data for the graph
$donation_chart_sql = "SELECT donation_type, COUNT(*) as count FROM donationst GROUP BY donation_type";
$donation_chart_result = $conn->query($donation_chart_sql);

$donation_types = [];
$donation_counts = [];

while ($row = $donation_chart_result->fetch_assoc()) {
    $donation_types[] = $row['donation_type'];
    $donation_counts[] = $row['count'];
}

// Convert data to JSON for Chart.js
$donation_types_json = json_encode($donation_types);
$donation_counts_json = json_encode($donation_counts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            background-color: #f4f4f4; 
            padding: 20px; 
        }
        .chart-container { 
            width: 60%; 
            margin: auto; 
        }
        .stats { 
            display: flex; 
            justify-content: center; 
            gap: 30px; 
            margin-bottom: 20px; 
        }
        .stat-box { 
            background: white; 
            padding: 15px; 
            border-radius: 10px; 
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); 
        }
        .stat-box h3 { 
            margin: 0; 
            color: rgb(20, 134, 169); 
        }
        .stat-box p { 
            font-size: 20px; 
            font-weight: bold; 
            color: blue; 
        }
        .back-btn { 
            display: inline-block; 
            margin: 20px; 
            padding: 10px 20px; 
            background-color: blue; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
        .back-btn:hover { 
            background-color: darkblue; 
        }
    </style>
</head>
<body>

    <h2>Donation Statistics</h2>

    <!-- Display Total Users and Total Donations -->
    <div class="stats">
        <div class="stat-box">
            <h3>👥 Total Registered Users</h3>
            <p><?= $user_count ?></p>
        </div>
        <div class="stat-box">
            <h3>🎁 Total Donations to Trust</h3>
            <p><?= $donation_count ?></p>
        </div>
    </div>

    <!-- Donation Graph -->
    <div class="chart-container">
        <canvas id="donationChart"></canvas>
    </div>

    <a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

    <script>
        var ctx = document.getElementById('donationChart').getContext('2d');
        var donationChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $donation_types_json ?>,
                datasets: [{
                    label: 'Number of Donations',
                    data: <?= $donation_counts_json ?>,
                    backgroundColor: ['#00FFFF', '#33FF57'], // Cyan & Green
                    borderColor: ['#00CED1', '#28A745'], // Darker cyan & green for contrast
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false } // Hide legend if needed
                },
                barPercentage: 0.2, // Reduce bar width (default is 0.9)
                categoryPercentage: 0.3 // Reduce spacing between bars
            }
        });
    </script>

</body>
</html>

<?php $conn->close(); ?>
