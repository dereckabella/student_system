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

// Initialize variables
$message = "";
$error_message = "";
$student_info = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if ID number is provided
    if (empty($_POST['id_number'])) {
        $error_message = "Please enter a student ID number.";
    } else {
        // Retrieve ID number from the form
        $id_number = $_POST['id_number'];
        
        // Check if the student exists
        $sql = "SELECT * FROM registration WHERE id_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If student exists, retrieve student information
        if ($result->num_rows > 0) {
            $student_info = $result->fetch_assoc();
        } else {
            $error_message = "Student with ID number $id_number not found.";
        }
    }
}

// Check if form is submitted for password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $id_number = $_POST['id_number'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Hash the new password before storing it in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password in the database
        $sql_update = "UPDATE registration SET password = ? WHERE id_number = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $hashed_password, $id_number);
        
        if ($stmt_update->execute()) {
            $message = "Password reset successfully!";
        } else {
            $error_message = "Error resetting password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reset Password</title>
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
        .info-box {
            background-color: #fff; /* White background */
            border: 5px solid #ddd; /* Subtle border */
            width: 50%;
            padding: 10px;     
            background-color: #D3D3D3;
            text-align: center;
        }
    

        form {
            width: 50%; 
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            background-color: #fff;
            margin-top: 20px; 
            margin-left: 275px;
            margin-bottom: 10px; 
        }

        .card {
            width: 50%; 
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            background-color: #fff;
            margin-top: 10px; 
            margin-left: 275px;
            margin-bottom: 20px; 
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
        <h2 class="text-center mb-4">Admin Reset Password</h2>
        <?php if (!empty($message)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="id_number">Student ID Number:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="id_number" name="id_number" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary" name="search">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <?php if (!empty($student_info)) : ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Student Information</h5>
                    <p class="card-text"><strong>Name:</strong> <?php echo $student_info['firstName'] . ' ' . $student_info['lastName']; ?></p>
                    <p class="card-text"><strong>Email:</strong> <?php echo $student_info['email']; ?></p>
                </div>
            </div>
            <form method="POST">
                <input type="hidden" name="id_number" value="<?php echo $student_info['id_number']; ?>">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary" name="reset_password">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
