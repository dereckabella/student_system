<?php
session_start();

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
    header("Location: login.php");
    exit();
}


$sql_remaining = "SELECT remaining_sessions FROM registration WHERE id_number = ?";
$stmt_remaining = $conn->prepare($sql_remaining);
$stmt_remaining->bind_param("i", $_SESSION['id_number']);
$stmt_remaining->execute();
$result_remaining = $stmt_remaining->get_result();
$row_remaining = $result_remaining->fetch_assoc();

$remaining_sessions = $row_remaining['remaining_sessions'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $remaining_sessions--;

    $update_sql = "UPDATE registration SET remaining_sessions = ? WHERE id_number = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $remaining_sessions, $_SESSION['id_number']);
    $update_stmt->execute();
}


$sql_history = "SELECT s.time_in, s.time_out, s.lab_number, s.purpose 
                FROM sitin_records s 
                WHERE s.id_number = ?";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $_SESSION['id_number']);
$stmt_history->execute();
$result_history = $stmt_history->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in History</title>
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
            background-color: #f8f9fa; 
            padding-top: 5rem;
            transition: all 0.3s;
            z-index: 9999; 
            border: 2px solid red;
        }

        #sidebar .nav-link {
            color: #000;
            transition: color 0.3s;
            width: 100%; 
            border-bottom: 1px solid #ccc; 
        }

        #sidebar .nav-link:hover {
            color: #007bff; 
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
            transform: translate(-50%, -50%); 
            width: 100px;
            height: 100px;
        }

        .info-box {
            background-color: #fff; 
            border: 1px solid #ddd; /
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
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-house-door-fill"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="edit_profile.php">
                        <i class="bi bi-person-fill"></i> Edit Profile
                    </a>    
                </li>
  
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-clock-history"></i> Sit-in History
                    </a>
                </li>
            </ul>
        </div>

        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
  


    <div id="content">
        <header class="bg-primary text-white py-3 text-center rounded ">
            <h1>Sit-in History</h1>
        </header>

        <br>
        <br>
        <div class="container">
            <div class="row d-flex justify-content-start"> 
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Session</th>
                                <th>Date</th>
                                <th>Lab Number</th>
                                <th>Purpose</th>
                                <th>Time In</th>
                                <th>Time Out</th>   
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $session_number = 1;
                            while ($row_history = $result_history->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $session_number++ . "</td>";
                                echo "<td>" . date("Y-m-d", strtotime($row_history["time_in"])) . "</td>";
                                echo "<td>" . $row_history["lab_number"] . "</td>";
                                echo "<td>" . $row_history["purpose"] . "</td>";
                                echo "<td>" . date("Y-m-d H:i:s", strtotime($row_history["time_in"])) . "</td>";
                                echo "<td>" . ($row_history["time_out"] ? date("Y-m-d H:i:s", strtotime($row_history["time_out"])) : "N/A") . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
