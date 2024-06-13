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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["searchInput"])) {
    $searchInput = $_POST["searchInput"];

    // Ensure the student id is treated as an integer
    $searchInput = (int) $searchInput;
    $searchQuery = "SELECT id_number, firstName, lastName, remaining_sessions FROM registration WHERE id_number = $searchInput";
    $result = $conn->query($searchQuery);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); 
        $studentData = array(
            'idNumber' => $row['id_number'],
            'fullName' => $row['firstName'] . " " . $row['lastName'],
            'remainingSessions' => $row['remaining_sessions']
            // ... other relevant fields
        );
        echo json_encode($studentData); 
    } else {
        echo json_encode(array('error' => 'Student Not Found')); 
    }

    $conn->close(); 
}
?>
