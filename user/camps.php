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
// Fetch data from the blood_donation_camps table
$sql = "SELECT 
    camps.*, 
    hospitals.hospitalName 
FROM 
    blood_donation_camps AS camps 
JOIN 
    hospitals ON camps.hospital_id = hospitals.hospital_id 
WHERE 
    camps.status != 'pending';";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Camps</title>
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
        .card {
      transition: transform 0.3s ease-in-out;
    }
    .card:hover {
      transform: translateY(-10px);
    }
    .input-group input {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .input-group input:hover {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .btn-search {
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-search:hover {
      background-color: #721c24;
      color: white;
    }
    .table-container {
      max-height: 400px;
      overflow-y: auto;
      overflow-x: hidden;
    }
    .table-container::-webkit-scrollbar {
      width: 8px;
    }
    .table-container::-webkit-scrollbar-thumb {
      background-color: #dc3545;
      border-radius: 4px;
    }
    .table-container::-webkit-scrollbar-track {
      background-color: #f8f9fa;
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
                    <span class="username"><?php echo $_SESSION['user_name'] ;?></span>
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
    <div class="content">
        <div class="container my-5">
            <h1 class="text-center mb-4">Blood Camps</h1>
          
            <!-- Search Blood Camps Section -->
            <div class="row mb-4">
              <div class="col-md-6 mx-auto">
                <div class="input-group">
                  <input type="text" id="searchInput" class="form-control" placeholder="Search Blood Camps" onkeyup="searchCamps()">
                  <button class="btn btn-danger btn-search" type="button">Search</button>
                </div>
              </div>
            </div>
          
            <!-- Blood Camps List Section -->
            <div class="row">
              <div class="col-md-12">
                <div class="card shadow-sm">
                  <div class="card-body">
                    <h5 class="card-title">Blood Camps List</h5>
                    <div class="table-container">
                      <table class="table table-hover" id="campsTable">
                        <thead class="table-danger">
                          <tr>
                            <th>Hospital Name</th>
                            <th>Organizer Name</th>
                            <th>Location</th>
                            <th>No. of Medical Staff</th>
                            <th>Camp Capacity</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Check if the query returned any results
                            if ($result->num_rows > 0) {
                                // Loop through the results and display them in the table
                                while ($row = $result->fetch_assoc()) {
                                    $hospital_name = $row['hospital_name'];
                                    $organizer_name = $row['org_name'];
                                    $location = $row['location'];
                                    $no_of_medical_staff = 0; // You might want to fetch this data as well
                                    $camp_capacity = $row['participants'] + 20; // Adding 20 to the number of participants
                                    $date = $row['formatted_date'];

                                    echo "<tr>
                                            <td>$hospital_name</td>
                                            <td>$organizer_name</td>
                                            <td>$location</td>
                                            <td>$no_of_medical_staff</td>
                                            <td>$camp_capacity</td>
                                            <td>$date</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No camps available</td></tr>";
                            }
                            ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          
          </div>
          <?php
// Close the database connection
$conn->close();
?>
          <script>
            function searchCamps() {
              const input = document.getElementById("searchInput").value.toUpperCase();
              const table = document.getElementById("campsTable");
              const rows = table.getElementsByTagName("tr");
          
              for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName("td");
                let display = false;
                for (let j = 0; j < cells.length; j++) {
                  const cell = cells[j];
                  if (cell) {
                    const txtValue = cell.textContent || cell.innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                      display = true;
                      break;
                    }
                  }
                }
                rows[i].style.display = display ? "" : "none";
              }
            }
          </script>
          
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
