<?php
session_start(); // Start the session
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}
// Database configuration
$host = 'localhost';
$db = 'blood_donation';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Handle delete request
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE dr_id = :id");
    $stmt->execute(['id' => $id]);
}

// Handle search request
$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search_term'];
}

$stmt = $pdo->prepare("SELECT * FROM doctors WHERE fullName LIKE :search OR email LIKE :search");
$stmt->execute(['search' => '%' . $search . '%']);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor List</title>
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
        .card-header {
            background-color: #dc3545;
            color: white;
        }
        .modal-header {
            background-color: #dc3545;
            color: white;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #dc3545;
            color: white;
        }
        .modal-header {
            background-color: #dc3545;
            color: white;
        }
        .search-bar {
            margin-bottom: 20px;
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
                    <span class="username">admin</span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-logout" onclick="window.location.href='../index.html'">Logout</button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Side Navigation Bar -->
    <div class="sidebar">
        <a href="admindash.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="manage-users.php"><i class="fas fa-users"></i>Manage Users</a>
        <a href="manage-hospitals.php"><i class="fas fa-hospital"></i>Manage Hospitals</a>
        <a href="manage-doctors.php"><i class="fas fa-user-md"></i>Manage Doctors</a>
        <a href="manage-donar.php"><i class="fa fa-hand-holding-heart"></i>Manage Donars</a>
        <a href="blood-inventory.php"><i class="fas fa-tint"></i>Blood Inventory</a>
        <a href="view-requests.php"><i class="fas fa-clipboard-list"></i>View Requests</a>
        <a href="manage-camps.php"><i class="fa fa-calendar-day"></i>Manage Camps</a> <!-- New Donor List Element -->
        <a href="reports.php"><i class="fas fa-chart-line"></i>Reports</a>
        <a href="admin-settings.php"><i class="fa fa-cogs"></i>Settings</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container">
            <div class="container mt-5">
                <h2 class="text-center mb-4"> Manage Doctor</h2>
        
                <!-- Search Bar with Button -->
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search for doctors...">
                    <div class="input-group-append">
                        <button class="btn btn-danger" type="button" onclick="filterResults()">Search</button>
                    </div>
                </div>
        
                <!-- Doctor Table -->
                <div class="card">
    <div class="card-header">
        <h5>Manage Doctor</h5>
    </div>
    <div class="card-body">
        <div style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-striped" id="doctorTable">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Doctor ID</th>
                        <th>Hospital</th>
                        <th>First Name</th>
                        <th>Speciality</th>
                        <th>Contact</th>
                        <th>Gmail</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="doctorList">
                    <?php $i=1; ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?= htmlspecialchars($user['dr_id']) ?></td>
                            <td><?= htmlspecialchars($user['hospital']) ?></td>
                            <td><?= htmlspecialchars($user['fullName']) ?></td>
                            <td><?= htmlspecialchars($user['specialization']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['dr_id']) ?>">
                                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
            </div>
        
                </div>
            </div>
        
            <!-- JavaScript -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script>

        
                // Search filter function
                function filterResults() {
                    var input, filter, table, tr, i, txtValue;
                    input = document.getElementById('searchInput').value;
                    filter = input.toUpperCase();
                    table = document.getElementById('doctorTable');
                    tr = table.getElementsByTagName('tr');
        
                    for (i = 1; i < tr.length; i++) {  // Start from index 1 to skip header row
                        txtValue = tr[i].textContent || tr[i].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = '';
                        } else {
                            tr[i].style.display = 'none';
                        }
                    }
                }
        
                // Save button functionality (example)
                document.getElementById('saveDoctorChangesBtn').addEventListener('click', function() {
                    var assignWork = document.getElementById('doctorAssignWorkInput').value;
                    if (hospitalId) {
                        alert('Doctor assigned to: ' + assignWork );
                        $('#doctorModal').modal('hide'); // Close modal after saving
                    } else {
                        alert('Please enter Hospital ID.');
                    }
                });
            </script>
            <!-- Page Content Goes Here -->
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
