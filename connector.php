<?php
function get_db_connection() {
    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $database = "student_system"; 

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    return $conn; 
}
