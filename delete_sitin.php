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

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_number"])) {
    $idNumber = $_POST["id_number"];

    // Fetch data for confirmation before deleting
    $query = "SELECT firstName, lastName FROM registration WHERE id_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch data from the first row
        $row = $result->fetch_assoc();
        $firstName = $row["firstName"];
        $lastName = $row["lastName"];

        // Perform the delete operation
        $deleteQuery = "DELETE FROM sitin_records WHERE id_number = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $idNumber);

        if ($stmt->execute()) {
            $message = "Sit-in record for $firstName $lastName deleted successfully.";
        } else {
            $message = "Error deleting record: " . $stmt->error;
        }
    } else {
        $message = "No records found for provided ID number.";
    }

    $stmt->close(); // Close statement after deleting
}

// Fetch and display sit-in records
$sitin_query = "SELECT sitin_records.id_number, registration.firstName, registration.lastName, sitin_records.lab_number, sitin_records.time_in, sitin_records.time_out FROM sitin_records INNER JOIN registration ON sitin_records.id_number = registration.id_number";
$sitin_result = $conn->query($sitin_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Sit-in Record</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
            text-transform: UPPERCASE;
            padding: 10px;
        }

        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            color: #fff;
            background-color: #c82333;
            border-color: #bd2130;
        }

        #content {
                transition: all 0.3s;
                margin-left: 250px;
                padding: 10px;
            }
    </style>
</head>

<body>


    <div class="container">
        <nav id="sidebar" class="bg-info">
            <div class="p-4">
                <h3 class="text-center"></h3>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="search_db.php">Search</a></li>
                    <li class="nav-item"><a class="nav-link" href="delete_sitin.php">Delete Sitin</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Sitin</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_sitin_records.php">View Sitin Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Generate Reports</a></li>
                </ul>
            </div>
            <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
        </nav>
    
    

       <div id="content">
       <h1 class="text-center mt-5">Delete Sit-in Record</h1>
        <?php if (!empty($message)) : ?>
            <div class="alert alert-primary mt-3" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-11">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Lab Number</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($sitin_result->num_rows > 0) {
                            while ($row = $sitin_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_number'] . "</td>";
                                echo "<td>" . $row['firstName'] . "</td>";
                                echo "<td>" . $row['lastName'] . "</td>";
                                echo "<td>" . $row['lab_number'] . "</td>";
                                echo "<td>" . $row['time_in'] . "</td>";
                                echo "<td>" . ($row['time_out'] ? $row['time_out'] : 'Not logged out') . "</td>";
                                echo "<td><form method='post'><input type='hidden' name='id_number' value='" . $row['id_number'] . "'><button type='submit' class='btn btn-danger'>Delete</button></form></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
       </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
