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

// Fetch sit-in records with student details
$sql = "SELECT sitin_records.*, registration.firstName, registration.lastName FROM sitin_records INNER JOIN registration ON sitin_records.id_number = registration.id_number";
$result = $conn->query($sql);

// Fetch feedback records
$feedbackSql = "SELECT * FROM feedback";
$feedbackResult = $conn->query($feedbackSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sitin Records</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
    table {
        background-color: #e3f2fd; /* Light blue background */
    }
    th {
        background-color: #90caf9; /* Medium blue background for table headers */
    }
    .logout-btn {
        background-color: #ef9a9a; /* Light red background for logout buttons */
    }

</style>
<body>

<nav id="sidebar" class="bg-info">
    <div class="p-4">
        <h3 class="text-center"></h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="search_db.php">Search</a></li>
            <li class="nav-item"><a class="nav-link" href="delete_sitin.php">Delete</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Sitin</a></li>
            <li class="nav-item"><a class="nav-link" href="view_sitin_records.php">View Sitin Records</a></li>
            <li class="nav-item"><a class="nav-link" href="generate_reports.php">Generate Reports</a></li>
        </ul>
    </div>
    <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
</nav>

<div id="content">
    <h1 class="text-center">Sit-in Records</h1>
    
    <!-- Sit-in records table -->
    <div class="container mt-5">
        <h2 class="mb-3">Sit-in Records</h2>
        <table class="table">
            <thead>
                <tr class="text-center">
                    <th>ID Number</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Purpose</th>
                    <th>Lab</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Feedback</th> 
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='text-center'>";
                        echo "<td>" . $row['id_number'] . "</td>";
                        echo "<td>" . $row['firstName'] . "</td>";
                        echo "<td>" . $row['lastName'] . "</td>";
                        echo "<td>" . $row['purpose'] . "</td>";
                        echo "<td>" . $row['lab_number'] . "</td>";
                        echo "<td>" . $row['time_in'] . "</td>";
                        echo "<td>" . ($row['time_out'] ? $row['time_out'] : 'Not logged out') . "</td>";
                        echo "<td><button class='view-feedback-btn btn btn-primary' data-feedback='" . $row['id_number'] . "'>View Feedback</button></td>";
                        echo "<td><button class='logout-btn btn btn-danger' data-record-id='" . $row['id_number'] . "'>Logout</button></td>";
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

<!-- Feedback modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Feedback Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="feedbackMessage"></p>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // View Feedback button click event
        $('.view-feedback-btn').click(function() {
            var feedbackId = $(this).data('feedback');
            var feedbackMessage = getFeedbackMessage(feedbackId);
            $('#feedbackMessage').text(feedbackMessage);
            $('#feedbackModal').modal('show');
        });

        // Function to fetch feedback message from PHP based on feedback ID
        function getFeedbackMessage(feedbackId) {
            <?php
            if ($feedbackResult->num_rows > 0) {
                $feedbackMessages = array();
                while ($feedbackRow = $feedbackResult->fetch_assoc()) {
                    $feedbackMessages[$feedbackRow['id_number']] = $feedbackRow['message'];
                }
                echo "var feedbackMessages = " . json_encode($feedbackMessages) . ";";
                echo "return feedbackMessages[feedbackId];";
            }
            ?>
        }
    });
    document.querySelectorAll('.logout-btn').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default form submission behavior

        var recordId = event.target.dataset.recordId;

        if (confirm("Are you sure you want to log out this sit-in record?")) {
            // Perform AJAX call to update the record with time_out
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_sitin.php', true); // Change the PHP file name
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        location.reload(); // Reload to show the updated record
                    } else {
                        alert("Error updating time-out: " + response.error);
                    }
                } else {
                    alert("Error updating time-out");
                }
            };
            xhr.send('id_number=' + recordId); // Correct parameter name
        }
    });
});

</script>

</body>
</html>

<?php
$conn->close();
?>
