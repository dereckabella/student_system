<?php
// Database connection
$hostname = "localhost"; 
$username = "root";
$password = "";
$database = "student_system";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from POST request
$purpose = $_POST['purpose'];
$lab = $_POST['lab'];
$remainingSession = $_POST['remainingSession'];
$studentId = $_POST['studentId']; 

// Check if the student has an ongoing session or hasn't logged out yet
$checkQuery = "SELECT * FROM sitin_records WHERE id_number = ? AND time_out IS NULL";
$stmtCheck = $conn->prepare($checkQuery);
$stmtCheck->bind_param("i", $studentId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    // Student has an ongoing session or hasn't logged out yet
    echo json_encode(array('success' => false, 'error' => 'Cannot add another session. Ongoing session exists.'));
} else {
    // No ongoing session, proceed with inserting the new sit-in record
    // Prepare and execute SQL query
    $sql = "INSERT INTO sitin_records (id_number, purpose, lab_number, time_in) VALUES (?, ?, ?, CURRENT_TIMESTAMP())"; // Removed remaining_session
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $studentId, $purpose, $lab); 

    if ($stmt->execute()) {
        // Update remaining sessions count for the student
        $sql_update = "UPDATE registration SET remaining_sessions = remaining_sessions - 1 WHERE id_number = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $studentId);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            echo json_encode(array('success' => true)); 
        } else {
            echo json_encode(array('success' => false, 'error' => 'Failed to update remaining sessions count.'));
        }
    } else {
        echo json_encode(array('success' => false, 'error' => 'Error recording sit-in: ' . $conn->error)); // More detailed error reporting
    }
}

$stmt->close();
$stmt_update->close();
$stmtCheck->close();
$conn->close();
?>
