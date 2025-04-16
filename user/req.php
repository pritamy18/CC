<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Handle request deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_request_id'])) {
    $request_id = $_POST['delete_request_id'];

    // Check if the request is "Pending" before deletion
    $checkSql = "SELECT status FROM blood_donor WHERE request_id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $request_id, $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        if ($row['status'] === 'Pending') {
            // Delete the request
            $deleteSql = "DELETE FROM blood_requests WHERE id = ? AND user_id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("ii", $request_id, $user_id);
            $deleteStmt->execute();
            $deleteStmt->close();
            $message = "Request deleted successfully.";
        } else {
            $message = "You cannot delete an approved request. Please fill the form to cancel the request.";
        }
    } else {
        $message = "Request not found or does not belong to you.";
    }
    $checkStmt->close();
}


// SQL query to fetch blood requests for the logged-in user
$sql = "SELECT bd.request_id,bd.hospital_id, h.hospitalName, bd.date, bd.status 
FROM blood_donor bd
JOIN hospitals h ON bd.hospital_id = h.hospital_id 
WHERE bd.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blood Requests</title>
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
            z-index: 2;
        }
        .navbar-brand {
            color: #ffffff !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-link {
            color: #ffffff !important;
            margin-right: 15px;
            transition: color 0.3s, background-color 0.3s;
        }
        .nav-link:hover {
            color: #f8f9fa !important;
            background-color: #721c24;
            border-radius: 4px;
            padding: 5px 10px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-info img {
            border-radius: 50%;
            height: 40px;
            width: 40px;
            margin-right: 10px;
            transition: transform 0.3s;
        }
        .user-info img:hover {
            transform: scale(1.1);
            cursor: pointer;
        }
        .username {
            color: #ffffff;
            margin-right: 15px;
            transition: color 0.3s, background-color 0.3s;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .username:hover {
            color: #f8f9fa;
            background-color: #721c24;
        }
        .btn-logout {
            background-color: #721c24;
            border: none;
            color: #ffffff;
            transition: background-color 0.3s;
        }
        .btn-logout:hover {
            background-color: #dc3545;
        }
        .sidebar {
            height: calc(100vh - 56px);
            position: fixed;
            top: 56px;
            left: 0;
            width: 250px;
            background-color: #721c24;
            padding-top: 20px;
            overflow-x: hidden;
            z-index: 1;
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
            font-size: 20px;
        }
        .sidebar a:hover {
            background-color: #495057;
            padding-left: 30px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .container {
            background-color: #ffffff; /* White background for content */
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table-container {
            max-height: 400px; /* Set the maximum height */
            overflow-y: auto; /* Add vertical scrolling */
        }
        .table thead th {
            background-color: #dc3545; /* Red background for table header */
            color: #ffffff; /* White text color */
        }
        .btn-danger {
            background-color: #dc3545; /* Red button color */
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333; /* Darker red on hover */
            border-color: #bd2130;
        }
    </style>
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
                    <span class="username"><?php echo $_SESSION['user_name'] ;?></span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-logout" onclick="window.location.href='../logout.php'">Logout</button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Side Navigation Bar -->
    <div class="sidebar">
    <a href="userdash.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
    <a href="user.php"><i class="fas fa-users"></i>Users List</a>
    <a href="donate.php"><i class="fas fa-hand-holding-heart"></i>Donation Form</a>
    <a href="reqform.php"><i class="fas fa-clipboard-list"></i>Request Blood Form</a>
    <a href="request.php"><i class="fas fa-receipt"></i>My Blood Request</a> 
    <a href="req.php"><i class="fas fa-clipboard-check"></i>Donation Requests</a>
    <a href="nearby.php"><i class="fas fa-map-marker-alt"></i>Nearby Hospital</a>
    <a href="camps.php"><i class="fas fa-calendar-day"></i>Blood Camps</a>
    <a href="camp.php"><i class="fas fa-calendar-plus"></i>Organize Blood Camp</a>
    <a href="requests.php"><i class="fas fa-inbox"></i>Blood Request</a> 
    </div>

    <!-- Content -->
    <div class="content">
            <!-- Page Content Goes Here -->
            <div class="container mt-5">
                <h1 class="text-center">My Blood Donation Requests</h1>
                <p class="text-center">Here you can view your blood donation requests</p>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Request ID</th>
                                <th>Location</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="requestTableBody">
                <?php
                // Display the requests
                if ($result->num_rows > 0) {
                    $serial_no = 1; // Initialize serial number
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$serial_no}</td>
                                <td>REQ{$row['request_id']}</td>
                                <td>{$row['hospitalName']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['status']}</td>
                                 <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='delete_request_id' value='{$row['request_id']}'>
                                        <button type='submit' class='btn btn-danger'>Delete</button>
                                    </form>
                                </td>  
                                </tr>";
                        $serial_no++;
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No blood requests available</td></tr>";
                }
                ?>
            </tbody>
                    </table>
                </div>
            </div>
        
<?php
// Close the database connection
$conn->close();
?>
        </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
