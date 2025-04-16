<?php
session_start(); // Start the session
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
}
// Database connection parameters
$host = 'localhost'; // Database host
$db   = 'blood_donation'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// SQL query to fetch blood data grouped by hospital and blood type
$sql = "
    SELECT 
        h.hospitalName, 
        b.type, 
        SUM(b.amount) AS total_amount 
    FROM 
        blood b 
    JOIN 
        hospitals h ON b.hospital_id = h.hospital_id 
    GROUP BY 
        h.hospitalName, b.type
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$bloodData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare an array to hold the data for each hospital
$inventory = [];

// Initialize totals for each blood type
$totalCounts = [
    'O+' => 0,
    'A-' => 0,
    'B+' => 0,
    'AB-' => 0,
    'O-' => 0,
    'A+' => 0,
    'B-' => 0,
    'AB+' => 0,
];

// Populate the inventory array
foreach ($bloodData as $row) {
    $hospital = $row['hospitalName'];
    $bloodType = $row['type'];
    $amount = $row['total_amount'];

    if (!isset($inventory[$hospital])) {
        $inventory[$hospital] = [
            'O+' => 0,
            'A-' => 0,
            'B+' => 0,
            'AB-' => 0,
            'O-' => 0,
            'A+' => 0,
            'B-' => 0,
            'AB+' => 0,
        ];
    }

    $inventory[$hospital][$bloodType] += $amount;
    $totalCounts[$bloodType] += $amount;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Inventory</title>
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
        .container {
            margin-top: 20px;
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
        .hospital-table {
            max-height: 400px; /* Adjust based on preference */
            overflow-y: auto;
        }
        table {
            width: 100%;
        }
        .table th, .table td {
            text-align: center;
        }
        .search-container {
            display: flex;
            margin-bottom: 15px;
        }
        th{
            background-color: #e74c3c;
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
            <div class="card">
                <div class="card-body">
                    <div class="search-container">
                        <input type="text" id="search" class="form-control search-input" placeholder="Search by hospital name or blood type">
                        <button id="searchBtn" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
    
            <!-- Hospital Blood Inventories -->
            <div class="card">
                <div class="card-header">
                    Hospital Blood Inventories
                </div>
                <div class="card-body hospital-table">
                    <table class="table table-bordered">
                    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Hospital</th>
            <th>O+</th>
            <th>A-</th>
            <th>B+</th>
            <th>AB-</th>
            <th>O-</th>
            <th>A+</th>
            <th>B-</th>
            <th>AB+</th>
        </tr>
    </thead>
    <tbody id="inventoryTable">
        <?php foreach ($inventory as $hospital => $bloodAmounts): ?>
            <tr>
                <td><?= htmlspecialchars($hospital) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['O+']) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['A-']) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['B+']) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['AB-']) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['O-']) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['A+']) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['B-']) ?></td>
                <td><?= htmlspecialchars($bloodAmounts['AB+']) ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td><strong>Total</strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['O+']) ?></strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['A-']) ?></strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['B+']) ?></strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['AB-']) ?></strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['O-']) ?></strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['A+']) ?></strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['B-']) ?></strong></td>
            <td><strong><?= htmlspecialchars($totalCounts['AB+']) ?></strong></td>
        </tr>
    </tbody>
</table></div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            document.getElementById('searchBtn').addEventListener('click', function() {
                let searchValue = document.getElementById('search').value.toLowerCase();
                let rows = document.querySelectorAll('#inventoryTable tr');
                
                rows.forEach(row => {
                    let hospitalName = row.cells[0].textContent.toLowerCase();
                    let bloodTypes = Array.from(row.cells).slice(1).map(cell => cell.textContent.toLowerCase()).join(' ');
                    
                    if (hospitalName.includes(searchValue) || bloodTypes.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        </script>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
