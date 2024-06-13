<!DOCTYPE html>
<html lang="en">
<?php
// Database connection
$hostname = "localhost";
$username = "root";
$password = "";
$database = "student_system";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize arrays for storing data
$purposeLabels = [];
$purposeData = [];
$labLabels = [];
$labData = [];

// Check if a date is submitted
if (isset($_POST['submit'])) {
    $selectedDate = $_POST['date'];

    // Query to get purpose counts for the selected date
    $sqlPurpose = "SELECT purpose, COUNT(*) as count FROM sitin_records WHERE DATE(time_in) = '$selectedDate' AND time_out IS NOT NULL GROUP BY purpose";
    $resultPurpose = $conn->query($sqlPurpose);
    if ($resultPurpose->num_rows > 0) {
        while ($rowPurpose = $resultPurpose->fetch_assoc()) {
            $purposeLabels[] = $rowPurpose["purpose"];
            $purposeData[] = $rowPurpose["count"];
        }
    }

    // Query to get lab counts for the selected date
    $sqlLab = "SELECT lab_number, COUNT(*) as count FROM sitin_records WHERE DATE(time_in) = '$selectedDate' GROUP BY lab_number";
    $resultLab = $conn->query($sqlLab);
    if ($resultLab->num_rows > 0) {
        while ($rowLab = $resultLab->fetch_assoc()) {
            $labLabels[] = $rowLab["lab_number"];
            $labData[] = $rowLab["count"];
        }
    }
}

$conn->close();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('bgimage.jpg');
            background-size: cover; 
            background-repeat: no-repeat;
        }
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #f8f9fa; /* Light gray background color */
            padding-top: 1rem;
            transition: all 0.3s;
            z-index: 9999; /* Ensure sidebar is above content */
            border: 2px solid red;
        }
        #sidebar .nav-link {
            color: #000; /* Black color for navigation items */
            transition: color 0.3s;
            width: 100%; /* Make the nav links wider */
            border-bottom: 1px solid #ccc; /* Add border bottom */
            cursor: pointer; /* Change cursor to pointer */
        }
        #sidebar .nav-link:hover {
            color: #007bff; /* Change color on hover */
            text-decoration: none;
        }
        #sidebar.active {
            margin-left: -250px;
        }
        #content {
            transition: all 0.3s;
            margin-left: 430px;
            margin-top: 50px;
            padding: 20px;
        }
        .nav-item {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px;
        }
        .nav-icon {
            margin-right: 10px; /* Add some spacing between icon and text */
        }
        .rounded-circle {
            position: absolute;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%); /* Center the image */
            width: 100px;
            height: 100px;
            text-align: center;
        }
        .info-box {
            background-color: #fff; /* White background */
            border: 5px solid #ddd; /* Subtle border */
            width: 50%;
            padding: 10px;     
            background-color: #D3D3D3;
            text-align: center;
        }
        .container {
            margin: 20px;
        }

        .school-info{
            background-color: #fff; /* White background */
            border: 1px solid #ddd; /* Subtle border */
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        form {
            width: 50%;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-info">
        <div class="p-4">
            <h3 class="text-center"></h3>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="bi bi-house-door nav-icon"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="search_db.php"><i class="bi bi-search nav-icon"></i>Search</a></li>
                <li class="nav-item"><a class="nav-link" href="delete_sitin.php"><i class="bi bi-trash nav-icon"></i>Delete Sitin</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-calendar-event nav-icon"></i>Sitin</a></li>
                <li class="nav-item"><a class="nav-link" href="view_sitin_records.php"><i class="bi bi-journal nav-icon"></i>View Sitin Records</a></li>
                <li class="nav-item"><a class="nav-link" href="generate_reports.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Generate Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_feedback.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_announcement.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="reset_password.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reset Password</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_reservation.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="piechart.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Daily Analytics</a></li>
            </ul>
        </div>
        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
    
    <div id="content">
        <!-- Date filter form -->
        <form method="post" action="" class="mb-2">
            <div class="input-group">
                <label for="date" class="input-group-text">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control">
                <button type="submit" name="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
        <div style="display: flex;">
            <div style="width: 40%; margin-right: 10px;">
                <canvas id="purposeChart" width="50" height="50"></canvas>
            </div>
            <div style="width: 40%; margin-left: 10px;">
                <canvas id="labChart" width="50" height="50"></canvas>
            </div>
        </div>    
    </div>
    <br>
    <br>
</div>

<script>
    function generatePieChart(labels, data, canvasId) {
        var ctx = document.getElementById(canvasId).getContext('2d');
        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Count',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    // Check if purpose and lab data are not empty
    <?php if (!empty($purposeLabels) && !empty($purposeData) && !empty($labLabels) && !empty($labData)) { ?>
        // Generate pie chart for purpose
        generatePieChart(<?php echo json_encode($purposeLabels); ?>, <?php echo json_encode($purposeData); ?>, 'purposeChart');

        // Generate pie chart for lab
        generatePieChart(<?php echo json_encode($labLabels); ?>, <?php echo json_encode($labData); ?>, 'labChart');
    <?php } ?>

    document.getElementById('logoutBtn').addEventListener('click', function() {
        var logoutConfirmed = confirm("Are you sure you want to logout?");
        if (logoutConfirmed) {
            window.location.href = 'admin_login.php'; // Redirect to logout page
        }
    });
</script>

</body>
</html>
