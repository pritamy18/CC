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
    <title>Blood Donation Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
        .scroll {
            height: calc(94vh - 60px); /* Adjust height to fit within viewport */
            overflow-y: auto;
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
    <div class="scroll">
      <div class="content">

        <h1 class="text-center">Blood Donation Form</h1>
        <form action="" method="post">
    <!-- Personal Information -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
        </div>
        <div class="form-group col-md-6">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" class="form-control">
                <option value="M">Male</option>
                <option value="F">Female</option>
                <option value="O">Other</option>
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="fatherName">Father's Name</label>
            <input type="text" class="form-control" id="fatherName" name="fatherName" placeholder="Father's Name">
        </div>
        <div class="form-group col-md-6">
            <label for="dob">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="age">Age</label>
            <input type="number" class="form-control" id="age" name="age" placeholder="Age">
        </div>
        <div class="form-group col-md-6">
            <label for="occupation">Occupation</label>
            <input type="text" class="form-control" id="occupation" name="occupation" placeholder="Occupation">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="organization">Organization</label>
            <input type="text" class="form-control" id="organization" name="organization" placeholder="Organization">
        </div>
        <div class="form-group col-md-6">
            <label for="nationality">Nationality</label>
            <input type="text" class="form-control" id="nationality" name="nationality" placeholder="Nationality">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Address">
        </div>
        <div class="form-group col-md-6">
            <label for="mobileNo">Mobile Number</label>
            <input type="text" class="form-control" id="mobileNo" name="mobileNo" placeholder="Mobile Number">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
        </div>
        <div class="form-group col-md-6">
            <label for="height">Height</label>
            <input type="text" class="form-control" id="height" name="height" placeholder="Height">
        </div>
    </div>

    <!-- Health Metrics -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="weight">Weight</label>
            <input type="number" step="0.01" class="form-control" id="weight" name="weight" placeholder="Weight">
        </div>
        <div class="form-group col-md-6">
        <label for="bloodGroup">Blood Group</label>
          <select id="bloodGroup" name="bloodGroup" class="form-control">
             <option value="A+">A+</option>
             <option value="A-">A-</option>
             <option value="B+">B+</option>
             <option value="B-">B-</option>
             <option value="AB+">AB+</option>
             <option value="AB-">AB-</option>
             <option value="O+">O+</option>
             <option value="O-">O-</option>
           </select>

        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="rhFactor">Rh Factor</label>
            <input type="text" class="form-control" id="rhFactor" name="rhFactor" placeholder="Rh Factor">
        </div>
        <div class="form-group col-md-6">
            <label for="hb">Hb</label>
            <input type="number" step="0.01" class="form-control" id="hb" name="hb" placeholder="Hb">
        </div>
    </div>

    <!-- Donation History -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Registered as Regular Donor</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="regularDonor" name="regularDonor">
                <label class="form-check-label" for="regularDonor">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Donated Blood Previously</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="donatedBlood" name="donatedBlood">
                <label class="form-check-label" for="donatedBlood">Yes</label>
            </div>
        </div>
    </div>

    <!-- Medical History -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Discomfort During/After Donation</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="discomfort" name="discomfort">
                <label class="form-check-label" for="discomfort">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Feeling Well Today</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="feelingWell" name="feelingWell">
                <label class="form-check-label" for="feelingWell">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Eaten in Last 4 Hours</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="eatenInLast4Hours" name="eatenInLast4Hours">
                <label class="form-check-label" for="eatenInLast4Hours">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Slept Well Last Night</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="sleptWellLastNight" name="sleptWellLastNight">
                <label class="form-check-label" for="sleptWellLastNight">Yes</label>
            </div>
        </div>
    </div>

    <!-- Health Conditions -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Acute Respiratory Problem</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acuteRespiratoryProblem" name="acuteRespiratoryProblem">
                <label class="form-check-label" for="acuteRespiratoryProblem">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Infected by Hepatitis/Malaria/HIV/AIDS/Veneral Disease</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="infectedByDiseases" name="infectedByDiseases">
                <label class="form-check-label" for="infectedByDiseases">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Venipuncture Site Normal</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="venipunctureSiteNormal" name="venipunctureSiteNormal">
                <label class="form-check-label" for="venipunctureSiteNormal">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Unexplained Weight Loss</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="unexplainedWeightLoss" name="unexplainedWeightLoss">
                <label class="form-check-label" for="unexplainedWeightLoss">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Repeated Diarrhoea</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="repeatedDiarrhoea" name="repeatedDiarrhoea">
                <label class="form-check-label" for="repeatedDiarrhoea">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Swollen Gland</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="swollenGland" name="swollenGland">
                <label class="form-check-label" for="swollenGland">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Under Treatment</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="underTreatment" name="underTreatment">
                <label class="form-check-label" for="underTreatment">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Allergic Reaction</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="allergicReaction" name="allergicReaction">
                <label class="form-check-label" for="allergicReaction">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Antibiotics</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="antibiotics" name="antibiotics">
                <label class="form-check-label" for="antibiotics">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Aspirin</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="aspirin" name="aspirin">
                <label class="form-check-label" for="aspirin">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Alcohol in Last 24 Hours</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="alcohol24Hours" name="alcohol24Hours">
                <label class="form-check-label" for="alcohol24Hours">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>B.P. Medicine/Anti-Depressant</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="bpMedicine" name="bpMedicine">
                <label class="form-check-label" for="bpMedicine">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Ear Piercing/Tattooing</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="earPiercingTattooing" name="earPiercingTattooing">
                <label class="form-check-label" for="earPiercingTattooing">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Dental Extraction</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="dentalExtraction" name="dentalExtraction">
                <label class="form-check-label" for="dentalExtraction">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Major Surgery</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="majorSurgery" name="majorSurgery">
                <label class="form-check-label" for="majorSurgery">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Minor Surgery</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="minorSurgery" name="minorSurgery">
                <label class="form-check-label" for="minorSurgery">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Blood Transfusion</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="bloodTransfusion" name="bloodTransfusion">
                <label class="form-check-label" for="bloodTransfusion">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Steroids</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="steroids" name="steroids">
                <label class="form-check-label" for="steroids">Yes</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Vaccination</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="vaccination" name="vaccination">
                <label class="form-check-label" for="vaccination">Yes</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label>Dog Bite/Rabies Vaccine (1 Year)</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="dogBiteRabiesVaccine" name="dogBiteRabiesVaccine">
                <label class="form-check-label" for="dogBiteRabiesVaccine">Yes</label>
            </div>
        </div>
    </div>

    <!-- Travel History and Recent Conditions -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="travelHistory">Travel History in Last 3 Months</label>
            <input type="text" class="form-control" id="travelHistory" name="travelHistory" placeholder="Travel History">
        </div>
        <div class="form-group col-md-6">
            <label for="infectionHistory">History of Infection or Quarantined in Family/Colleagues</label>
            <input type="text" class="form-control" id="infectionHistory" name="infectionHistory" placeholder="History of Infection">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="signSymptoms">Sign/Symptoms of Fever/Cold/Cough in Last Month</label>
            <input type="text" class="form-control" id="signSymptoms" name="signSymptoms" placeholder="Sign/Symptoms">
        </div>
        <div class="form-group col-md-6">
            <label for="lastDonatedBlood">Time When Last Donated Blood</label>
            <input type="date" class="form-control" id="lastDonatedBlood" name="lastDonatedBlood" placeholder="Time of Last Donation">
        </div>
    </div>

    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
</form>

    </div>
</div>
<?php
if(isset($_POST['submit'])){
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

// Collecting and holding form data in variables
$id_num=mt_rand(00001,99999);
$request_id ="REQ".$id_num; // Adjust the default value as needed
$status= '<span class="badge badge-warning">Pending</span>';
$date=date("d-m-Y");
$name = isset($_POST['name']) ? $_POST['name'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$fatherName = isset($_POST['fatherName']) ? $_POST['fatherName'] : '';
$dob = isset($_POST['dob']) ? $_POST['dob'] : '';
$age = isset($_POST['age']) ? $_POST['age'] : '';
$occupation = isset($_POST['occupation']) ? $_POST['occupation'] : '';
$org = isset($_POST['organization']) ? $_POST['organization'] : '';
$nationality = isset($_POST['nationality']) ? $_POST['nationality'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$mobile = isset($_POST['mobileNo']) ? $_POST['mobileNo'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : ''; 
$height = isset($_POST['height']) ? $_POST['height'] : '';
$weight = isset($_POST['weight']) ? $_POST['weight'] : '';
$bloodGroup = isset($_POST['bloodGroup']) ? $_POST['bloodGroup'] : '';
$rhFactor = isset($_POST['rhFactor']) ? $_POST['rhFactor'] : '';
$hb = isset($_POST['hb']) ? $_POST['hb'] : '';
$regRegularDonor = isset($_POST['regularDonor']) ? "Yes" : 'No';
$donatedBlood = isset($_POST['donatedBlood']) ? "Yes" : 'No';
$discomfort = isset($_POST['discomfort']) ?"Yes" : 'No';
$feelingWell = isset($_POST['feelingWell']) ?"Yes" : 'No';
$eatenInLast4Hours = isset($_POST['eatenInLast4Hours']) ? "Yes" : 'No';
$sleptWell = isset($_POST['sleptWellLastNight']) ?"Yes" : 'No';
$acuteRespiratory = isset($_POST['acuteRespiratoryProblem']) ? "Yes" : 'No';
$infected = isset($_POST['infectedByDiseases']) ?"Yes" : 'No';
$venipunctureNormal = isset($_POST['venipunctureSiteNormal']) ? "Yes" : 'No';
$unexplainedWeightLoss = isset($_POST['unexplainedWeightLoss']) ? "Yes" : 'No';
$repeatedDiarrhoea = isset($_POST['repeatedDiarrhoea']) ? "Yes" : 'No';
$swollenGland = isset($_POST['swollenGland']) ? "Yes" : 'No';
$underTreatment = isset($_POST['underTreatment']) ? "Yes" : 'No';
$allergicReaction = isset($_POST['allergicReaction']) ? "Yes" : 'No';
$antibiotics = isset($_POST['antibiotics']) ? "Yes" : 'No';
$aspirin = isset($_POST['aspirin']) ? "Yes" : 'No';
$alcohol24Hours = isset($_POST['alcohol24Hours']) ? "Yes" : 'No';
$bpMedicine = isset($_POST['bpMedicine']) ? "Yes" : 'No';
$earPiercing = isset($_POST['earPiercingTattooing']) ? "Yes" : 'No';
$dentalExtraction = isset($_POST['dentalExtraction']) ?"Yes" : 'No';
$majorSurgery = isset($_POST['majorSurgery']) ?"Yes" : 'No';
$minorSurgery = isset($_POST['minorSurgery']) ?"Yes" : 'No';
$bloodTransfusion = isset($_POST['bloodTransfusion']) ?"Yes" : 'No';
$steroids = isset($_POST['steroids']) ?"Yes" : 'No';
$vaccination = isset($_POST['vaccination']) ?"Yes" : 'No';
$dogBiteRabies = isset($_POST['dogBiteRabiesVaccine']) ? "Yes" : 'No';
$travelHistory = isset($_POST['travelHistory']) ? $_POST['travelHistory'] : '';
$infectionHistory = isset($_POST['infectionHistory']) ? $_POST['infectionHistory'] : '';
$signSymptoms = isset($_POST['signSymptoms']) ? $_POST['signSymptoms'] : '';
$lastDonatedBlood = isset($_POST['lastDonatedBlood']) ? $_POST['lastDonatedBlood'] : '';

// Prepare and bind
// Prepare the SQL statement
mysqli_query($conn,"INSERT INTO `blood_donor`(`request_id`, `user_id`, `status`, `date`, `name`, `gender`, `fatherName`, `dob`, `age`, `occupation`, `org`, `nationality`, `address`, `mobile`, `email`, `height`, `weight`, `bloodGroup`, `rhFactor`, `hb`, `regRegularDonor`, `donatedBlood`, `discomfort`, `feelingWell`, `eatenInLast4Hours`, `sleptWell`, `acuteRespiratory`, `infected`, `venipunctureNormal`, `unexplainedWeightLoss`, `repeatedDiarrhoea`, `swollenGland`, `underTreatment`, `allergicReaction`, `antibiotics`, `aspirin`, `alcohol24Hours`, `bpMedicine`, `earPiercing`, `dentalExtraction`, `majorSurgery`, `minorSurgery`, `bloodTransfusion`, `steroids`, `vaccination`, `dogBiteRabies`, `travelHistory`, `infectionHistory`, `signSymptoms`, `lastDonatedBlood`) VALUES 
('$request_id','$_SESSION[user_id]', '$status','$date',
'$name', '$gender', '$fatherName', '$dob', '$age', '$occupation', '$org', '$nationality', '$address', '$mobile', 
'$email', '$height', '$weight', '$bloodGroup', '$rhFactor', '$hb', '$regRegularDonor', '$donatedBlood', '$discomfort',
'$feelingWell', '$eatenInLast4Hours', '$sleptWell', '$acuteRespiratory', '$infected', '$venipunctureNormal',
'$unexplainedWeightLoss', '$repeatedDiarrhoea', '$swollenGland', '$underTreatment', '$allergicReaction', '$antibiotics',
'$aspirin', '$alcohol24Hours', '$bpMedicine', '$earPiercing', '$dentalExtraction', '$majorSurgery', '$minorSurgery',
'$bloodTransfusion', '$steroids', '$vaccination', '$dogBiteRabies', '$travelHistory', '$infectionHistory',
'$signSymptoms', '$lastDonatedBlood'
);");



$conn->close();

?>
<script>
    alert("Your Blood Donation Form has been submitted");
    window.location.href="donate.php";
</script>
<?php
}
?>

</body>
</html>