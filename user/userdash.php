<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}

// Database connection parameters
$servername = "localhost"; // your server
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "blood_donation"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// For Users
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT fullName FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_name'] = $user['fullName'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
        .dashboard-container {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card-header {
            background-color: #dc3545;
            color: #ffffff;
        }
        .card-body {
            background-color: #ffffff;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #dc3545;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .profile-summary, .statistics, .recent-activities, .appointment-list {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .activity-item, .appointment-item {
            border-bottom: 1px solid #e9ecef;
            padding: 10px 0;
        }
        .activity-item:last-child, .appointment-item:last-child {
            border-bottom: none;
        }
        .btn {
            border-radius: 4px;
        }
        .scroll {
            height: calc(94vh - 60px); /* Adjust height to fit within viewport */
            overflow-y: auto;
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
    <div class="scroll">
    <div class="content">
        
        <div class="container">
            <!-- Page Content Goes Here -->
              
    <div class="dashboard-container">
       
            <div class="row">
                <!-- Profile Summary -->
                <div class="col-md-4">
                    <div class="card profile-summary">
                        <div class="card-header">
                            <h5 class="card-title">Profile Summary</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong>Username</p>
                            <p><strong>Blood Type:</strong> O+</p>
                            <p><strong>Last Donation:</strong> January 15, 2024</p>
                            <p><strong>Donation Frequency:</strong> Every 3 months</p>
                        </div>
                    </div>
                </div>
                <!-- Recent Activities -->
                <div class="col-md-4">
                    <div class="card recent-activities">
                        <div class="card-header">
                            <h5 class="card-title">Recent Activities</h5>
                        </div>
                        <div class="card-body">
                            <div class="activity-item">
                                <p>Donated blood on February 20, 2024</p>
                            </div>
                            <div class="activity-item">
                                <p>Requested a blood donation camp on March 10, 2024</p>
                            </div>
                            <!-- Add more activities here -->
                        </div>
                    </div>
                </div>
                <!-- Upcoming Appointments -->
                <div class="col-md-4">
                    <div class="card appointment-list">
                        <div class="card-header">
                            <h5 class="card-title">Upcoming Appointments</h5>
                        </div>
                        <div class="card-body">
                            <div class="appointment-item">
                                <p>Blood donation camp on March 25, 2024 at XYZ Hospital</p>
                            </div>
                            <div class="appointment-item">
                                <p>Blood donation camp on April 15, 2024 at ABC Clinic</p>
                            </div>
                            <!-- Add more appointments here -->
                        </div>
                    </div>
                </div>
                <!-- Statistics -->
                <div class="col-md-12">
                    <div class="card statistics">
                        <div class="card-header">
                            <h5 class="card-title">Statistics</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Donations:</strong> 15</p>
                            <p><strong>Total Camps Requested:</strong> 3</p>
                            <p><strong>Upcoming Appointments:</strong> 2</p>
                            <p><strong>Next Scheduled Donation:</strong> June 15, 2024</p>
                            <p><strong>Total Camps Attended:</strong> 5</p>
                            <p><strong>Total Blood Requests Made:</strong> 4</p>
                            <p><strong>Last Donor Campaign:</strong> March 10, 2024</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
