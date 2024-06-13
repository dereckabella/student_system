<?php

$hostname = "localhost"; // replace with your actual database hostname
$username = "root";
$password = "";
$database = "student_system";

// Create connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve values from the form
    $idNumber = $_POST["id_number"];
    $password = $_POST["password"];

    // SQL query to select data from the users table
    $sql = "SELECT * FROM registration WHERE id_number=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $idNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the entered password against the hashed password in the database
        if (password_verify($password, $row['password'])) {
            session_start(); // Start a session
            $_SESSION['id_number'] = $row['id_number'];
            $_SESSION['firstName'] = $row['firstName'];
            $_SESSION['lastName'] = $row['lastName'];
            
            echo "User logged in. Redirecting..."; // Add this line
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
	
}

// Close the statement and connection

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="loginstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('bgimage.jpg');
            background-size: cover; 
            background-repeat: no-repeat;
        }

        .container {
            width: 60%;
            margin: 100px auto;
            padding: 30px;
            border: none;
            border-radius: 10px;
            background-color: #e0f0ff;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
        }

        .login-form {
            width: 45%;
        }

        .login-form h1 {
            text-align: center;
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
        .form-group input[type="password"] {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #3377ff;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'Roboto', sans-serif;
            margin-bottom: 10px;
            width: 100%;
            text-transform: uppercase;
        }

        button[type="submit"]:hover {
            background-color: #2255cc;
        }

        button[type="button"] {
            background-color: #83bdff;
            color: white;
            width: 100%;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'Roboto', sans-serif;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        button[type="button"]:hover {
            background-color: #6fa1e6;
        }

        .heading {
            width: 50%;
            text-align: center;
            padding-top: 10px; /* Adjust this value as needed */
        }

        .heading img {
        width: 60%; /* Adjust image width as needed */
        max-width: 100%; /* Set maximum width to 100% */
        height: auto; /* Maintain aspect ratio */
        margin-top: 5px; /* Adjust margin-top as needed */
}
    </style>
</head>

<body>
    <div class="container">
        <div class="heading">
            <h1>SIT IN MONITORING SYSTEM</h1>
            <img src="ITCCS.PNG" alt="Your Image Description">
        </div>
        <div class="login-form">
            <h1>Login</h1>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">ID Number:</label>
                    <input type="text" name="id_number" id="id_number"> 
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required> 
                </div>
                <button type="submit">Login</button>
                <button type="button"><a href="register.php" style="text-decoration: none;">Don't have an account? Register here</a></button>
                <button type="button"><a href="admin_login.php" style="text-decoration: none;">Login as admin</a></button>
            </form>
        </div>
    </div>
</body>
</html>