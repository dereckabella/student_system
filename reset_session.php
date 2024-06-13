<?php
// Establish database connection
$hostname = "localhost";
$username = "root";
$password = "";
$database = "student_system";

$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve ID number from the form
    $id_number = $_POST['id_number'];

    // Update remaining sessions for the student to default value (30)
    $sql = "UPDATE registration SET remaining_sessions = 30 WHERE id_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_number);

    if ($stmt->execute()) {
        // Session reset successfully
        $message = "Session reset successfully for student with ID number: " . $id_number;
    } else {
        // Error occurred
        $error_message = "Error resetting session: " . $conn->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Student Session</title>
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
            margin-left: 250px;
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
                <li class="nav-item"><a class="nav-link" href="admin_announcement.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="reset_password.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reset Password</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_reservation.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="piechart.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Daily Analytics</a></li>
            </ul>
        </div>
        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
    
    <div id="content">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Reset Student Session</h2>
            <?php if(isset($message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="id_number">Enter Student ID Number:</label>
                    <input type="text" class="form-control" id="id_number" name="id_number" required>
                </div>
                <button type="submit" class="btn btn-primary">Reset Session</button>
            </form>
        </div>
    </div>
</body>
</html>
