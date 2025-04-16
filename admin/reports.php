<?php
session_start(); // Start the session
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}
// Database configuration
$host = 'localhost';
$db = 'blood_donation';
$user = 'root';
$pass = '';
// Create a new MySQLi instance
$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Handle status change request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $reportId = $_POST['report_id'];

    // Fetch the current status
    $stmt = $mysqli->prepare("SELECT status FROM reports WHERE report_id = ?");
    $stmt->bind_param("s", $reportId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Check if the status exists
    if ($row) {
        // Toggle status
        $newStatus = ($row['status'] === 'Pending') ? 'Resolved' : 'Pending';

        // Update the status in the database without affecting the date
        $updateStmt = $mysqli->prepare("UPDATE reports SET status = ? WHERE report_id = ?");
        $updateStmt->bind_param("ss", $newStatus, $reportId);
        $updateStmt->execute();
        
        // Check if update was successful
        if ($updateStmt->affected_rows > 0) {
            echo "<script>alert('Status updated successfully!')</script>";
        } else {
            echo "<script>alert('Failed to update status.')</script>";
        }
    }
    $stmt->close();
}

// SQL query to fetch data
$sql = "SELECT * FROM reports WHERE report_type='system' "; // Change to your table name

$result = $mysqli->query($sql);

// Check for query errors
if ($result === false) {
    die('Query error: ' . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #dc3545;
            z-index: 2; /* Ensure it stays above other content */
        }
        .navbar-brand {
            color: #ffffff !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-item {
            margin-left: auto;
        }
        .nav-link {
            color: #ffffff !important;
            margin-right: 15px;
            transition: color 0.3s ease, background-color 0.3s ease;
        }
        .nav-link:hover {
            color: #f8f9fa !important;
            background-color: #721c24; /* Darker shade for hover effect */
            border-radius: 4px;
            padding: 5px 10px; /* Adds some padding to make background visible */
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-info img {
            border-radius: 50%;
            height: 40px;
            width: 40px;
            object-fit: cover;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        .user-info img:hover {
            transform: scale(1.1);
            cursor: pointer;
        }
        .username {
            color: #ffffff;
            margin-right: 15px;
            transition: color 0.3s ease, background-color 0.3s ease;
            padding: 5px 10px; /* Adds some padding to make background visible */
            border-radius: 4px;
        }
        .username:hover {
            color: #f8f9fa;
            background-color: #721c24; /* Darker shade for hover effect */
        }
        .btn-logout {
            background-color: #721c24;
            border: none;
            color: #ffffff;
            transition: background-color 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #dc3545;
        }
        .sidebar {
            height: calc(100vh - 56px); /* Adjust height to fit below navbar */
            position: fixed;
            top: 56px; /* Start exactly below the navbar */
            left: 0;
            width: 250px;
            background-color: #721c24;
            overflow-x: hidden;
            z-index: 1;
            padding-top: 20px;
            transition: width 0.3s; /* Transition if you want to adjust width later */
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: #f8f9fa;
            display: flex;
            align-items: center;
            transition: background-color 0.3s, padding-left 0.3s;
        }
        .sidebar a i {
            margin-right: 15px;
            font-size: 20px; /* Adjust icon size */
        }
        .sidebar a:hover {
            background-color: #495057;
            padding-left: 30px;
        }
        .content {
            margin-left: 250px; /* Ensure content is shifted to the right to accommodate sidebar */
            padding: 20px;
        }
        .table-header {
            background-color: #dc3545; /* Bootstrap danger color */
            color: #fff;
        }
        .table th, .table td {
            text-align: center;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .table tbody tr:hover {
            background-color: #f5c6cb; /* Light pink color on hover */
        }
        .btn-status {
            cursor: pointer;
        }
        .btn-primary {
            background-color: #dc3545; /* Red background for the search button */
            border: none;
        }
        .btn-primary:hover {
            background-color: #c82333; /* Darker red on hover */
        }
        @media (max-width: 992px) {
            .sidebar {
                width: 200px; /* Adjust sidebar width for smaller screens */
            }
        }
    </style>
     <script>
        function toggleStatus(button) {
            const row = button.closest('tr');
            const reportId = row.cells[1].textContent; // Get the Report ID from the row

            // Create a form to submit the report ID
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'report_id';
            input.value = reportId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Blood Bridge</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto user-info">
                <li class="nav-item">
                    <img src="../image/p.jpg" alt="User Logo">
                </li>
                <li class="nav-item">
                    <span class="username">admin</span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-logout" onclick="window.location.href='../index.html'">Logout</button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Side Navigation Bar -->
    <div class="sidebar">
        <a href="admindash.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="manage-users.php"><i class="fas fa-users"></i>Manage Users</a>
        <a href="manage-hospitals.php"><i class="fas fa-hospital"></i>Manage Hospitals</a>
        <a href="manage-doctors.php"><i class="fas fa-user-md"></i>Manage Doctors</a>
        <a href="manage-donar.php"><i class="fa fa-hand-holding-heart"></i>Manage Donars</a>
        <a href="blood-inventory.php"><i class="fas fa-tint"></i>Blood Inventory</a>
        <a href="view-requests.php"><i class="fas fa-clipboard-list"></i>View Requests</a>
        <a href="manage-camps.php"><i class="fa fa-calendar-day"></i>Manage Camps</a> <!-- New Donor List Element -->
        <a href="reports.php"><i class="fas fa-chart-line"></i>Reports</a>
        <a href="admin-settings.php"><i class="fa fa-cogs"></i>Settings</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container">
            <!-- Page Content Goes Here -->
           
                <h1 class="text-center">Reports List</h1>
                
                <!-- Search Field and Button -->
                <div class="search-bar d-flex">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search for reports...">
                    <button class="btn btn-primary ml-2" id="searchButton">Search</button>
                </div>
                
                <!-- Reports Table -->
                <table class="table table-bordered">
            <thead class="table-header">
                <tr>
                    <th>SR No</th>
                    <th>Report ID</th>
                    <th>Status</th>
                    <th>Issue Type</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="reportsTableBody">
                <?php
                // Fetch and display data
                $srNo = 1; // Serial number for display
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$srNo}</td>
                        <td>{$row['report_id']}</td>
                        <td class='status'>{$row['status']}</td>
                        <td>{$row['issue_type']}</td>
                        <td>{$row['system_issue']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <form method='POST' action=''>
                                <input type='hidden' name='report_id' value='{$row['report_id']}'>
                                <button type='submit' class='btn btn-warning'>Change Status</button>
                            </form>
                        </td>
                    </tr>";
                    $srNo++;
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the MySQLi connection
$mysqli->close();
?>
            </div>
        
            <!-- Scripts -->
            <script>
                document.getElementById('searchButton').addEventListener('click', function() {
                    filterTable();
                });
        
                document.getElementById('searchInput').addEventListener('keyup', function(event) {
                    if (event.key === 'Enter') {
                        filterTable();
                    }
                });
        
                function filterTable() {
                    let filter = document.getElementById('searchInput').value.toLowerCase();
                    let rows = document.getElementById('reportsTableBody').getElementsByTagName('tr');
                    
                    for (let i = 0; i < rows.length; i++) {
                        let cells = rows[i].getElementsByTagName('td');
                        let found = false;
        
                        for (let j = 0; j < cells.length; j++) {
                            if (cells[j].textContent.toLowerCase().includes(filter)) {
                                found = true;
                                break;
                            }
                        }
                        
                        rows[i].style.display = found ? '' : 'none';
                    }
                }
            </script>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
