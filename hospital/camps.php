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
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Assuming you have a session and the hospital_id is stored in it
$hospital_id = $_SESSION['hospital_id']; // Change according to your session handling

// Fetch camp data
$stmt = $pdo->prepare("SELECT * FROM blood_donation_camps WHERE hospital_id = ?");
$stmt->execute([$hospital_id]);
$camps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch doctors related to the hospital
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE hospital_id = ?");
$stmt->execute([$hospital_id]);
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Form submission
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $camp_id = $_POST['camp_id']; // Fetch the camp_id from the form
    $date = $_POST['date'];
    $location = $_POST['location'];
    $doctor_ids = isset($_POST['doctors']) ? $_POST['doctors'] : [];
    $assigned_doctors = implode(',', $doctor_ids); // Convert array to comma-separated string
    

// Then use $assigned_doctors in your SQL query
$stmt = $pdo->prepare("UPDATE blood_donation_camps SET status='approved' ,hospital_id=?, scheduled = ?, location = ?, assigned_doctors = ? WHERE camp_id = ?");
if ($stmt->execute([$hospital_id,$date, $location, $assigned_doctors, $camp_id])) {
    // Handle success
} else {
    echo "Error updating record: " . $stmt->errorInfo()[2];
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camp Request Details</title>
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
        .modal-header {
            background-color: #dc3545; /* Dark red for modal header */
            color: white;
        }
        .modal-footer {
            justify-content: space-between;
        }
        table {
            background-color: white;
        }
        th, td {
            text-align: center;
        }
        .btn-primary {
            background-color: #dc3545; /* Dark red for button background */
            border-color: #dc3545; /* Match border color with background */
        }
        .btn-primary:hover {
            background-color: #c82333; /* Slightly darker red on hover */
            border-color: #bd2130; /* Slightly darker border color on hover */
        }
        .scroll {
            height: calc(94vh - 60px); /* Adjust height to fit within viewport */
            overflow-y: auto;
        }
        .form-container {
            margin: 20px;
        }
        .doctor-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .doctor-item:hover {
            background-color: #f8f9fa;
        }
        .btn-custom {
            background-color: #d9534f;
            border-color: #d9534f;
        }
        .btn-custom:hover {
            background-color: #c9302c;
            border-color: #c9302c;
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
    <div class="scroll">
        <div class="content">
            <!-- Page Content Goes Here -->
            <div class="container mt-5">
            <h2>Camp Request Details</h2>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Camp Name</th>
                            <th>Participants</th>
                            <th>Location</th>
                            <th>Date Requested</th>
                            <th>Admin</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($camps as $camp): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($camp['camp_id']); ?></td>
                                <td><?php echo htmlspecialchars($camp['org_name']); ?></td>
                                <td><?php echo htmlspecialchars($camp['participants']); ?></td>
                                <td><?php echo htmlspecialchars($camp['location']); ?></td>
                                <td><?php echo htmlspecialchars($camp['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($camp['admin_notes']); ?></td>
                                <td><?php echo htmlspecialchars($camp['status']); ?></td>
                                <td>
                                    <button class="btn btn-primary" 
                                            data-toggle="modal" 
                                            data-target="#assignModal" 
                                            data-camp-id="<?php echo htmlspecialchars($camp['camp_id']); ?>" 
                                            data-camp-name="<?php echo htmlspecialchars($camp['org_name']); ?>"
                                            data-location="<?php echo htmlspecialchars($camp['location']); ?>"
                                            data-date-requested="<?php echo htmlspecialchars($camp['created_at']); ?>">Assign Date & Staff
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        
         <!-- Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Assign Camp Date & Staff</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignForm" action="" method="POST">
                    <input type="hidden" id="camp_id" name="camp_id" value="">
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="doctors">Doctors:</label>
                        <select id="doctors" class="form-control" name="doctors[]" multiple required>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo htmlspecialchars($doctor['dr_id']); ?>"><?php echo htmlspecialchars($doctor['fullName']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-secondary mt-2" id="selectDoctorsButton">Select Doctors</button>
                        <div id="selectedDoctors" class="mt-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="assignForm">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedDoctorsArray = [];

document.getElementById('selectDoctorsButton').addEventListener('click', function() {
    const selectedOptions = Array.from(document.getElementById('doctors').selectedOptions);
    const selectedIds = selectedOptions.map(option => option.value);
    const selectedNames = selectedOptions.map(option => option.text).join(', ');

    // Add selected IDs to the array if not already present
    selectedIds.forEach(id => {
        if (!selectedDoctorsArray.includes(id)) {
            selectedDoctorsArray.push(id);
        }
    });

    // Show selected names
    const selectedDoctorsDiv = document.getElementById('selectedDoctors');
    if (selectedDoctorsArray.length > 0) {
        selectedDoctorsDiv.innerHTML = `<strong>Selected Doctors:</strong> ${selectedNames}`;
    } else {
        selectedDoctorsDiv.innerHTML = `<strong>No doctors selected.</strong>`;
    }

    // Update the hidden input with all selected IDs
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'doctor_ids'; // New name for storing IDs
    hiddenInput.value = selectedDoctorsArray.join(',');
    document.getElementById('assignForm').appendChild(hiddenInput);
});
</script>


            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Full jQuery -->
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

            <script>
                $('#assignModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var campId = button.data('camp-id');
                    var location = button.data('location');
                    var dateRequested = button.data('date-requested');

                    var modal = $(this);
                    modal.find('.modal-body #camp_id').val(campId); // Set the camp_id in the hidden input
                    modal.find('.modal-body #location').val(location);
                    modal.find('.modal-body #date').val(dateRequested);
                });
            </script>
        </div>
    </div>
</body>
</html>