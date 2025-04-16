<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "blood_donation";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $message ="";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user ID from session
    $user_id = $_SESSION['user_id']; // Ensure user_id is set in session

    // Get form data
    $org_name = $_POST['orgName'] ?? '';
    $location = $_POST['location'];
    $date = $_POST['date'];
    $participants = $_POST['participants'];
    $details = $_POST['details'] ?? '';
    $contact_phone = $_POST['contact'] ?? '';
    $contact_email = $_POST['contactEmail'] ?? '';
    $hospital_name = $_POST['hospital'] ?? ''; // Additional field for hospital name
    function generateUniqueCode() {
        // Generate a random alphabet character (A-Z)
        $alphabet = chr(rand(65, 90)); // ASCII range for A-Z is 65 to 90
        
        // Generate a random 10-digit number
        $number = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        
        // Combine them into a single code
        $uniqueCode = $alphabet . $number;
    
        return $uniqueCode;
    }
    
    // Example usage:
    $code = generateUniqueCode();
    // Prepare SQL statement
    $sql = "INSERT INTO blood_donation_camps (camp_id,user_id, org_name, location, date, participants, details, contact_phone, contact_email, hospital_name) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssssss",$code, $user_id, $org_name, $location, $date, $participants, $details, $contact_phone, $contact_email, $hospital_name);

        // Execute statement
        if ($stmt->execute()) {
            $message = "Request submitted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Error preparing statement: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Blood Donation Camp</title>
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
        .container {
            background-color: #ffffff; /* White background for content */
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #dc3545; /* Red button color */
            border-color: #dc3545;
        }
        .btn-primary:hover {
            background-color: #c82333; /* Darker red on hover */
            border-color: #bd2130;
        }
        .form-section {
            margin-bottom: 20px;
        }
        .form-section h3 {
            margin-bottom: 15px;
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
                    <span class="username"><?php $_SESSION['user_name'] ;?></span>
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
    <div class="container mt-5">
    <h1 class="text-center">Request a Blood Donation Camp</h1>
    <p class="text-center">Fill out the form below to request a blood donation camp in your area.</p>
    
    <?php if (!empty($message)): ?>
        <div id="confirmation-message" class="alert alert-success text-center">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-section">
            <h3>Camp Request Details</h3>
            <div class="form-group">
                <label for="orgName">Organization/Community Name (if applicable)</label>
                <input type="text" class="form-control" id="orgName" name="orgName" placeholder="Enter the name of your organization or community">
            </div>
            <div class="form-group">
                <label for="location">Preferred Location</label>
                <input type="text" class="form-control" id="location" name="location" placeholder="Enter the area or address" required>
            </div>
            <div class="form-group">
                <label for="date">Preferred Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="participants">Estimated Number of Participants</label>
                <input type="number" class="form-control" id="participants" name="participants" placeholder="Enter the estimated number of participants" required>
            </div>
            <div class="form-group">
                <label for="details">Additional Details</label>
                <textarea class="form-control" id="details" name="details" rows="3" placeholder="Any additional information or special requests"></textarea>
            </div>
            <div class="form-group">
                <label for="contact">Contact Person's Phone Number</label>
                <input type="tel" class="form-control" id="contact" name="contact" required>
            </div>
            <div class="form-group">
                <label for="contactEmail">Contact Person's Email Address</label>
                <input type="email" class="form-control" id="contactEmail" name="contactEmail" required>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="orgCheck" name="orgCheck">
                <label class="form-check-label" for="orgCheck">This request is on behalf of an organization or community.</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>

<script>
    // Function to hide the confirmation message after 10 seconds
    window.onload = function() {
        const message = document.getElementById('confirmation-message');
        if (message) {
            setTimeout(() => {
                message.style.display = 'none';
            }, 10000); // 10000 milliseconds = 10 seconds
        }
    };
</script>

        
     </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
