<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

        .wrapper {
            display: flex;
            width: 100%;
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
            border-right: 1px solid #dee2e6;
        }

        #sidebar .nav-link {
            color: #000;
            transition: color 0.3s;
            width: 100%;
            border-bottom: 1px solid #ccc;
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

        .search-container {
            width: 100%; 
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            background-color: #fff;
            margin-top: 50px; 
            margin-left: 290px; 
        }
        
        .search h2 {
            margin-bottom: 20px;
        }

        .info-box {
            margin-top: 20px;
        }

        .info-box div {
            margin-bottom: 10px;
        }

        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            color: #fff;
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Additional styles for responsiveness */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                margin-left: 0;
            }
        }

        .student-info-container { 
            background-color: #e3f2fd; /* Light blue background */
            width: 100%; 
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            margin-top: 50px; 
            margin-left: 290px; 
        }

        .info-item {
            margin-bottom: 15px; /* More spacing for readability*/
        }

        .info-item strong {
            display: inline-block;
            width: 120px; /* Consistent label width */
        }

        .info-item input[type="text"] {
            width: 50%;   /* Make input fields take up full available width */
            box-sizing: border-box; /* Ensure padding and borders are included in the width */
            margin-top: 5px; /* Add a little space above the inputs */
        }
        
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar" class="bg-info">
        <div class="p-4">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="search_db.php">Search</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Delete</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Sitin</a></li>
                <li class="nav-item"><a class="nav-link" href="view_sitin_records.php">View Sitin Records</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Generate Reports</a></li>
            </ul>
        </div>
        <button id="logoutBtn" class="btn btn-danger mb-3 position-absolute bottom-0 start-0 m-3 ">Logout</button>
    </nav>

    <div id="content">
        <div class="search-container">
            <div class="search">
                <h2 class="text-center mb-4">Search Student</h2>
                <form id="searchForm" method="post" onsubmit="return performSearch();"> 
                    <div class="input-group mb-3">
                        <input type="text" id="searchInput" name="searchInput" class="form-control" placeholder="Enter ID Number" required>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="studentInfo" class="info-box"></div>
    </div>

</div>

<script>
    function performSearch() {
        document.getElementById('studentInfo').innerHTML = '';

        var searchInput = document.getElementById('searchInput').value;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.error) {
                    document.getElementById('studentInfo').innerHTML = "<h1>" + response.error + "</h1>";
                } else {
                    var studentData = response;
                    var output = "<div class='student-info-container'>";
                    output += "<h2 class='text-center'>Student SitIn Information</h2>";
                    output += "<div class='info-item'><strong>ID Number:</strong> " + studentData.idNumber + "</div>";
                    output += "<input type='hidden' id='studentId' value='" + studentData.idNumber + "'>"; 
                    output += "<div class='info-item'><strong>Full Name:</strong> " + studentData.fullName + "</div>";
                    output += "<div class='info-item'><label for='purpose'><strong>Purpose: </strong></label>";
                    output += "<select id='purpose' name='purpose'>";
                    output += "<option value='PYTHON'>PYTHON</option>";
                    output += "<option value='C++'>C++</option>";
                    output += "<option value='JAVA'>JAVA</option>";
                    output += "<option value='C#'>C#</option>";
                    output += "<option value='MS WORD'>MS WORD</option>";
                    output += "<option value='EXAM'>EXAM</option>";
                    output += "</select></div>";
                    output += "<div class='info-item'><label for='lab'><strong>Laboratory: </strong></label>";
                    output += "<select id='lab' name='lab'>";
                    output += "<option value='526'>526</option>";
                    output += "<option value='524'>524</option>";
                    output += "<option value='528'>528</option>";
                    output += "<option value='527'>527</option>";
                    output += "</select></div>";
                    output += "<div class='info-item'><label for='remainingSession'><strong>Remaining Session: </strong></label>";
                    output += "<input type='text' id='remainingSession' name='remainingSession' value='" + studentData.remainingSessions + "' readonly></div>";
                    output += "<button type='button' onclick='sitIn()' class='btn btn-primary'>SIT IN</button>";
                    output += "</div>";
                    document.getElementById('studentInfo').innerHTML = output;
                }
            }
        };
        xhr.open('POST', 'search.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send('searchInput=' + searchInput);

        return false; // Prevent form submission
    }

    function sitIn() {
        var studentId = document.getElementById('studentId').value;
        var purpose = document.getElementById('purpose').value;
        var lab = document.getElementById('lab').value;
        var remainingSession = document.getElementById('remainingSession').value; // Retrieve remainingSession value

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Sit-in recorded successfully'); 
                    window.location.href = 'view_sitin_records.php';
                } else {
                    if (response.error === 'Cannot add another session. Ongoing session exists.') {
                        alert(response.error);
                    } else {
                        alert('Error recording sit-in: ' + response.error); 
                    }
                }
            }
        };
        xhr.open('POST', 'insert_sitin_record.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send('purpose=' + encodeURIComponent(purpose) + '&lab=' + encodeURIComponent(lab) + '&remainingSession=' + encodeURIComponent(remainingSession) + '&studentId=' + encodeURIComponent(studentId));
    }

    document.getElementById('logoutBtn').addEventListener('click', function() {
        var logoutConfirmed = confirm("Are you sure you want to logout?");
        if (logoutConfirmed) {
            window.location.href = 'login.php'; // Redirect to logout page
        }
    });
</script>

</body>
</html>