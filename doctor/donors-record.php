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

// Fetching data from blood_donor table
$sql = "SELECT bd.request_id, bd.user_id, bd.blood_id, bd.status, bd.date FROM blood_donor bd";
$result = $conn->query($sql);

// Handle dynamic fetching based on request type
function fetchDetails($conn, $type, $id) {
    switch ($type) {
        case 'request':
            $sql = "SELECT * FROM blood_donor WHERE request_id = ?";
            break;
        case 'user':
            $sql = "SELECT * FROM users WHERE user_id = ?";
            break;
        case 'blood':
            $sql = "SELECT * FROM blood WHERE blood_id = ?";
            break;
        default:
            return null;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

if (isset($_GET['fetch_type']) && isset($_GET['id'])) {
    $type = $_GET['fetch_type'];
    $id = $_GET['id'];
    $data = fetchDetails($conn, $type, $id);

    if ($data) {
        if ($type === 'request') {
            echo "<p><strong>Request ID:</strong> {$data['request_id']}</p>
                  <p><strong>User ID:</strong> {$data['user_id']}</p>
                  <p><strong>Blood ID:</strong> {$data['blood_id']}</p>
                  <p><strong>Status:</strong> {$data['status']}</p>
                  <p><strong>Date:</strong> {$data['date']}</p>";
        } elseif ($type === 'user') {
            echo "<p><strong>Name:</strong> {$data['fullName']}</p>
                  <p><strong>Email:</strong> {$data['email']}</p>
                  <p><strong>Phone:</strong> {$data['phone']}</p>
                  <p><strong>Location:</strong> {$data['location']}</p>
                  <p><strong>Blood Type:</strong> {$data['bloodType']}</p>";
        } elseif ($type === 'blood') {
            echo "<p><strong>Blood ID:</strong> {$data['blood_id']}</p>
                  <p><strong>Blood Type:</strong> {$data['type']}</p>
                  <p><strong>Amount:</strong> {$data['amount']} units</p>
                  <p><strong>Status:</strong> {$data['status']}</p>";
        }
    } else {
        echo "<p>No data found.</p>";
    }
    exit; // Stop further execution after fetching details
}
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
        .request-id-link, .blood-id-link, .user-id-link {
            cursor: pointer;
            color: #dc3545;
            text-decoration: underline;
        }
        .request-id-link:hover, .blood-id-link:hover, .user-id-link:hover {
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
 <!-- Content -->
 <div class="content">
        <div class="container mt-5">
            <h1 class="text-center mb-4">Blood Request Details</h1>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>SR No</th>
                        <th>Request ID</th>
                        <th>User ID</th>
                        <th>Blood ID</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$sr_no}</td>
                            <td><a href=\"#\" class=\"request-id-link\" data-id=\"{$row['request_id']}\">{$row['request_id']}</a></td>
                            <td><a href=\"#\" class=\"user-id-link\" data-id=\"{$row['user_id']}\">{$row['user_id']}</a></td>
                            <td><a href=\"#\" class=\"blood-id-link\" data-id=\"{$row['blood_id']}\">{$row['blood_id']}</a></td>
                            <td>{$row['status']}</td>
                            <td>{$row['date']}</td>
                          </tr>";
                    $sr_no++;
                }
            } else {
                echo "<tr><td colspan=\"6\" class=\"text-center\">No requests found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Handle click on Request ID
    $('.request-id-link').on('click', function(event) {
        event.preventDefault();
        var requestId = $(this).data('id');
        fetchData('request', requestId);
    });

    // Handle click on User ID
    $('.user-id-link').on('click', function(event) {
        event.preventDefault();
        var userId = $(this).data('id');
        fetchData('user', userId);
    });

    // Handle click on Blood ID
    $('.blood-id-link').on('click', function(event) {
        event.preventDefault();
        var bloodId = $(this).data('id');
        fetchData('blood', bloodId);
    });

    function fetchData(type, id) {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            data: { fetch_type: type, id: id },
            success: function(data) {
                showModal(data, type.charAt(0).toUpperCase() + type.slice(1) + ' Details', id);
            },
            error: function() {
                alert('Error loading details.');
            }
        });
    }

    function showModal(bodyContent, title, id) {
        var modalHtml = `
            <div class="modal fade" id="dynamic-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">${title} (ID: ${id})</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">${bodyContent}</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

        $('body').append(modalHtml);
        $('#dynamic-modal').modal('show');

        $('#dynamic-modal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
});
</script>

<?php
$conn->close();
?>
</body>
</html>