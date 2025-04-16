<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
} 
$hospital_id = $_SESSION['hospital_id']; // Assuming the hospital ID is stored in session
// Database configuration
$host = 'localhost';
$dbname = 'blood_donation';
$username = 'root';
$password = '';

// Create connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// For Hospitals
if (isset($_SESSION['hospital_id'])) {
    $stmt = $pdo->prepare("SELECT hospitalName FROM hospitals WHERE hospital_id = :hospital_id");
    $stmt->bindParam(':hospital_id', $_SESSION['hospital_id'], PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $hospital = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['hospital_name'] = $hospital['hospitalName'];
    }
}
    // Query to select the total count of each blood type
    $sql = "SELECT type, SUM(amount) AS total FROM blood WHERE hospital_id = '$hospital_id'  GROUP BY type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Initialize an array to store the total for each blood type
    $blood_totals = [];

    // Fetch results and store in the blood_totals array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $blood_totals[$row['type']] = $row['total'];
    }

    // Store overall total
    $overall_total = array_sum($blood_totals);

    

    // Close the connection
$pdo = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard</title>
     <!-- Bootstrap CSS -->
     <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
   
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        .scroll {
            height: calc(94vh - 60px); /* Adjust height to fit within viewport */
            overflow-y: auto;
        }
        .card {
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .card-header{
            background-color: #dc3545;
        }
        .header {
            margin-bottom: 20px;
            
        }
        .dashboard-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .dashboard-item {
            flex: 1 1 calc(50% - 20px);
            border-radius: 35px;
        }
        @media (max-width: 768px) {
            .dashboard-item {
                flex: 1 1 100%;
            }
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
                    <span class="username"><?php echo $_SESSION['hospital_name'] ?></span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-logout" onclick="window.location.href='../index.html'">Logout</button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Side Navigation Bar -->
    <div class="sidebar">
        <a href="hospitaldash.php"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a>
        <a href="blood-inventory.php"><i class="fas fa-tint"></i><span> Blood Entry</span></a>
        <a href="donors.php"><i class="fas fa-users"></i><span>User List</span></a>
        <a href="doctors.php"><i class="fas fa-user-md"></i><span>Manage Doctors</span></a>
        <a href="camps.php"><i class="fas fa-calendar-alt"></i><span>Manage Camps</span></a>
        <a href="donor-requests.php"><i class="fas fa-hand-holding-heart"></i><span> Donor Requests</span></a>
        <a href="blood-requests.php"><i class="fas fa-hand-holding-medical"></i><span> Blood Requests</span></a>
        <a href="reports.php"><i class="fas fa-chart-line"></i><span>Manage Reports</span></a>
    </div>

    <!-- Content -->
    <div class="scroll">
    <div class="content">
            <!-- Page Content Goes Here -->
    <!-- Dashboard Header -->
    <div class="header">
        <h1>Hospital Dashboard</h1>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Blood Inventory Overview -->
        <div class="dashboard-item">
            <div class="card">
                <div class="card-header">
                    <h4>Blood Inventory Overview</h4>
                </div>
                <div class="card-body">
                    <p>Total Blood Units: <strong><?php echo $overall_total; ?></strong></p>
                    <p>Available Blood Types:</p>
                    <ul>
                        <?php
 foreach ($blood_totals as $type => $total) {
    ?><li><?php echo "$type" ?><strong>: <?php echo "$total" ?> units</strong></li><?php
      
 }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Upcoming Donations -->
        <div class="dashboard-item">
            <div class="card">
                <div class="card-header">
                    <h4>Upcoming Donations</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-09-15</td>
                                <td>10:00 AM</td>
                                <td>Hospital Lobby</td>
                            </tr>
                            <tr>
                                <td>2024-09-20</td>
                                <td>2:00 PM</td>
                                <td>Community Center</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Patient Requests -->
        <div class="dashboard-item">
            <div class="card">
                <div class="card-header">
                    <h4>Patient Requests</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Blood Type</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Soham</td>
                                <td>O+</td>
                                <td>2 units</td>
                                <td>Fulfilled</td>
                            </tr>
                            <tr>
                                <td>Vishal Yadav</td>
                                <td>A-</td>
                                <td>1 unit</td>
                                <td>Pending</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="dashboard-item">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Activities</h4>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Donor registration completed for vishal Yadav.</li>
                        <li>Blood drive scheduled at Community Center on 2024-09-20.</li>
                        <li>New patient request added for Soham.</li>
                        <!-- Add more activities as needed -->
                    </ul>
                </div>
            </div>
        </div>

        <!-- Alerts and Notifications -->
        <div class="dashboard-item">
            <div class="card">
                <div class="card-header">
                    <h4>Alerts and Notifications</h4>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Urgent need for O- blood type.</li>
                        <li>Upcoming blood drive scheduled for 2024-09-15.</li>
                        <li>System maintenance planned for 2024-09-18 from 2:00 AM to 4:00 AM.</li>
                        <!-- Add more notifications as needed -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
