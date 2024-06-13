<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['id_number'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$hostname = "localhost";
$username = "root";
$password = "";
$database = "student_system";
$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('bgimage.jpg');
            background-size: cover; 
            background-repeat: no-repeat;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
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

        table {
            width: 100%;
            margin-top: 20px;
        }
        
        .table thead th {
            background-color: #007bff;
            color: white;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div>
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
                    <!-- My Reservations item with icon -->
                    <li class="nav-item">
                        <a class="nav-link" href="my_reservations.php">
                        <i class="bi bi-calendar2-check"></i> My Reservations
                        </a>
                    </li>
                    
                </ul>
            </div>

            <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3">Logout</button>
        </nav>

        <div id="content">
            <div class="container">
                <h2>My Reservations</h2>
                <?php
                $sql = "SELECT reservation_id, lab_number, computer_number, purpose, reservation_date, admin_status, decline_reason FROM reservations WHERE id_number = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_SESSION['id_number']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<table class='table table-striped table-bordered'>";
                    echo "<thead><tr><th>Lab Number</th><th>Computer Number</th><th>Purpose</th><th>Reservation Date</th><th>Status</th><th>Decline Reason</th></tr></thead>";
                    echo "<tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["lab_number"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["computer_number"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["purpose"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["reservation_date"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["admin_status"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["decline_reason"]) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p class='alert alert-warning'>No reservations found.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
