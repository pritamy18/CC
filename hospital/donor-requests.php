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
 $conn = new mysqli($host, $username, $password, $dbname);
 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }

// Fetch data from donor table
$sql = "SELECT 
    bd.*,         
    h.hospitalName ,
    d.fullName   
FROM 
    blood_donor bd
LEFT JOIN 
    hospitals h ON bd.hospital_id = h.hospital_id  
LEFT JOIN 
    doctors d ON bd.dr_id = d.dr_id";     

$result = $conn->query($sql);

$donorRequests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $donorRequests[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Requests</title>
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

        /* Table styles */
        table {
            width: 100%;
            max-width: 1200px; /* Increased width */
            border-collapse: collapse;
            margin: 20px auto; /* Center the table */
            background-color: #fff;
            border: 1px solid #ccc;
            text-align: center;
            overflow-y: auto;
            max-height: 500px;
            display: block;
        }

        th, td {
            padding: 20px;
            width:180px;
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
            padding-top: 80px;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 100%; /* Increased width */
            max-width: 610px; /* Max width for larger screens */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
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

        .modal-content h2 {
            margin-top: 0;
            color: #d9534f;
        }

        .card {
            background-color: #f1f1f1;
            border-radius: 8px;
            padding: 20px;
            margin: 10px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card p {
            margin: 10px 0;
            font-size: 16px;
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
            <h1>Blood Donation Requests</h1>

            <!-- Main Table for Donation Requests -->
            <table>
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Request ID</th>
                        <th>Status</th>
                        <th>Hospital Name</th>
                        <th>Doctor Name</th>
                        <th>Request Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donorRequests as $index => $request): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><a href="#" onclick="showRequestDetails('<?php echo $request['request_id']; ?>')"><?php echo $request['request_id']; ?></a></td>
                        <td><?php echo $request['status']; ?></td>
                        <td><?php echo $request['hospitalName'] ?? 'null'; ?></td>
                        <td><?php echo $request['fullName'] ?? 'null'; ?></td>
                        <td><?php echo $request['date']; ?></td> <!-- Display request date -->
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Modal for Request Details -->
            <div id="requestDetailsModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeRequestDetailsModal()">&times;</span>
                    <h2>Request Details</h2>
                    <div id="requestDetails" class="card"></div>
                </div>
            </div>

            <script>
                // Store request details fetched from the database
                const requestDetails = <?php echo json_encode(array_column($donorRequests, null, 'request_id')); ?>;

                function showRequestDetails(requestId) {
                    const request = requestDetails[requestId];
                    const details = `
                       <table>
    <tr><th>Request ID</th><td>${request.request_id}</td></tr>
    <tr><th>Date</th><td>${request.date}</td></tr>
    <tr><th>Name</th><td>${request.name}</td></tr>
    <tr><th>Gender</th><td>${request.gender}</td></tr>
    <tr><th>Blood Group</th><td>${request.bloodGroup}</td></tr>
    <tr><th>Rh Factor</th><td>${request.rhFactor}</td></tr>
    <tr><th>Hb</th><td>${request.hb}</td></tr>
    <tr><th>Father's Name</th><td>${request.fatherName}</td></tr>
    <tr><th>Age</th><td>${request.age}</td></tr>
    <tr><th>Occupation</th><td>${request.occupation}</td></tr>
    <tr><th>Date of Birth</th><td>${request.dob}</td></tr>
    <tr><th>Organization</th><td>${request.org}</td></tr>
    <tr><th>Nationality</th><td>${request.nationality}</td></tr>
    <tr><th>Address</th><td>${request.address}</td></tr>
    <tr><th>Mobile No</th><td>${request.mobile}</td></tr>
    <tr><th>Email</th><td>${request.email}</td></tr>
    <tr><th>Height</th><td>${request.height}</td></tr>
    <tr><th>Weight</th><td>${request.weight}</td></tr>
    <tr><th>Registered as Regular Donor</th><td>${request.regRegularDonor ? 'Yes' : 'No'}</td></tr>
    <tr><th>Donated Blood Previously</th><td>${request.donatedBlood ? 'Yes' : 'No'}</td></tr>
    <tr><th>Discomfort During/After Donation</th><td>${request.discomfort ? 'Yes' : 'No'}</td></tr>
    <tr><th>Feel Well Today</th><td>${request.feelingWell ? 'Yes' : 'No'}</td></tr>
    <tr><th>Ate in Last 4 Hours</th><td>${request.eatenInLast4Hours ? 'Yes' : 'No'}</td></tr>
    <tr><th>Slept Well Last Night</th><td>${request.sleptWell ? 'Yes' : 'No'}</td></tr>
    <tr><th>Acute Respiratory Problem</th><td>${request.acuteRespiratory ? 'Yes' : 'No'}</td></tr>
    <tr><th>Infected with Hepatitis, Malaria, HIV/AIDS or Venereal Disease</th><td>${request.infected ? 'Yes' : 'No'}</td></tr>
    <tr><th>Venipuncture Site Normal</th><td>${request.venipunctureNormal ? 'Yes' : 'No'}</td></tr>
    <tr><th>Unexplained Weight Loss</th><td>${request.unexplainedWeightLoss ? 'Yes' : 'No'}</td></tr>
    <tr><th>Repeated Diarrhoea</th><td>${request.repeatedDiarrhoea ? 'Yes' : 'No'}</td></tr>
    <tr><th>Swollen Gland</th><td>${request.swollenGland ? 'Yes' : 'No'}</td></tr>
    <tr><th>Under Treatment</th><td>${request.underTreatment ? 'Yes' : 'No'}</td></tr>
    <tr><th>Allergic Reaction</th><td>${request.allergicReaction ? 'Yes' : 'No'}</td></tr>
    <tr><th>Taking Antibiotics</th><td>${request.antibiotics ? 'Yes' : 'No'}</td></tr>
    <tr><th>Taking Aspirin</th><td>${request.aspirin ? 'Yes' : 'No'}</td></tr>
    <tr><th>Taking Alcohol in Last 24 Hours</th><td>${request.alcohol24Hours ? 'Yes' : 'No'}</td></tr>
    <tr><th>Taking BP Medicine</th><td>${request.bpMedicine ? 'Yes' : 'No'}</td></tr>
    <tr><th>Ear Piercing</th><td>${request.earPiercing ? 'Yes' : 'No'}</td></tr>
    <tr><th>Dental Extraction</th><td>${request.dentalExtraction ? 'Yes' : 'No'}</td></tr>
    <tr><th>Major Surgery</th><td>${request.majorSurgery ? 'Yes' : 'No'}</td></tr>
    <tr><th>Minor Surgery</th><td>${request.minorSurgery ? 'Yes' : 'No'}</td></tr>
    <tr><th>Blood Transfusion</th><td>${request.bloodTransfusion ? 'Yes' : 'No'}</td></tr>
    <tr><th>Steroids</th><td>${request.steroids ? 'Yes' : 'No'}</td></tr>
    <tr><th>Vaccination</th><td>${request.vaccination ? 'Yes' : 'No'}</td></tr>
    <tr><th>Dog Bite Rabies Vaccine in Last 1 Year</th><td>${request.dogBiteRabies ? 'Yes' : 'No'}</td></tr>
    <tr><th>Travel History in Last 3 Months</th><td>${request.travelHistory ? 'Yes' : 'No'}</td></tr>
    <tr><th>History of Infection or Quarantine in Family/Friends/Colleagues/Neighbors</th><td>${request.infectionHistory ? 'Yes' : 'No'}</td></tr>
    <tr><th>Signs/Symptoms of Fever, Cold, Cough in Last 1 Month</th><td>${request.signSymptoms ? 'Yes' : 'No'}</td></tr>
    <tr><th>Ear Piercing/Tattooing</th><td>${request.earPiercing ? 'Yes' : 'No'}</td></tr>
    <tr><th>Last Donated Blood</th><td>${request.lastDonatedBlood}</td></tr>
</table>
  `;
                    document.getElementById('requestDetails').innerHTML = details;
                    document.getElementById('requestDetailsModal').style.display = 'block';
                }

                function closeRequestDetailsModal() {
                    document.getElementById('requestDetailsModal').style.display = 'none';
                }

                window.onclick = function(event) {
                    const requestDetailsModal = document.getElementById('requestDetailsModal');
                    if (event.target == requestDetailsModal) {
                        requestDetailsModal.style.display = 'none';
                    }
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
