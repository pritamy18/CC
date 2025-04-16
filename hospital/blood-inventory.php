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


// Fetch blood inventory data
$sql = "SELECT bdc.org_name,d.fullName, bd.name, d.dr_id, h.hospitalName, b.amount, b.type, bdc.location, b.entrydate
        FROM blood AS b
        JOIN hospitals AS h ON b.hospital_id = h.hospital_id
        JOIN blood_donation_camps AS bdc ON b.camp_id = bdc.camp_id
        JOIN doctors AS d ON b.dr_id = d.dr_id
        JOIN blood_donor AS bd ON b.request_id = bd.request_id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$bloodInventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        h1 {
            text-align: center;
            color: #d9534f;
        }

        /* Button styles */
        .btn {
            padding: 8px 12px;
            background-color: #721c24;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #dc3545;
        }

        .btn-approve {
            background-color: #5cb85c;
        }

        .btn-reject {
            background-color: #d9534f;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            text-align: center;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #d9534f;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form and other content inside modal */
        form {
            margin-top: 10px;
        }

        label {
            font-size: 14px;
        }

        input[type="date"], input[type="time"], input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
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
    <div class="content">
        <div class="container">
            <!-- Page Content Goes Here -->
            <h1>Blood Inventory Management</h1>
            <!-- Main Table for Hospital Requests -->
            <table>
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Blood Camp</th>
                        <th>Donar</th>
                        <th>Doctor</th>
                        <th>Quantity</th>
                        <th>Blood Type</th>
                        <th>Location</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bloodInventory as $index => $blood): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $blood['org_name']; ?></td>
                            <td><?php echo $blood['name']; ?></td>
                            <td><?php echo $blood['fullName']; ?></td>
                            <td><?php echo $blood['amount']; ?></td>
                            <td><?php echo $blood['type']; ?></td>
                            <td><?php echo $blood['location']; ?></td>
                            <td><?php echo $blood['entrydate']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
                        </div>
            </div>

            <script>
                function showDetails(id) {
                    fetch(`getDetails.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            const detailsText = document.getElementById('detailsText');
                            detailsText.textContent = data.details;
                            document.getElementById('detailsContainer').style.display = 'block';
                        })
                        .catch(error => console.error('Error fetching details:', error));
                }

            </script>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>