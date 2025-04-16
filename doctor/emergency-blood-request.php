<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.html'); // Redirect if not logged in
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

// Fetching blood requests from the database
function fetchBloodRequests($conn) {
    $sql = "SELECT * FROM blood_requests"; // Adjust as necessary
    return $conn->query($sql);
}

// Approve blood request and update blood inventory
function approveBloodRequest($conn, $requestId) {
    // Fetch request details
    $requestQuery = "SELECT blood_type, quantity FROM blood_requests WHERE request_id = ?";
    $stmt = $conn->prepare($requestQuery);
    $stmt->bind_param("s", $requestId);
    $stmt->execute();
    $requestDetails = $stmt->get_result()->fetch_assoc();

    // Check blood availability and update the blood table
    if ($requestDetails) {
        $bloodGroup = $requestDetails['blood_type'];
        $requestedAmount = $requestDetails['quantity'];
        $today = date('Y-m-d');

        // First check the available amount
        $checkQuery = "SELECT amount FROM blood WHERE type = ? AND amount >= ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("si", $bloodGroup, $requestedAmount);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // If sufficient blood is available, proceed with the update
            $updateQuery = "UPDATE blood SET amount = amount - ?, brequest_id = ?, exitdate = ?, status = 'Not Available' WHERE type = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("iss", $requestedAmount, $requestId, $today);
            
            if ($updateStmt->execute()) {
                if ($updateStmt->affected_rows > 0) {
                    return true; // Successful update
                } else {
                    error_log("No rows affected for Request ID: $requestId. Check blood availability or the request ID.");
                }
            } else {
                error_log("Update error: " . $updateStmt->error);
            }
        } else {
            error_log("Not enough blood available for Request ID: $requestId.");
        }
    } else {
        error_log("No request details found for ID: $requestId");
    }
    return false; // Return false if no details found or an error occurred
}

// Handle form submission for approval
$success = false; // Initialize success variable
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_request_id'])) {
    $requestId = $_POST['approve_request_id'];
    $success = approveBloodRequest($conn, $requestId);
}

$bloodRequests = fetchBloodRequests($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Records Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #dc3545;
        }
        .navbar-brand, .nav-link, .username {
            color: #ffffff !important;
        }
        .navbar-collapse {
            display: flex;
            justify-content: flex-end;
        }
        .nav-link:hover, .username:hover {
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
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .user-info img:hover {
            transform: scale(1.1);
        }
        .btn-logout {
            background-color: #721c24;
            border: none;
            color: #ffffff;
            margin-left: 15px;
            transition: background-color 0.3s ease;
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
            transition: width 0.3s;
        }
        .sidebar a {
            padding: 15px;
            font-size: 18px;
            color: #f8f9fa;
            display: flex;
            align-items: center;
            transition: background-color 0.3s, padding-left 0.3s;
        }
        .sidebar a i {
            margin-right: 15px;
        }
        .sidebar a:hover {
            background-color: #495057;
            padding-left: 30px;
        }
        .content {
            margin-left: 250px;
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
            margin-bottom: 10px;
        }
        .card p {
            font-size: 24px;
        }
        .table thead th {
            background-color: #dc3545;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #e2e6ea;
            cursor: pointer;
        }
        .btn-primary, .btn-danger {
            margin-right: 5px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .request-id-link {
            cursor: pointer;
            color: #dc3545;
            text-decoration: underline;
        }
        .request-id-link:hover {
            color: #c82333;
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
                    <span class="username"><?php echo $_SESSION['doctor_name'] ;?></span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-logout" onclick="window.location.href='../logout.php'">Logout</button>
                </li>
            </ul>
        </div>
    </nav>


    <!-- Side Navigation Bar -->
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="blood-inventory.php"><i class="fas fa-tint"></i>Add Blood</a>
        <a href="donor-request.php"><i class="fas fa-user-plus"></i>Donor Requests</a>
        <a href="emergency-blood-request.php"><i class="fas fa-exclamation-circle"></i>Blood Requests</a>
        <a href="donors-record.php"><i class="fas fa-users"></i>Donors Record</a>
        <a href="camp-schedule.php"><i class="fas fa-calendar-day"></i>Camps Detail</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i>Report Issue</a>
    </div>
    <!-- Content -->
    <div class="content">
        <div class="container">
            <!-- Page Content Goes Here -->
            <div class="container mt-5">
        <h1 class="text-center mb-4">Blood Requests</h1>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Urgency Level</th>
                    <th>Blood Group</th>
                    <th>Requested Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                    <?php if ($bloodRequests->num_rows > 0): ?>
                        <?php while ($row = $bloodRequests->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['urgency_level']; ?></td>
                                <td><?php echo $row['blood_type']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="approve_request_id" value="<?php echo $row['request_id']; ?>">
                                        <button type="submit" class="btn btn-primary">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No blood requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>