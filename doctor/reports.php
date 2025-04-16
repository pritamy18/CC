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
$dr_id = $_SESSION['doctor_name'];
// Function to generate an 8-digit alphanumeric report ID
function generateReportID() {
    return strtoupper(bin2hex(random_bytes(4))); // Generates an 8-character alphanumeric ID
}

// Fetch hospitals
$hospital_query = "SELECT hospital_id, hospitalName FROM hospitals";
$hospitals = $conn->query($hospital_query);

// Fetch users
$user_query = "SELECT user_id, fullName FROM users";
$users = $conn->query($user_query);

// Generate report ID
$report_id = generateReportID();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $report_type = $_POST['reportType'];
    $hospital_id = isset($_POST['hospitalID']) ? $_POST['hospitalID'] : null;
    $issue_type = isset($_POST['issueType']) ? $_POST['issueType'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $user_id = isset($_POST['userID']) ? $_POST['userID'] : null;
    $eligibility_status = isset($_POST['eligibilityStatus']) ? $_POST['eligibilityStatus'] : null;
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;
    $system_issue = isset($_POST['systemIssue']) ? $_POST['systemIssue'] : null;

    // Prepare and bind the insert statement
    $stmt = $conn->prepare("INSERT INTO reports (report_id, dr_id, report_type, hospital_id, issue_type, description, user_id, eligibility_status, reason, system_issue) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $report_id, $dr_id, $report_type, $hospital_id, $issue_type, $description, $user_id, $eligibility_status, $reason, $system_issue);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: reports.php"); 
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
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
        .container {
            margin-top: 20px;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .btn-submit {
            background-color: #dc3545; /* Red color for blood donation theme */
            color: #ffffff; /* White text */
            border: none; /* Remove border */
        }
        .btn-submit:hover {
            background-color: #c82333; /* Darker red for hover effect */
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
        <div class="form-container">
            <h2 class="text-center">Report Issues</h2>
            <form action="" method="post">
                <input type="hidden" name="report_id" value="<?php echo $report_id; ?>"> <!-- Pass the report ID -->
                <input type="hidden" name="dr_id" value="<?php echo $dr_id; ?>"> <!-- Pass the doctor ID -->

                <div class="form-group">
                    <label for="reportType">Report Type:</label>
                    <select class="form-control" id="reportType" name="reportType" onchange="showReportSection()">
                        <option value="" disabled selected>Select Report Type</option>
                        <option value="hospital">Hospital Issue</option>
                        <option value="user">User Eligibility</option>
                        <option value="system">System Issue</option>
                    </select>
                </div>

                <!-- Hospital Report Section -->
                <div id="hospitalReport" style="display: none;">
                    <h4>Report Hospital Issues</h4>
                    <div class="form-group">
                        <label for="hospitalID">Hospital Name:</label>
                        <select class="form-control" id="hospitalID" name="hospitalID">
                            <option value="" disabled selected>Select Hospital</option>
                            <?php while ($row = $hospitals->fetch_assoc()): ?>
                                <option value="<?php echo $row['hospital_id']; ?>"><?php echo $row['hospitalName']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="issueType">Issue Type:</label>
                        <select class="form-control" id="issueType" name="issueType">
                            <option value="" disabled selected>Select Issue Type</option>
                            <option value="emergency">Emergency</option>
                            <option value="equipment">Required Equipment</option>
                            <option value="doctors">Need More Doctors</option>
                            <option value="patients">No Patients</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Provide a detailed description"></textarea>
                    </div>
                </div>

                <!-- User Report Section -->
                <div id="userReport" style="display: none;">
                    <h4>Report Blood Donation Eligibility</h4>
                    <div class="form-group">
                        <label for="userID">User Name:</label>
                        <select class="form-control" id="userID" name="userID">
                            <option value="" disabled selected>Select User</option>
                            <?php while ($row = $users->fetch_assoc()): ?>
                                <option value="<?php echo $row['user_id']; ?>"><?php echo $row['fullName']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="eligibilityStatus">Eligibility Status:</label>
                        <select class="form-control" id="eligibilityStatus" name="eligibilityStatus">
                            <option value="" disabled selected>Select Eligibility Status</option>
                            <option value="eligible">Eligible to Donate</option>
                            <option value="not_eligible">Not Eligible to Donate</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason:</label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Provide a detailed reason if not eligible"></textarea>
                    </div>
                </div>

                <!-- System Report Section -->
                <div id="systemReport" style="display: none;">
                    <h4>Report System Issues</h4>
                    <label for="issueType">Issue Currently you are Facing:</label>
                    <input type="text" class="form-control" id="issueType" name="issueType"></input>
                    <div class="form-group">
                        <label for="systemIssue">Issue Description:</label>
                        <textarea class="form-control" id="systemIssue" name="systemIssue" rows="4" placeholder="Describe the system issue"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit btn-block">Submit Report</button>
            </form>
        </div>
    </div>
</div>
         <script>
                document.getElementById('reportType').addEventListener('change', function () {
                    var selectedType = this.value;
                    document.getElementById('hospitalReport').style.display = (selectedType === 'hospital') ? 'block' : 'none';
                    document.getElementById('userReport').style.display = (selectedType === 'user') ? 'block' : 'none';
                    document.getElementById('systemReport').style.display = (selectedType === 'system') ? 'block' : 'none';
                });
            </script>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
