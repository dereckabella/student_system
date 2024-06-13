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

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $_SESSION['id_number'];
    $lab_number = $_POST["lab_number"];
    $computer_number = $_POST["computer_number"];
    $purpose = $_POST["purpose"];
    $reservation_date = $_POST["reservation_date"];

    // Prepare and execute SQL statement to insert reservation
    $stmt = $conn->prepare("INSERT INTO reservations (id_number, lab_number, computer_number, purpose, reservation_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_number, $lab_number, $computer_number, $purpose, $reservation_date);

    if ($stmt->execute()) {
        echo '<div id="content" class="alert alert-success" role="alert">Reservation successfully created.</div>';
    } else {
        echo '<div id="content" class="alert alert-danger" role="alert">Error creating reservation: ' . $conn->error . '</div>';
    }

    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reservation</title>
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

        h2 {
            text-align: center;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="datetime-local"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            background-color: #3377ff;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            text-transform: uppercase;
        }

        input[type="submit"]:hover {
            background-color: #2255cc;
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

        form {
            width: 50%; 
            margin: 0 auto;
            border: 3px solid #ccc;
            border-radius: 5px;
            padding: 30px;  
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
                    
                </ul>
            </div>

            <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
        </nav>

        <div id="content">
        <h2>Reservation Form</h2>
        <form action="student_reservation.php" method="POST">
            <div class="form-group">
                <label for="lab_number">Laboratory Room:</label>
                <select id="lab_number" name="lab_number" class="form-control" required>
                    <?php
                    
                    $purposes = ['C++', 'JAVA', 'PYTHON', 'C#', 'EXAM'];
                    $lab_rooms = ['526', '527', '528', '540', '542'];
                   
                    foreach ($lab_rooms as $lab) {
                        echo "<option value='$lab'>$lab</option>";
                    }
                    ?>
                </select>
            </div>
               <div class="form-group">
                <label for="computer_number">Computer Number:</label>
                <select id="computer_number" name="computer_number" class="form-control" required>
                    <?php
                   
                    for ($i = 1; $i <= 50; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="purpose">Purpose:</label>
                <select id="purpose" name="purpose" class="form-control" required>
                    <?php
                    // Output each purpose as an option in the dropdown menu
                    foreach ($purposes as $purpose) {
                        echo "<option value='$purpose'>$purpose</option>";
                    }
                    ?>
                </select>
            </div>
                <div class="form-group">
                    <label for="reservation_date">Reservation Date:</label>
                    <input type="datetime-local" id="reservation_date" name="reservation_date" class="form-control" required>
            </div>
                <button type="submit" class="btn btn-primary">Submit</button>
          
        </form>
        <form action="my_reservations.php" method="GET">
                <button type="submit" class="btn btn-secondary">View My Reservations</button>
            </form>

     
    </div>
    </div>
</body>
</html>
