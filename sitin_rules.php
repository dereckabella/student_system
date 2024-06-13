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
$sql = "SELECT remaining_sessions FROM registration WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_number']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$remaining_sessions = $row['remaining_sessions'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Rules</title>
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
        .container {
            background-color: #fff; /* White background */
            border-radius: 10px; /* Rounded corners */
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add shadow */
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .rule {
            margin-bottom: 20px;
            background-color: #87CEEB;
            border-radius: 10px;
            border: 5px solid #ddd;
            width: 90%;
            margin: 0 auto;
        }
    </style>
</head>
<body>

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
                    <i class="bi bi-clock-history"></i> Announcements
                    </a>
                </li>
                
            </ul>
        </div>

        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
    <div id="content">
        <!-- University of Cebu Laboratory Rules and Regulations -->
        <div class="rule">
            <h2>University of Cebu Laboratory Rules and Regulations</h2>
            <ol>
                <li>
                    <h5>Maintain Silence and Decorum</h5>
                    <p>Students must maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans, and other personal pieces of equipment must be switched off.</p>
                </li>
                <li>
                    <h5>No Games Allowed</h5>
                    <p>Games, including computer-related games, card games, and other games that may disturb the operation of the lab, are not allowed.</p>
                </li>
                <li>
                    <h5>Internet Usage</h5>
                    <p>Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing software are strictly prohibited.</p>
                </li>
                <li>
                    <h5>Internet Access Restrictions</h5>
                    <p>Getting access to other websites not related to the course, especially pornographic and illicit sites, is strictly prohibited.</p>
                </li>
                <li>
                    <h5>No Unauthorized Changes</h5>
                    <p>Deleting computer files and changing the setup of the computer is considered a major offense.</p>
                </li>
                <li>
                    <h5>Computer Time Usage</h5>
                    <p>Students must observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</p>
                </li>
                <li>
                    <h5>Proper Decorum Inside the Laboratory</h5>
                    <p>
                        <ul>
                            <li>Do not enter the lab unless the instructor is present.</li>
                            <li>All bags, knapsacks, and the likes must be deposited at the counter.</li>
                            <li>Follow the seating arrangement of your instructor.</li>
                            <li>At the end of class, all software programs must be closed.</li>
                            <li>Return all chairs to their proper places after using.</li>
                        </ul>
                    </p>
                </li>
                <li>
                    <h5>Prohibited Activities</h5>
                    <p>Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</p>
                </li>
                <li>
                    <h5>Disturbance and Offensive Behavior</h5>
                    <p>
                        <ul>
                            <li>Anyone causing a continual disturbance will be asked to leave the lab.</li>
                            <li>Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</li>
                            <li>Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</li>
                        </ul>
                    </p>
                </li>
                <li>
                    <h5>Escalation of Serious Offenses</h5>
                    <p>For serious offenses, the lab personnel may call the Civil Security Office (CSU) for assistance.</p>
                </li>
                <li>
                    <h5>Reporting Technical Problems</h5>
                    <p>Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant, or instructor immediately.</p>
                </li>
            </ol>
        </div>
        <!-- End of University of Cebu Laboratory Rules and Regulations -->
    </div>
</body>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function() {
        var logoutConfirmed = confirm("Are you sure you want to logout?");
        if (logoutConfirmed) {
            window.location.href = 'login.php'; // Redirect to logout page
        }
    });
</script>
</html>
