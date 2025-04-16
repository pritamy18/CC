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

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL query to sum total blood
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total_blood FROM blood");
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Store the total in a variable
    $total = $result['total_blood'] ?? 0; // Default to 0 if null

     // Count unique user_id from blood_donar
     $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) AS unique_donors FROM blood_donor");
     $stmt->execute();
     $donorResult = $stmt->fetch(PDO::FETCH_ASSOC);
     $uniqueDonors = $donorResult['unique_donors'] ?? 0;
 
     // Count total hospitals
     $stmt = $pdo->prepare("SELECT COUNT(*) AS total_hospitals FROM hospitals");
     $stmt->execute();
     $hospitalResult = $stmt->fetch(PDO::FETCH_ASSOC);
     $totalHospitals = $hospitalResult['total_hospitals'] ?? 0;
 
     // Select 4 donors from blood_donar
     $stmt = $pdo->prepare("SELECT * FROM blood_donor LIMIT 4");
     $stmt->execute();
     $donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
     // Select 4 requests from blood_request with name and urgency level
     $stmt = $pdo->prepare("SELECT receiver_name, urgency_level FROM blood_requests LIMIT 4");
     $stmt->execute();
     $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
 

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Management System - Dashboard</title>
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


h1 {
    color: #dc3545;
}

.card-container {
    display: flex;
    gap: 20px;
}

.card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.card h2 {
    margin: 0 0 10px;
}

.card p {
    font-size: 24px;
    margin: 0;
}
        @media (max-width: 992px) {
            .sidebar {
                width: 200px; /* Adjust sidebar width for smaller screens */
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
                    <span class="username">Admin</span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-logout" onclick="window.location.href='../index.html'">Logout</button>

                </li>
            </ul>
        </div>
    </nav>

    <!-- Side Navigation Bar -->
    <div class="side-nav">
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
                <!-- Dashboard Content -->
                <div class="container mt-3">
                    <div class="row">
                        <!-- Total Donors -->
                        <div class="col-md-4">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Donors</h5>
                                    <p class="card-text display-4"><?php echo $uniqueDonors ?></p>
                                </div>
                            </div>
                        </div>
        
                        <!-- Active Requests -->
                        <div class="col-md-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Affiliated Hospital</h5>
                                    <p class="card-text display-4"><?php echo $totalHospitals ?></p>
                                </div>
                            </div>
                        </div>
        
                        <!-- Blood Stock -->
                        <div class="col-md-4">
                            <div class="card text-white bg-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Blood Stock</h5>
                                    <p class="card-text display-4"><?php echo $total;  ?> Units</p>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="row">
                        <!-- Recent Donors -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white" style="background-color: #a71d2a;">
                                    Recent Donors
                                </div>
                                <ul class="list-group list-group-flush">
                                     <?php foreach ($donors as $donor) {
                                         echo "<li class='list-group-item'>" . htmlspecialchars($donor['name']) . "<span class='badge badge-success float-right'>".htmlspecialchars($donor['bloodGroup'])."</span></li>";
                                        }
                                   ?>
                                </ul>
                            </div>
                        </div>
        
                        <!-- Latest Requests -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white" style="background-color: #a71d2a;">
                                    Latest Requests
                                </div>
                                <ul class="list-group list-group-flush">
                                <?php  foreach ($requests as $request) {
                                         echo "<li class='list-group-item'>" . htmlspecialchars($request['receiver_name']) . "<span class='badge badge-success float-right'>".htmlspecialchars($request['urgency_level'])."</span></li>";
                                        }
                                   ?> </ul>
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
