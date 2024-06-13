<?php
session_start();
$hostname = "localhost"; // replace with your actual database hostname
$username = "root";
$password = "";
$database = "student_system";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["searchInput"])) {
    $searchInput = $_POST["searchInput"];
    $searchQuery = "SELECT * FROM registration WHERE id_number = '$searchInput'";
    $result = $conn->query($searchQuery);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch only the relevant student's data

        // Output creation in a structured way
        $output = "<div class='row'>"; 
        $output .= "<div class='info-box fs-5'>";  // Start info box
        $output .= "<h2 class='text-center'>Student SitIn Information</h2>";
        $output .= "<div class='mb-2 col-md-6'><strong>ID Number:</strong> {$row['id_number']}</div>";
        $output .= "<div class='mb-2 col-md-8'><strong>Full Name:</strong> {$row['firstName']} {$row['middleName']} {$row['lastName']}</div>";
        $output .= "<div class='mb-2 col-md-6'><strong>Purpose:</strong> 
                    <select name='purpose'>
                        <option value='Lab'>Lab</option>
                        <option value='Exam'>Exam</option>
                        <option value='Research'>Research</option>
                    </select>
                </div>";
        $output .= "<div class='mb-2 col-md-6'><strong>Laboratory:</strong> <input type='text' name='labNumber'></div>"; 
        $output .= "<div class='mb-2 col-md-6'><strong>Remaining Sessions:</strong> <input type='text' name='session1'></div>";  
        $output .= "<button class='mb-3' onclick='sitIn()'>Sit In</button>"; // Sit In button
        $output .= "</div>";
        $output .= "</div>"; 
    } else {
        $output = "<h1>Student Not Found</h1>";
    }

    echo $output;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link" href="reset_session.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reset Session</a></li>
                <li class="nav-item"><a class="nav-link" href="reset_password.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reset Password</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_reservation.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="piechart.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Daily Analytics</a></li>
            </ul>
        </div>
        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
    
    <div id="content">
    <div class="school-info">
      <h2>About Our School</h2>
      <p>We ensure that teaching and learning resources provide students with challenging and engaging programs that will hone their skills and prepare them to become globally competitive.
We offer clubs and associations that our student can join. These are run by students themselves (and usually one staff or faculty advisor) and cover all sorts of interests.</p>
  </div>
            
    </div>
    <br>
    <br>
</div>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function() {
        var logoutConfirmed = confirm("Are you sure you want to logout?");
        if (logoutConfirmed) {
            window.location.href = 'admin_login.php'; // Redirect to logout page
        }
    });
</script>

</body>
</html>
