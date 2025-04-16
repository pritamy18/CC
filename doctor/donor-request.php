<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
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

        .navbar-brand,
        .nav-link,
        .username {
            color: #ffffff !important;
        }

        .navbar-collapse {
            display: flex;
            justify-content: flex-end;
        }

        .nav-link:hover,
        .username:hover {
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            margin-bottom: 10px;
        }

        .card p {
            font-size: 24px;
        }

        .container {
            margin-top: 20px;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .btn {
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-approve:hover {
            background-color: #28a745;
            color: #ffffff;
        }

        .btn-reject:hover {
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-approve,
        .btn-reject {
            margin-right: 5px;
        }

        .header {
            margin-bottom: 20px;
            text-align: center;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-height: 80%;
            /* Make sure the modal doesn't take up the full height of the viewport */
            overflow-y: auto;
            /* Enable vertical scrolling if content exceeds max-height */
            position: relative;
            /* Needed to position the close button properly */
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

        thead th {
            background-color: #dc3545;
            color: #ffffff;
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
    <?php
    $servername = "localhost";
    $username = "root"; // Replace with your MySQL username
    $password = "";     // Replace with your MySQL password
    $dbname = "blood_donation"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch all donor IDs
    $sql = "SELECT * FROM blood_donor";
    $result = $conn->query($sql);
    $i = 1;
    $status1 = '<span class="badge badge-warning">Pending</span>';
    $status2 = '<span class="badge badge-success">Approved</span>';
    $status3 = 'span class="badge badge-danger">Rejected</span>';
    ?>
    <!-- Content -->
    <div class="content">
        <div class="container">
            <!-- Page Content Goes Here -->
            <div class="container">
                <h2 class="header">Blood Donation Requests list</h2>
                <div class="scroll-table">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Request ID</th>
                                <th>Blood Type</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td><a href="#" class="donor-link" data-donor-id="<?php echo $row['request_id']; ?>"><?php echo $row['request_id']; ?></a></td>
                                    <td><?php echo $row['bloodGroup']; ?></td>
                                    <td><?php echo $row['date']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td>
                                        <!-- Approve Button -->
                                        <form action="" method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['request_id']; ?>">
                                            <input type="hidden" name="action" value='<span class="badge badge-success">Approved</span>'>
                                            <button type="submit" class="btn btn-approve btn-sm" data-toggle="tooltip" data-placement="top" title="Approve">Approve</button>
                                        </form>
                                        <!-- Reject Button -->
                                        <form action="" method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['request_id']; ?>">
                                            <input type="hidden" name="action" value='<span class="badge badge-danger">Rejected</span>'>
                                            <button type="submit" class="btn btn-reject btn-sm" data-toggle="tooltip" data-placement="top" title="Reject">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php $i++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            // Database connection
            $servername = "localhost";
            $username = "root"; // Replace with your MySQL username
            $password = "";     // Replace with your MySQL password
            $dbname = "blood_donation"; // Replace with your database name

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Handle action button responses
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'];
                $action = $_POST['action'];

                // Prepare and execute update query
                $sql = mysqli_query($conn, "UPDATE blood_donor SET status = '$action' WHERE request_id = '$id';");
            ?><script>
                    window.location.href = "donor-request.php";
                </script><?php

                            $conn->close();
                        }

                            ?>


            <!-- User Details Modal -->
            <!-- The Modal -->
            <div id="bloodDonorModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Donor Details</h2>
                    <div id="donorDetails"></div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById("bloodDonorModal");
                    var span = document.getElementsByClassName("close")[0];
                    var donorDetails = document.getElementById("donorDetails");

                    document.querySelectorAll('.donor-link').forEach(function(link) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            var donorId = this.getAttribute('data-donor-id');

                            fetch('get_donor_details.php?request_id=' + donorId)
                                .then(response => response.json())
                                .then(data => {
                                    donorDetails.innerHTML = `
                        <table>
                           <tr><th>Request Id</th><td>${data.request_id}</td></tr>
<tr><th>Date</th><td>${data.date}</td></tr>
<tr><th>Name</th><td>${data.name}</td></tr>
<tr><th>Gender</th><td>${data.gender}</td></tr>
<tr><th>Blood Group</th><td>${data.bloodGroup}</td></tr>
<tr><th>Rh Factor</th><td>${data.rhFactor}</td></tr>
<tr><th>Hb</th><td>${data.hb}</td></tr>
<tr><th>Father's Name</th><td>${data.fatherName}</td></tr>
<tr><th>Age</th><td>${data.age}</td></tr>
<tr><th>Occupation</th><td>${data.occupation}</td></tr>
<tr><th>Date of Birth</th><td>${data.dob}</td></tr>
<tr><th>Organization</th><td>${data.org}</td></tr>
<tr><th>Nationality</th><td>${data.nationality}</td></tr>
<tr><th>Address</th><td>${data.address}</td></tr>
<tr><th>Mobile No</th><td>${data.mobile}</td></tr>
<tr><th>Email</th><td>${data.email}</td></tr>
<tr><th>Height</th><td>${data.height}</td></tr>
<tr><th>Weight</th><td>${data.weight}</td></tr>
<tr><th>Registered as Regular Donor</th><td>${data.regRegularDonor }</td></tr>
<tr><th>Donated Blood Previously</th><td>${data.donatedBlood }</td></tr>
<tr><th>Discomfort During/After Donation</th><td>${data.discomfort }</td></tr>
<tr><th>Feel Well Today</th><td>${data.feelingWell}</td></tr>
<tr><th>Ate in Last 4 Hours</th><td>${data.eatenInLast4Hours}</td></tr>
<tr><th>Slept Well Last Night</th><td>${data.sleptWell }</td></tr>
<tr><th>Acute Respiratory Problem</th><td>${data.acuteRespiratory }</td></tr>
<tr><th>Infected with Hepatitis, Malaria, HIV/AIDS or Venereal Disease</th><td>${data.infected}</td></tr>
<tr><th>Venipuncture Site Normal</th><td>${data.venipunctureNormal}</td></tr>
<tr><th>Unexplained Weight Loss</th><td>${data.unexplainedWeightLoss}</td></tr>
<tr><th>Repeated Diarrhoea</th><td>${data.repeatedDiarrhoea }</td></tr>
<tr><th>Swollen Gland</th><td>${data.swollenGland}</td></tr>
<tr><th>Under Treatment</th><td>${data.underTreatment }</td></tr>
<tr><th>Allergic Reaction</th><td>${data.allergicReaction }</td></tr>
<tr><th>Taking Antibiotics</th><td>${data.antibiotics }</td></tr>
<tr><th>Taking Aspirin</th><td>${data.aspirin }</td></tr>
<tr><th>Taking Alcohol in Last 24 Hours</th><td>${data.alcohol24Hours }</td></tr>
<tr><th>Taking BP Medicine</th><td>${data.bpMedicine }</td></tr>
<tr><th>Ear Piercing</th><td>${data.earPiercing }</td></tr>
<tr><th>Dental Extraction</th><td>${data.dentalExtraction }</td></tr>
<tr><th>Major Surgery</th><td>${data.majorSurgery }</td></tr>
<tr><th>Minor Surgery</th><td>${data.minorSurgery}</td></tr>
<tr><th>Blood Transfusion</th><td>${data.bloodTransfusion }</td></tr>
<tr><th>Steroids</th><td>${data.steroids}</td></tr>
<tr><th>Vaccination</th><td>${data.vaccination}</td></tr>
<tr><th>Dog Bite Rabies Vaccine in Last 1 Year</th><td>${data.dogBiteRabies }</td></tr>
<tr><th>Travel History in Last 3 Months</th><td>${data.travelHistory }</td></tr>
<tr><th>History of Infection or Quarantine in Family/Friends/Colleagues/Neighbors</th><td>${data.infectionHistory }</td></tr>
<tr><th>Signs/Symptoms of Fever, Cold, Cough in Last 1 Month</th><td>${data.signSymptoms }</td></tr>
<tr><th>Ear Piercing/Tattooing</th><td>${data.earPiercing }</td></tr>
<tr><th>Dental Extraction</th><td>${data.dentalExtraction }</td></tr>
<tr><th>Major Surgery</th><td>${data.majorSurgery }</td></tr>
<tr><th>Minor Surgery</th><td>${data.minorSurgery }</td></tr>
<tr><th>Blood Transfusion</th><td>${data.bloodTransfusion }</td></tr>
<tr><th>Last Donated Blood</th><td>${data.lastDonatedBlood}</td></tr>

                        </table>
                    `;
                                    modal.style.display = "block";
                                })
                                .catch(error => console.error('Error fetching donor details:', error));
                        });
                    });

                    span.onclick = function() {
                        modal.style.display = "none";
                    }

                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                });
            </script>

            <!-- Bootstrap JS and dependencies -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>