<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['id_number'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$hostname = "localhost";
$username = "root";
$password = "";
$database = "student_system";
$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $reservation_id = $_POST["reservation_id"];
        // Approve reservation
        $stmt = $conn->prepare("UPDATE reservations SET admin_status = 'approved' WHERE reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['decline'])) {
        $reservation_id = $_POST["reservation_id"];
        $decline_reason = $_POST["decline_reason"];
        // Decline reservation with reason
        $stmt = $conn->prepare("UPDATE reservations SET admin_status = 'declined', decline_reason = ? WHERE reservation_id = ?");
        $stmt->bind_param("si", $decline_reason, $reservation_id);
        $stmt->execute();
        $stmt->close();
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $reservation_id = $_POST["reservation_id"];
        
        // Approve reservation
        $stmt = $conn->prepare("UPDATE reservations SET admin_status = 'approved' WHERE reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $stmt->close();
        
        // Get the approved reservation details
        $approved_reservation_query = "SELECT * FROM reservations WHERE reservation_id = $reservation_id AND admin_status = 'approved'";
        $approved_reservation_result = $conn->query($approved_reservation_query);
        
        if ($approved_reservation_result->num_rows > 0) {
            // Fetch the reservation details
            $approved_reservation_row = $approved_reservation_result->fetch_assoc();
            
            // Insert into sitin_records table
            $insert_query = "INSERT INTO sitin_records (id_number, purpose, lab_number) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iss", $approved_reservation_row['id_number'], $approved_reservation_row['purpose'], $approved_reservation_row['lab_number']);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
    } elseif (isset($_POST['decline'])) {
        $reservation_id = $_POST["reservation_id"];
        $decline_reason = $_POST["decline_reason"];
        // Decline reservation with reason
        $stmt = $conn->prepare("UPDATE reservations SET admin_status = 'declined', decline_reason = ? WHERE reservation_id = ?");
        $stmt->bind_param("si", $decline_reason, $reservation_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reservation</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Your custom CSS styles here */
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('bgimage.jpg');
            background-size: cover; 
            background-repeat: no-repeat;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: skyblue;
        }

        table, th, td {
            border: 3px solid black;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        .btn-approve, .btn-decline {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            font-size: 1.2em;
        }

        .btn-approve:hover {
            color: #28a745;
        }

        .btn-decline:hover {
            color: #dc3545;
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
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
        }
        .nav-icon {
            margin-right: 10px; /* Add some spacing between icon and text */
        }
    </style>
</head>
<body>
    <div class="wrapper">
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
                    <li class="nav-item"><a class="nav-link" href="admin_announcement.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="reset_password.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reset Password</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_reservation.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="piechart.php"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Daily Analytics</a></li>
                </ul>
            </div>
            <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
        </nav>

        <div id="content">
            <h2>Booking Request & Approval</h2>
            <?php
            $sql = "SELECT r.*, CONCAT_WS(' ', s.firstName, s.lastName) AS full_name FROM reservations r
            INNER JOIN registration s ON r.id_number = s.id_number";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Student Name</th>";
        echo "<th>Lab Number</th>";
        echo "<th>Computer Number</th>";
        echo "<th>Purpose</th>";
        echo "<th>Reservation Date</th>";
        echo "<th>Status</th>";
        echo "<th>Action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["full_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["lab_number"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["computer_number"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["purpose"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["reservation_date"]) . "</td>";
            echo "<td>" . htmlspecialchars(ucfirst($row["admin_status"])) . "</td>";
            echo "<td>";
            echo "<form method='post' style='display:inline-block;'>";
            echo "<input type='hidden' name='reservation_id' value='" . htmlspecialchars($row["reservation_id"]) . "'>";
            echo "<button type='submit' class='btn-approve' name='approve'><i class='fas fa-check'></i></button>";
            echo "</form>";
            echo "<button type='button' class='btn-decline' data-toggle='modal' data-target='#declineModal' data-id='" . htmlspecialchars($row["reservation_id"]) . "'><i class='fas fa-times'></i></button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No reservations found.</p>";
    }

    $conn->close();
    ?>
        </div>
    </div>

    <!-- Decline Reason Modal -->
    <div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" id="declineForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="declineModalLabel">Reason for Decline</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="reservation_id" id="modalReservationId">
                        <div class="form-group">
                            <label for="decline_reason">Reason</label>
                            <textarea class="form-control" id="decline_reason" name="decline_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="decline">Decline</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#declineModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var reservationId = button.data('id');
            var modal = $(this);
            modal.find('#modalReservationId').val(reservationId);
        });


        function handleFormSubmission(form) {
            var reservationId = $(form).find('input[name="reservation_id"]').val();
            var newStatus = ''; // Determine the new status here based on the action
            $('#status_' + reservationId).text(newStatus);
        }
    </script>
</body>
</html>
