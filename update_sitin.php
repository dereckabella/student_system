<?php
// Check if the id_number parameter is set
if(isset($_POST['id_number'])) {
    // Database connection
    $hostname = "localhost"; 
    $username = "root";
    $password = "";
    $database = "student_system";

    $conn = new mysqli($hostname, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind parameters
    $stmt = $conn->prepare("UPDATE sitin_records SET time_out = NOW() WHERE id_number = ?");
    $stmt->bind_param("i", $_POST['id_number']);

    // Execute the statement
    if ($stmt->execute()) {
        // Decrement remaining_sessions
        $decrementStmt = $conn->prepare("UPDATE registration SET remaining_sessions = remaining_sessions - 1 WHERE id_number = ?");
        $decrementStmt->bind_param("i", $_POST['id_number']);
        $decrementStmt->execute();

        // Check if decrement was successful
        if ($decrementStmt->affected_rows > 0) {
            $response = array('success' => true, 'message' => 'Time-out updated and remaining_sessions decremented successfully.');
        } else {
            $response = array('success' => false, 'error' => 'Error: Failed to decrement remaining_sessions.');
        }
    } else {
        $response = array('success' => false, 'error' => 'Error: Failed to update time-out.');
    }

    // Close connections
    $stmt->close();
    $decrementStmt->close();
    $conn->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Return error if id_number parameter is not set
    $response = array('success' => false, 'error' => 'Error: id_number parameter is not set.');
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>