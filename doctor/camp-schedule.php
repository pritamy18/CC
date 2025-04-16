<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();

}
// Database connection
$servername = "localhost"; // Change as per your configuration
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "blood_donation"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching data from camp_donation_request table
$sql = "SELECT camps.*, hospitals.hospitalName FROM blood_donation_camps AS camps 
         JOIN  hospitals ON camps.hospital_id = hospitals.hospital_id WHERE camps.status != 'pending';"; // Adjust as needed
$result = $conn->query($sql);

// Handle dynamic fetching based on user ID
function fetchUserDetails($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id); // Assuming user_id is a string
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $userData = fetchUserDetails($conn, $user_id);
    
    if ($userData) {
        echo json_encode($userData);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
    exit; // Stop further execution after fetching user details
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Camp Details</title>
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
        }
        .user-id-link {
            cursor: pointer;
            color: #dc3545;
            text-decoration: underline;
        }
        .user-id-link:hover {
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
                <h1 class="text-center mb-4">Blood Donation Camp Organizer Details</h1>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>SR No</th>
                            <th>User ID</th>
                            <th>Hospital Name</th>
                            <th>Camp Date</th>
                            <th>Location</th>
                            <th>Staff Allocated</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$sr_no}</td>
                            <td><a href=\"#\" class=\"user-id-link\" data-id=\"{$row['user_id']}\">{$row['user_id']}</a></td>
                            <td>{$row['hospital_name']}</td>
                            <td>{$row['scheduled']}</td>
                            <td>{$row['location']}</td>
                            <td>{$row['assigned_doctors']}</td>
                          </tr>";
                    $sr_no++;
                }
            } else {
                echo "<tr><td colspan=\"6\" class=\"text-center\">No camp organizers found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Full jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $('.user-id-link').on('click', function(event) {
        event.preventDefault();
        var userId = $(this).data('id');
        fetchUserData(userId);
    });

    function fetchUserData(userId) {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            data: { user_id: userId },
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    alert(data.error);
                } else {
                    showUserModal(data);
                }
            },
            error: function() {
                alert('Error loading user details.');
            }
        });
    }

    function showUserModal(user) {
        // Ensure we're not creating duplicate modals
        $('#user-modal').remove(); 

        var modalHtml = `
            <div class="modal fade" id="user-modal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userModalLabel">User ID: ${user.user_id} - Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>User ID:</strong> ${user.user_id}</p>
                            <p><strong>Name:</strong> ${user.fullName}</p>
                            <p><strong>Contact:</strong> ${user.phone}</p>
                            <p><strong>Email:</strong> ${user.email}</p>
                            <p><strong>Address:</strong> ${user.location}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

        $('body').append(modalHtml); // Append modal to body
        $('#user-modal').modal('show'); // Show the modal

        $('#user-modal').on('hidden.bs.modal', function() {
            $(this).remove(); // Remove the modal from DOM after it's closed
        });
    }
});
</script>

<?php
$conn->close();
?>
  </div>
    </div>
</body>
</html>
