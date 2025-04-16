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
    <title>Blood Request</title>
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
                    <span class="username"><?php echo $_SESSION['user_name'];?></span>
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
            <!-- Page Content Goes Here -->

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

// Initialize variables for the form submission
$request_id = uniqid('REQ', true); // Generate a unique alphanumeric request ID

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $donarName=$_POST['donorName'];
    $donorContact = $_POST['donorContact'];
    $bloodGroup = $_POST['bloodGroup'];
    $unitsNeeded = $_POST['unitsNeeded'];
    $requestUrgency = $_POST['requestUrgency'];
    $additionalInfo = $_POST['additionalInfo'];
    $request_to=$_POST['request_to'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO blood_requests (request_id,id,request_to,receiver_name,receiver_contact, blood_type, quantity, urgency_level, additional_info) VALUES (?,?,?,?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssssss", $request_id,$_SESSION['user_id'],$request_to, $donarName,$donorContact, $bloodGroup, $unitsNeeded, $requestUrgency, $additionalInfo);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Blood request submitted successfully. Request ID: " . htmlspecialchars($request_id) . "</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($stmt->error) . "</div>";
    }

    // Close statement
    $stmt->close();
}
?>
    <h1 class="text-center mb-4">Emergency Blood Request</h1>

    <!-- Emergency Blood Request Form -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Request Form</h5>
                    <form id="emergencyRequestForm" method="POST" action="">
                    <div class="mb-3">
                            <label for="request" class="form-label">Request To</label>
                            <select name="request_to" id="request" class="form-control">
                                <option value="">All</option>
                                <option value="hospital">Hospital</option>
                                <option value="user">User</option>
                            </select>
                            
                        </div> 
                    <div class="mb-3">
                            <label for="donorName" class="form-label">Requester Name</label>
                            <input type="text" class="form-control" id="donorName" name="donorName" required>
                        </div>   
                    <div class="mb-3">
                            <label for="donorContact" class="form-label">Requester Contact Number</label>
                            <input type="text" class="form-control" id="donorContact" name="donorContact" required>
                        </div>
                        <div class="mb-3">
                            <label for="bloodGroup">Blood Group</label>
                            <select id="bloodGroup" name="bloodGroup" class="form-control" required>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="unitsNeeded" class="form-label">Units Needed</label>
                            <input type="number" class="form-control" id="unitsNeeded" name="unitsNeeded" required>
                        </div>
                        <div class="mb-3">
                            <label for="requestUrgency" class="form-label">Urgency Level</label>
                            <select class="form-select" id="requestUrgency" name="requestUrgency" required>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="additionalInfo" class="form-label">Additional Information</label>
                            <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-submit">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

