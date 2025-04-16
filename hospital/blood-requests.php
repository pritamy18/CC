<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
} 
  // Database configuration
  $host = 'localhost';
  $dbname = 'blood_donation';
  $username = 'root';
  $password = '';

  // Create connection
  $conn = new mysqli($host, $username, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
// Update status function
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $status = 'Approved';
    } elseif ($action == 'reject') {
        $status = 'Rejected';
    }

    $sql = "UPDATE blood_requests SET status = '$status' WHERE request_id = '$request_id'";
    $conn->query($sql);
}

// Fetching blood requests
$sql = "SELECT * FROM blood_requests";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urgent Blood Requests</title>
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
            text-align: center;
            color: #d9534f;
        }

        /* Table styles */
        table {
            width: 100%;
            max-width: 1200px;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ccc;
            text-align: center;
            overflow-y: auto;
            max-height: 550px;
            display: block;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            width:300px;
        }

        th {
            background-color: #d9534f;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Button styles */
        .action-btn {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .approve-btn {
            background-color: #5cb85c;
        }

        .reject-btn {
            background-color: #d9534f;
        }

        .no-action {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* Hide actions if status is approved */
        .hide-actions {
            display: none;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-body {
            margin-top: 20px;
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
    <div class="content">
        <div class="container">
            <!-- Page Content Goes Here -->
            <h1>Urgent Blood Requests</h1>

            <!-- Main Table for Urgent Blood Requests -->
            <table>
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Request ID</th>
                        <th>Blood Type</th>
                        <th>Quantity</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $num=0;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $num++;
                        echo "<tr>";
                        echo "<td>$num</td>";
                        echo "<td>{$row['request_id']}</td>";
                        echo "<td>{$row['blood_type']}</td>";
                        echo "<td>{$row['quantity']}</td>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['status']}</td>";
                        echo "<td>";
                        
                        if ($row['status'] == 'Pending') {
                            echo "<form method='POST' style='display:inline;'>
                                    <input type='hidden' name='request_id' value='{$row['request_id']}'>
                                    <button type='submit' name='action' value='approve' class='action-btn approve-btn'>Approve</button>
                                  </form>";
                            echo "<form method='POST' style='display:inline;'>
                                    <input type='hidden' name='request_id' value='{$row['request_id']}'>
                                    <button type='submit' name='action' value='reject' class='action-btn reject-btn'>Reject</button>
                                  </form>";
                        } else {
                            echo "<span class='no-action'>No Action Available</span>";
                        }
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No urgent blood requests available.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        
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