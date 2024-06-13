<?php
session_start(); // Start session if not already started

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

$sql = "SELECT profile_image FROM registration WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_number']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();


// Query to retrieve announcements
$sql = "SELECT title, announcement, date_posted FROM announcements ORDER BY date_posted DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        .rounded-circle {
            position: absolute;
            top: 15%;
            left: 50%;
            transform: translate(-50%, -50%); /* Center the image */
            width: 100px;
            height: 100px;
        }

        .card {
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
        <div class="m-5">
            <?php
            if ($row && isset($row['profile_image'])) {
                $imageData = base64_encode($row['profile_image']);
                echo '<img src="data:image/jpeg;base64,'.$imageData.'" alt="Profile Picture" class="rounded-circle border border-3">';
            } else {
                echo '<img src="defaultimage.jpg" alt="Default Profile Picture" class="rounded-circle border border-3">';
            }
            ?>
        </div>
  
        <div class="p-4">
            <h3 class="text-center mt-4">
                <?php 
                if(isset($_SESSION['firstName']) && isset($_SESSION['lastName'])) {
                    echo $_SESSION['firstName'] . ' ' . $_SESSION['lastName'];
                }
                ?>
            </h3>
            <ul class="nav flex-column">
                <!-- Dashboard item with icon -->
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-house-door-fill"></i> Dashboard
                    </a>
                </li>
                <!-- Edit Profile item with icon -->
                <li class="nav-item">
                    <a class="nav-link" href="edit_profile.php">
                        <i class="bi bi-person-fill"></i> Edit Profile
                    </a>    
                </li>
                <!-- Sitin item with icon -->
                <li class="nav-item">
                    <a class="nav-link" href="historysitin.php">
                    <i class="bi bi-clock-history"></i> Sit-in History
                    </a>
                </li>
                <!-- View Remaining Sessions item with icon -->
                <li class="nav-item">
                    <a class="nav-link" href="feedback.php">
                    <i class="bi bi-clock-history"></i> Feedback
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_announcement.php">
                    <i class="bi bi-megaphone-fill"></i> Announcements
                    </a>
                </li>
            </ul>
        </div>

        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
  
    <div id="content">
        <h2 class="text-center mb-4">Announcements</h2>
        <?php if ($result && $result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><strong><?php echo $row["title"]; ?></strong></h5>
                        <p class="card-text"><?php echo $row["announcement"]; ?></p>
                        <p class="card-text"><small class="text-muted">Posted on <?php echo $row["date_posted"]; ?></small></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="alert alert-info" role="alert">
                No announcements available.
            </div>
        <?php endif; ?>
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
