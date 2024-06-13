<?php
// Start session (assuming session management is in place)
session_start();

// Database connection
$hostname = "localhost"; 
$username = "root";
$password = "";
$database = "student_system";

$conn = new mysqli($hostname, $username, $password, $database);

// Check if student is logged in
if (!isset($_SESSION["id_number"])) {
    // Redirect to login page or handle unauthorized access
    header("Location: login.php");
    exit;
}

// Fetch profile image
$sql = "SELECT profile_image FROM registration WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_number']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Close the statement
$stmt->close();

// Feedback form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    // Prepare and bind the INSERT statement
    $stmt = $conn->prepare("INSERT INTO feedback (id_number, message) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION["id_number"], $message);

    // Set parameters and execute
    $message = $_POST["message"];

    $stmt->execute();

    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        // Feedback submitted successfully
        $feedback_confirmation = "Thank you for your feedback!";
    } else {
        // Feedback submission failed
        $feedback_confirmation = "Failed to submit feedback. Please try again.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

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

        .info-box {
            background-color: #fff; /* White background */
            border: 1px solid #ddd; /* Subtle border */
            padding: 20px;
            margin-bottom: 20px; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: Adds subtle shadow */
        }
        form {
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
                
            </ul>
        </div>

        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>

    <div id="content">
        <h2 class="text-center mb-4">Student Feedback</h2>
        <?php if (isset($feedback_confirmation)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $feedback_confirmation; ?>
            </div>
        <?php else : ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Submit Feedback</button>
            </form>
        <?php endif; ?>
    </div>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function() {
        var logoutConfirmed = confirm("Are you sure you want to logout?");
        if (logoutConfirmed) {
            window.location.href = 'login.php'; // Redirect to logout page
        }
    });
</script>

</body>
</html>
