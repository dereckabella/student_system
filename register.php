<?php
$hostname = "localhost"; // replace with your actual database hostname
$username = "root";
$password = "";
$database = "student_system";


$conn = new mysqli($hostname, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve values from the form
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
    $password = $_POST["password"];

    $check_query = "SELECT * FROM registration WHERE id_number = '$id_number'";
    $result = $conn->query($check_query);
    if ($result->num_rows > 0) {
        echo "ID number already exists. Please choose a different one.";
    } else {
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
       
        $sql = "INSERT INTO registration (id_number, firstName, middleName, lastName, age, gender, yearLevel, email, contactNumber, address, password) 
                VALUES ('$id_number', '$firstName', '$middleName', '$lastName', $age, '$gender', '$yearLevel', '$email', '$contactNumber', '$address', '$hashedPassword')";
        if ($conn->query($sql) === TRUE) {
            echo '<script type="text/javascript">
                    var confirmed = window.confirm("Registration successful. Do you want to proceed to the login page?");
                    if (confirmed) {
                        window.location.href = "login.php";
                    } else {
                        window.location.href = "register.php";
                    }
                  </script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css">
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


	</style>
</head>


<body>
    <div class="container">
        <h1>Registration Form</h1>

        <form action="register.php" method="post">
            <fieldset>
                <legend>Personal Information</legend>
				 <div class="form-group">
                    <label for="idNumber">ID Number:</label>
                    <input type="text" id="idNumber" name="idNumber" required>
                </div> 
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

            <fieldset>
                <legend>Account Information</legend>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>  

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div> 
            </fieldset>

            <button type="submit">Register</button>
            
        </form>
    </div>
</body>
</html>