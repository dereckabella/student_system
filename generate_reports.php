<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background-color: #f8f9fa; 
            padding-top: 5rem;
            transition: all 0.3s;
            z-index: 9999; 
            border: 2px solid red;
        }
        #sidebar .nav-link {
            color: #000; 
            transition: color 0.3s;
            width: 100%;
            border-bottom: 1px solid #ccc; 
            cursor: pointer; 
        }
        #sidebar .nav-link:hover {
            color: #007bff; 
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
        .nav-icon {
            margin-right: 10px; 
        }
        .rounded-circle {
            position: absolute;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%); 
            width: 100px;
            height: 100px;
            text-align: center;
        }
        .info-box {
            background-color: #fff; 
            border: 5px solid #ddd; 
            width: 50%;
            padding: 10px;     
            background-color: #D3D3D3;
            text-align: center;
        }
        .container {
            margin: 20px;
        }
        .form-group {
            width: 15%;
            padding: 10px; 
            display:inline-block;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-info">
        <div class="p-4">
            <h3 class="text-center"></h3>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="bi bi-house-door nav-icon"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="search_db.php"><i class="bi bi-search nav-icon"></i>Search</a></li>
                <li class="nav-item"><a class="nav-link" href="delete_sitin.php"><i class="bi bi-trash nav-icon"></i>Delete Sitin</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-calendar-event nav-icon"></i>Sitin</a></li>
                <li class="nav-item"><a class="nav-link" href="view_sitin_records.php"><i class="bi bi-journal nav-icon"></i>View Sitin Records</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-file-earmark-bar-graph nav-icon"></i>Generate Reports</a></li>
            </ul>
        </div>
        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>
    
    <div id="content">
        <div class="container">
            <h2>Generate Reports</h2>
            <form method="post" action="">
                <!-- Date dropdown -->
                <div class="form-group">
                    <label for="dateCriteria">Date:</label>
                    <input type="date" class="form-control" name="dateCriteria" id="dateCriteria">
                </div>
                <!-- Lab dropdown -->
                <div class="form-group">
                    <label for="labCriteria">Lab:</label>
                    <select class="form-control" name="labCriteria" id="labCriteria">
                        <option value="all">All</option>
                        <option value="524">524</option>
                        <option value="526">526</option>
                        <option value="527">527</option>
                        <option value="528">528</option>
                    </select>
                </div>
                <!-- Purpose dropdown -->
                <div class="form-group">
                    <label for="purposeCriteria">Purpose:</label>
                    <select class="form-control" name="purposeCriteria" id="purposeCriteria">
                        <option value="all">All</option>
                        <option value="C++">C++</option>
                        <option value="JAVA">JAVA</option>
                        <option value="PYTHON">PYTHON</option>
                        <option value="C#">C#</option>
                        <option value="MS WORD">MS WORD</option>
                        <option value="EXAM">EXAM</option>
                    </select>
                </div>
                <div class="form-group row">
                    <label>Search IDNO:</label>
                    <input class="mr-2" type="text"
                    id="searchInput" name="searchInput" placeholder="Enter ID Number">
                </div>
                <button type="submit" class="btn btn-primary m-3">Generate Report</button>
                
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["labCriteria"]) && isset($_POST["purposeCriteria"])) {
                // Database connection
                $hostname = "localhost"; 
                $username = "root";
                $password = "";
                $database = "student_system";

                $conn = new mysqli($hostname, $username, $password, $database);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $labCriteria = $_POST["labCriteria"];
                $purposeCriteria = $_POST["purposeCriteria"];

                // Initialize SQL query
                $sql = "SELECT r.id_number, CONCAT(r.firstName, ' ', COALESCE(r.middleName, ''), ' ', r.lastName) AS full_name, s.purpose, s.lab_number, s.time_in, s.time_out 
                FROM sitin_records s 
                INNER JOIN registration r ON s.id_number = r.id_number
                WHERE 1";

                // Add date criteria if it's not empty
                if (!empty($_POST["dateCriteria"])) {
                    $dateCriteria = $_POST["dateCriteria"];
                    $sql .= " AND DATE(s.time_in) = '$dateCriteria'";
                }

                if ($labCriteria !== "all") {
                    $sql .= " AND s.lab_number = '$labCriteria'";
                }
                
                if ($purposeCriteria !== "all") {
                    $sql .= " AND s.purpose = '$purposeCriteria'";
                }

                if (!empty($_POST["searchInput"])) {
                    $searchInput = $_POST["searchInput"];
                    $sql .= " AND r.id_number = '$searchInput'";
                }
                $sql .= " AND s.time_out IS NOT NULL";

                // Execute SQL query
                $result = $conn->query($sql);

                if ($result) {
                    if ($result->num_rows > 0) {
                        echo "<h3>Generated Report</h3>";
                        echo "<table id='reportTable' class='table table-bordered'>";
                        echo "<tr><th>ID Number</th><th>Full Name</th><th>Purpose</th><th>Lab Number</th><th>Time In</th><th>Time Out</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id_number"] . "</td>";
                            echo "<td>" . $row["full_name"] . "</td>";
                            echo "<td>" . $row["purpose"] . "</td>";
                            echo "<td>" . $row["lab_number"] . "</td>";
                            echo "<td>" . date("Y-m-d H:i:s", strtotime($row["time_in"])) . "</td>"; 
                            echo "<td>" . ($row["time_out"] ? date("Y-m-d H:i:s", strtotime($row["time_out"])) : "") . "</td>"; // Format date or empty if null
                            echo "</tr>";
                        }
                        echo "</table>";
                        // Print button
                        echo "<button onclick='printReport()' class='btn btn-primary'>Print Report</button>";
                    } else {
                        echo "<p>No records found for the selected criteria.</p>";
                    }
                } else {
                    echo "<p>Error executing query: " . $conn->error . "</p>";
                }

                // Pie chart data generation
                if (isset($_POST['generatePieChart'])) {
                    // Data for pie chart by purpose
                    $purposeLabels = [];
                    $purposeData = [];
        
                    // Data for pie chart by lab
                    $labLabels = [];
                    $labData = [];
        
                    // SQL query to get purpose counts
                    $sqlPurpose = "SELECT purpose, COUNT(*) as count FROM sitin_records WHERE time_out IS NOT NULL GROUP BY purpose";
                    $resultPurpose = $conn->query($sqlPurpose);
                    if ($resultPurpose->num_rows > 0) {
                        while($rowPurpose = $resultPurpose->fetch_assoc()) {
                            $purposeLabels[] = $rowPurpose["purpose"];
                            $purposeData[] = $rowPurpose["count"];
                        }
                    }
        
                    // SQL query to get lab counts
                    $sqlLab = "SELECT lab_number, COUNT(*) as count FROM sitin_records WHERE time_out IS NOT NULL GROUP BY lab_number";
                    $resultLab = $conn->query($sqlLab);
                    if ($resultLab->num_rows > 0) {
                        while($rowLab = $resultLab->fetch_assoc()) {
                            $labLabels[] = $rowLab["lab_number"];
                            $labData[] = $rowLab["count"];
                        }
                    }
        
                    // Pass the data to JavaScript
                  
                }

                $conn->close();
            }
            ?>

            <canvas id="purposeChart" width="400" height="400"></canvas>
            <canvas id="labChart" width="400" height="400"></canvas>

        </div>
    </div>
</div>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function() {
        var logoutConfirmed = confirm("Are you sure you want to logout?");
        if (logoutConfirmed) {
            window.location.href = 'admin_login.php'; 
        }
    });

    // FOR PRINTING
    function printReport() {
        var table = document.getElementById('reportTable');
        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write('<html><head><title>Print Report</title>');
        // Add Bootstrap CSS for table borders
        newWin.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">');
        newWin.document.write('</head><body>');
        newWin.document.write('<h1>Generated Report</h1>');
        // Add table with Bootstrap table-bordered class
        newWin.document.write('<table class="table table-bordered">' + table.innerHTML + '</table>');
        newWin.document.write('</body></html>');
        newWin.document.close();
        newWin.print();
    }

   

   
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
