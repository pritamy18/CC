<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
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
        .table-container {
            background-color: #ffffff; /* White background for the table */
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-height: 400px; /* Adjust height as needed */
            overflow-y: auto; /* Add vertical scroll */
        }
        .table thead {
            background-color: #dc3545; /* Red background for the table header */
            color: #ffffff; /* White text color */
        }
        .table-hover tbody tr {
            transition: background-color 0.3s ease; /* Smooth transition */
        }
        .table-hover tbody tr:hover {
            background-color: #f5c6cb; /* Slightly darker red on hover */
        }
        .table td {
            vertical-align: middle; /* Center align text vertically */
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
    <!--backend code to fetch user list -->
    <?php
// Database configuration
$servername = "localhost"; // your server name
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "blood_donation"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {  ?>
    <!-- Content -->
    <div class="content">
        <div class="container">
            <!-- Page Content Goes Here -->
            <div class="container mt-5">
                <div class="table-container">
                    <h2 class="mb-4">User List</h2>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Blood Type</th>
                                <th>Contact</th>
                                <th>Last Donated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                        
                                echo "<tr>
                                        <td>{$row['fullName']}</td>
                                        <td>{$row['location']}</td>
                                        <td>{$row['bloodType']}</td>
                                        <td>{$row['phone']}</td>
                                        <td>{$row['lastdate']}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No users found</td></tr>";
                        }  ?>
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
// Close connection
$conn->close();
?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
