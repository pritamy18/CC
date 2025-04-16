<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching blood requests for the logged-in user
function fetchBloodRequests($conn, $userId) {
    $sql = "SELECT request_id, id, request_to, receiver_name, receiver_contact, blood_type, quantity, urgency_level, additional_info, request_date, status, blood_id 
            FROM blood_requests 
            WHERE id= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    return $stmt->get_result();
}

$userId = $_SESSION['user_id']; // Assuming user_id is stored in session
$bloodRequests = fetchBloodRequests($conn, $userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Inventory</title>
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
    .card {
      transition: transform 0.3s ease-in-out;
    }
    .card:hover {
      transform: translateY(-10px);
    }
    .btn-request:hover, .btn-search:hover {
      background-color: #721c24;
      color: white;
    }
    .input-group input:hover {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .modal-body input:hover {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .modal-header {
      background-color: #dc3545;
      color: white;
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
    <div class="container">
    <div class="container mt-5">
                <h1 class="text-center">My Blood Requests</h1>
                <p class="text-center">Here you can view your blood requests and delete them if needed.</p>
                <div class="table-container">
                    <table class="table">
                        <thead>
                <th>Request ID</th>
                <th>Receiver Name</th>
                <th>Receiver Contact</th>
                <th>Blood Type</th>
                <th>Quantity</th>
                <th>Urgency Level</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Additional Info</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($bloodRequests->num_rows > 0): ?>
                <?php while ($row = $bloodRequests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['request_id']; ?></td>
                        <td><?php echo $row['receiver_name']; ?></td>
                        <td><?php echo $row['receiver_contact']; ?></td>
                        <td><?php echo $row['blood_type']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['urgency_level']; ?></td>
                        <td><?php echo $row['request_date']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['additional_info']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No blood requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
        </div>
        </div>
     <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>