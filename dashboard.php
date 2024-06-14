<?php
session_start(); // Start the session at the beginning

$hostname = "localhost"; 
$username = "root";
$password = "";
$database = "student_system";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['id_number'])) {
    // User is not logged in
    header("Location: login.php"); // Redirect to login
    exit();
}

// Query to retrieve remaining sessions from the database
$sql = "SELECT remaining_sessions, is_approved FROM registration WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_number']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$remaining_sessions = $row['remaining_sessions'];
$is_approved = $row['is_approved'];


$stmt = $conn->prepare("UPDATE reservations SET admin_status = 'approved' WHERE reservation_id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$stmt->close();

// Now, update is_approved for the corresponding user
// Assuming $_SESSION['id_number'] contains the user's id
$stmt = $conn->prepare("UPDATE registration SET is_approved = 1 WHERE id_number = ?");
$stmt->bind_param("i", $_SESSION['id_number']);
$stmt->execute();
$stmt->close();
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
            font-size: 10px;
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

        .info-box {
            background-color: #fff; /* White background */
            border: 1px solid #ddd; /* Subtle border */
            padding: 20px;
            margin-bottom: 20px; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: Adds subtle shadow */
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-info">
        <div class="m-5">
            <?php
            $sql = "SELECT profile_image FROM registration WHERE id_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['id_number']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['profile_image']) {
                $imageData = base64_encode($row['profile_image']);
                echo '<img src="data:image/jpeg;base64,'.$imageData.'" alt="Profile Picture" class="rounded-circle border border-3">';
            } else {
                echo '<img src="defaultimage.jpg" alt="Default Profile Picture" class="rounded-circle border border-3">';
            }
            ?>
        </div>
  
        <div class="p-4">
            <h3 class="text-center mt-4"><?php echo $_SESSION['firstName'] . ' ' . $_SESSION['lastName']; ?></h3>
            <ul class="nav flex-column">
                <!-- Dashboard item with icon -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
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

                <li class="nav-item">
                    <a class="nav-link" href="sitin_rules.php">
                    <i class="bi bi-file-earmark-ruled-fill"></i> Sitin Rules
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="student_reservation.php">
                    <i class="bi bi-file-earmark-ruled-fill"></i> Reservation
                    </a>
                </li>
                
            </ul>
        </div>

        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
  

    <p>shhessh update</p>
    
    <div id="content">
        <header class="bg-primary text-white py-3 text-center rounded ">
            <h1>Welcome, <?php echo $_SESSION['firstName']; ?>!</h1>
        </header>

        <br>
        <br>
        <div class="container">
            <div class="row d-flex justify-content-start"> 
                <div class="col-md-4">
                    <div class="info-box">
                        <h3>Remaining Sessions</h3>
                        <p class="display-4"><?php echo $remaining_sessions; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
    <div class="info-box">
        <h3>Reservation Status</h3>
        <p class="display-6">
            <?php 
            if ($is_approved == 1): 
                echo "Your reservation has been approved."; 
            else: 
            
                echo "You have not been approved yet.";
            endif; 
            ?>
        </p>
    </div>
</div>

                <div class="col-md-4">
                    <div class="info-box">
                        <h3>Available PC</h3>
                        <p class="display-4">5</p>
                    </div>
                </div>
            </div>

           
        </div>
    </div>
</div>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function() {
        var logoutConfirmed = confirm("Are you sure you want to logout?");
        if (logoutConfirmed) {
            window.location.href = 'login.php'; 
        }
    });
</script>

</body>
</html>
