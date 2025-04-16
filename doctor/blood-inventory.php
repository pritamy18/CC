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

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch blood records
$sql = "SELECT * FROM blood";
$result = $conn->query($sql);

// Fetch Donor requests
$status1 = '<span class="badge badge-warning">Pending</span>';
$status2 = '<span class="badge badge-success">Approved</span>';
$status3 = 'span class="badge badge-danger">Rejected</span>';
$sql1 = "SELECT request_id FROM blood_donor Where status='$status2' ";
$result1 = $conn->query($sql1);

// Fetch user IDs for the dropdown
$userSql = "SELECT user_id FROM users"; 
$userResult = $conn->query($userSql);
 
$userSq2 = "SELECT hospital_id,hospitalName FROM hospitals"; 
$result2 = $conn->query($userSq2);
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $bloodType = $_POST['bloodType'];
    $inventory = $_POST['inventory'];
    $hbLevel = $_POST['hbLevel'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO `blood`(`blood_id`, `request_id`, `hospital_id`, `dr_id`, `status`, `type`, `amount`, `entrydate`) VALUES (?,?,?,?,?, ?, ?, ?)");
    $stmt->bind_param("ssis", $userId, $bloodType, $inventory, $hbLevel);

    if ($stmt->execute()) {
        echo "<script>alert('Blood record added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

$conn->close();
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
        <div class="container my-5">
            <h1 class="text-center mb-4">Blood Records</h1>
        
            <!-- Button to trigger modal to add blood record -->
            <div class="text-end mb-3">
                <button class="btn btn-danger btn-add" data-bs-toggle="modal" data-bs-target="#addBloodModal">Add Blood Record</button>
            </div>
        
            <!-- Blood Records Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Available Blood Records</h5>
                    <div class="table-responsive scrollable-table">
                        <table class="table table-hover">
                            <thead class="table-danger">
                                <tr>
                                    <th>Blood ID</th>
                                    <th>Blood Type</th>
                                    <th>Inventory (Units)</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['blood_id']; ?></td>
                <td><?php echo $row['type']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td><?php echo $row['entrydate']; ?></td>
            </tr>
        <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Blood Record Modal -->
        <div class="modal fade" id="addBloodModal" tabindex="-1" aria-labelledby="addBloodModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBloodModalLabel">Add Blood Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addBloodForm" action="addblood.php" method="post">
                            <div class="mb-3">
                                <label for="userId" class="form-label">Donar ID</label>
                                <select class="form-select" id="userId" name="request_id">
                                <option selected disabled>Select Blood Type</option>
                                <?php while ($row = $result1->fetch_assoc()): ?>
                                    <option value="<?php echo $row['request_id']?>"><?php echo $row['request_id']; ?></option>
                            <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="hospitalId" class="form-label">Hospital Name</label>
                                <select class="form-select" id="userId" name="hospital_id">
                                <option selected disabled>Select Hospital Name</option>
                                <?php while ($row = $result2->fetch_assoc()): ?>
                                    <option value="<?php echo $row['hospital_id'];  ?>"><?php echo $row['hospitalName']; ?></option>
                            <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="inventory" class="form-label">Inventory (Units)</label>
                                <input type="number" class="form-control" id="inventory" name="amount" min="1" required>
                            </div>
                            <button type="submit" class="btn btn-danger">Add Blood Record</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</body>
</html>
