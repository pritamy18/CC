<?php
session_start(); // Start the session
$user_id = $_SESSION['doctor_id'];
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}
// Database connection details
$servername = "localhost"; // Your database host
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "blood_donation"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// For Doctors
if (isset($_SESSION['doctor_id'])) {
    $stmt = $conn->prepare("SELECT fullName FROM doctors WHERE dr_id = ?");
    $stmt->bind_param("s", $_SESSION['doctor_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        $_SESSION['doctor_name'] = $doctor['fullName'];
    }
}
// Prepare the SQL statement
$sql = "SELECT * FROM doctors WHERE dr_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id); // Use "s" for string, "i" for integer, etc.

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if a user was found
if ($result->num_rows > 0) {
    // Fetch user data
    $user_data = $result->fetch_assoc();

    // Store each field in separate variables
    $username = $user_data['fullName'];
    $email = $user_data['email'];
    $full_name = $user_data['specialization'];
    $phone_number = $user_data['phone'];
    // Add more fields as necessary

} else {
    die("User not found.");
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Now you can use these variables
// Example: echo $username, $email, $full_name, $phone_number;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Home Page</title>
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
            margin-left: 200px;
            padding: 20px;
        }
        h1 {
            color: #dc3545;
        }
        .card-container {
            display: flex;
            gap: 20px;
        }
        .doctor-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .doctor-card img {
            width: 40%;
            height: auto;
            object-fit: cover; /* Ensures the image covers the area without distortion */
        }
        .doctor-details {
            width: 60%;
            padding: 20px;
        }
        .doctor-details h2 {
            margin-bottom: 20px;
        }
        .doctor-details p {
            margin-bottom: 10px;
        }
        .doctor-details .visitor-count {
            font-size: 1.25rem;
            font-weight: bold;
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
            <div class="container my-5">
                <!-- Doctor Profile Section -->
                <div class="doctor-card mb-5">
                    <img src="https://media.istockphoto.com/id/177373093/photo/indian-male-doctor.jpg?s=612x612&w=0&k=20&c=5FkfKdCYERkAg65cQtdqeO_D0JMv6vrEdPw3mX1Lkfg=" alt="Doctor Image">
                    <div class="doctor-details">
                        <h2>Dr. <?php echo $username?></h2>
                        <p><strong>Specialization:</strong> <?php echo $full_name?></p>
                        <p><strong>Experience:</strong> 15 years</p>
                        <p><strong>Email:</strong> <?php echo $email?></p>
                        <p><strong>Phone:</strong> <?php echo $phone_number?></p>
                        <p><strong>About:</strong> Dr. <?php echo $username?> is a highly experienced cardiologist with a passion for treating critical condition patients. He has worked in various leading hospitals and has a reputation for excellent patient care and innovative treatments.</p>
                        <p class="visitor-count">Total Number of Visitors: 1234</p>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
