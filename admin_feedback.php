<?php
// Database connection parameters
$hostname = "localhost"; 
$username = "root";
$password = "";
$database = "student_system";

// Create database connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve feedback data including student names and timestamp
$sql = "SELECT feedback.message, registration.firstName, registration.lastName, feedback.created_at 
        FROM feedback 
        INNER JOIN registration ON feedback.id_number = registration.id_number";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Feedback</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Your custom CSS styles here */
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
            padding-top: 5rem;
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
            margin-left: 250px;
            padding: 20px;
        }
        .nav-item {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
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
        .feedback-container {
            background-color: #fff;
            border: 3px solid #ccc;
            border-radius: 5px;
            padding: 30px;
            margin: 0 auto; 
            margin-bottom: 20px;
            width: 90%;
            max-width: 850px; 
        }
    </style>
</head>
<body>
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
            </ul>
        </div>
        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
    <div id="content">
        <h2 class="text-center mb-4">Feedback from Students</h2>
        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="feedback-container">
                    <p><strong>Student Name:</strong> <?php echo $row["firstName"] . " " . $row["lastName"]; ?></p>
                    <p><strong>Message:</strong> <?php echo $row["message"]; ?></p>
                    <p><strong>Time Posted:</strong> <?php echo $row["created_at"]; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="alert alert-info" role="alert">
                No feedback available.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
