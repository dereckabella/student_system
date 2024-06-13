<?php
$hostname = "localhost"; 
$username = "root";
$password = "";
$database = "student_system";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $_POST["idNumber"];
    $firstName = $_POST["firstName"];
    $middleName = $_POST["middleName"];
    $lastName = $_POST["lastName"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $yearLevel = $_POST["yearLevel"];
    $email = $_POST["email"];
    $contactNumber = $_POST["contactNumber"]; 
    $address = $_POST["address"];

    // Check if a new image is uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $profileImageTmp = $_FILES['profile_image']['tmp_name'];
        $profileImageName = $_FILES['profile_image']['name']; 
        $profileImageExt = pathinfo($profileImageName, PATHINFO_EXTENSION);
        $profileImageNewName = uniqid('', true) . '.' . $profileImageExt;
        $profileImageDestination = 'uploads/' . $profileImageNewName;

        $uploadsDirectory = 'uploads/';
        if (!file_exists($uploadsDirectory)) {
            mkdir($uploadsDirectory, 0777, true); // Create the directory if it doesn't exist
        }

        if (move_uploaded_file($profileImageTmp, $profileImageDestination)) {
            $profileImageData = file_get_contents($profileImageDestination);
        } else {
            echo "Error uploading file.";
            exit();
        }
    } else {
        // No new image uploaded, retain the existing image
        $sqlImageSelect = "SELECT profile_image FROM registration WHERE id_number = ?";
        $stmtImageSelect = $conn->prepare($sqlImageSelect);
        $stmtImageSelect->bind_param("i", $id_number);
        $stmtImageSelect->execute();
        $resultImageSelect = $stmtImageSelect->get_result();

        // Check if the user has an existing image
        if ($resultImageSelect->num_rows > 0) {
            // Fetch the existing image data
            $rowImage = $resultImageSelect->fetch_assoc();
            $profileImageData = $rowImage['profile_image'];
        } else {
            // No existing image found, set profile image data to null
            $profileImageData = null;
        }
    }

    $sql = "UPDATE registration SET firstName = ?, middleName = ?, lastName = ?, age = ?, gender = ?, yearLevel = ?, email = ?, contactNumber = ?, address = ?, profile_image = ? WHERE id_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissssssi", $firstName, $middleName, $lastName, $age, $gender, $yearLevel, $email, $contactNumber, $address, $profileImageData, $id_number);

    if ($stmt->execute()) {
        // Update session data with new information
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;

        
        echo '<script type="text/javascript"> 
                  window.confirm("Profile updated successfully!"); 
                  window.location.href = "dashboard.php"; 
              </script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_GET['id'])) {
    $id_number = $_GET['id'];
} else if (isset($_SESSION['id_number'])) {
    $id_number = $_SESSION['id_number'];
} else {
    // Handle the case where the ID is not available
    echo "Error: User ID not found.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Profile</title>
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

            .container {
                    width: 500px; 
                    margin: 50px auto;
                    padding: 30px; 
                    border: none;
                    border-radius: 10px;
                    background-color: #e0f0ff; 
                    box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1); 
                }

                fieldset {
                    margin-bottom: 20px;
                    border: none; 
                    padding: 0; 
                }

                legend {
                    font-weight: bold;
                    font-size: 1.2em;
                    padding: 10px 20px;
                    background-color: #b3daff; 
                    border-radius: 5px; 
                    color: #333; 
                }

                .form-group {
                    margin-bottom: 15px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    color: #444; 
                }

                .form-group input[type="text"],
                .form-group input[type="email"],
                .form-group input[type="password"],
                .form-group input[type="number"],
                .form-group input[type="tel"],
                .form-group textarea,
                .form-group select {
                    width: 95%; /* Ensures full width */
                    padding: 10px; 
                    border: 1px solid #ccc;
                    border-radius: 5px;
                } 

                button[type="submit"] {
                    background-color: #3377ff;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.3s ease; 
                }

                button[type="submit"]:hover {
                    background-color: #2255cc; 
                }

                .nav-item {
                    font-size: 13px;
                    font-weight: bold;
                    text-transform: UPPERCASE;
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
        
        </style>
        </head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
        <nav id="sidebar" class="bg-info">
        <div class="m-5"> <?php
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
                            echo '<img src="defaultimage.jpg" alt="" class="rounded-circle border border-3 text-center">';
                        }
                        ?>
                        </div>
            <div class="p-4">
            <h3 class="text-center mt-4"><?php echo $_SESSION['firstName'] . ' ' . $_SESSION['lastName']; ?></h3>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Edit Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Sitin</a></li>
                <li class="nav-item"><a class="nav-link" href="#">View Remaining Sessions</a></li>
            </ul>
            </div>
        </nav>


    <div id="content">
 
    <br>
    <br>
    <div class="container">
      <h2>Edit Profile</h2>
      <form action="edit_profile.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="idNumber" value="<?php echo $id_number; ?>"> 
            <fieldset>

            <div class="form-group">
                <label for="profile_image">Profile Image:</label>
                <input type="file" id="profile_image" name="profile_image">
            </div>    

                <legend>Personal Information</legend>
				
                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>

                <div class="form-group">
                    <label for="middleName">Middle Name:</label>
                    <input type="text" id="middleName" name="middleName">
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>  

                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" required>
                </div>  

                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="yearLevel">Year Level:</label>
                    <input type="number" id="yearLevel" name="yearLevel" required>
                </div>
            </fieldset>

            <fieldset>
                <legend>Contact Information</legend>
                <div class="form-group">
                    <label for="contactNumber">Contact Number:</label>
                    <input type="tel" id="contactNumber" name="contactNumber" required>
                </div> 

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div> 

                 <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" required></textarea>
                </div> 
            </fieldset>

            
            <button type="submit">Save Changes</button>
            
        </form>
    </div>
  </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle sidebar
  document.getElementById('sidebarCollapse').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
  });
</script>
</body>
</html>