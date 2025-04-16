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
// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

// Handle Approve request
if (isset($_POST['approve'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM hospitals WHERE hospital_id = :id");
    $stmt->execute(['id' => $id]);
}
// SQL query to fetch hospital data and total staff count
$sql = "
    SELECT 
        h.hospital_id, 
        h.address, 
        h.phone, 
        COUNT(d.dr_id) AS staff_count 
    FROM 
        hospitals h 
    LEFT JOIN 
        doctors d ON h.hospital_id = d.hospital_id 
    GROUP BY 
        h.hospital_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fetch data
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Information</title>
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
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #e74c3c; /* Blood red background for card headers */
            color: #ffffff; /* White text color for contrast */
            font-weight: bold;
        }
        .card-body {
            background-color: #ffffff; /* White background for card body */
        }
        .search-container {
            display: flex;
            margin-bottom: 15px;
        }
        .search-input {
            flex: 1;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #e74c3c; /* Blood red background for the button */
            border: none;
        }
        .btn-primary:hover {
            background-color: #c0392b; /* Darker red for hover effect */
        }
        table {
            width: 100%;
        }
        .table th, .table td {
            text-align: center;
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
                <!-- Search Input and Button -->
                        <div class="search-container">
                            <input type="text" id="search" class="form-control search-input" placeholder="Search by hospital ID, location, or contact">
                            <button id="searchBtn" class="btn btn-primary">Search</button>
                        </div>
            <!-- Hospital Information -->
<div class="card">
    <div class="card-header">
        Hospital Information
    </div>
    <div class="card-body">
        <div style="max-height: 480px; overflow-y: auto;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>SR No</th>
                        <th>Hospital ID</th>
                        <th>Staff Count</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Approve</th>
                    </tr>
                </thead>
                <tbody id="infoTable">
                    <?php foreach ($hospitals as $index => $hospital): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($hospital['hospital_id']) ?></td>
                            <td><?= htmlspecialchars($hospital['staff_count']) ?></td>
                            <td><?= htmlspecialchars($hospital['address']) ?></td>
                            <td><?= htmlspecialchars($hospital['phone']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($hospital['hospital_id']) ?>">
                                    <button type="submit" name="approve" value="Approve" class="btn btn-danger" onclick="return confirm('Are you sure you want to Approve This Hospital?');">Approve</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

           <script>
                document.getElementById('searchBtn').addEventListener('click', function() {
                    let searchValue = document.getElementById('search').value.toLowerCase();
                    let rows = document.querySelectorAll('#infoTable tr');
                    
                    rows.forEach(row => {
                        let hospitalId = row.cells[1].textContent.toLowerCase();
                        let location = row.cells[3].textContent.toLowerCase();
                        let contact = row.cells[4].textContent.toLowerCase();
                        
                        if (hospitalId.includes(searchValue) || location.includes(searchValue) || contact.includes(searchValue)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
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
