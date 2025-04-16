<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
} 
// Database connection parameters
$servername = "localhost"; // your server
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "blood_donation"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Specify the doctor ID
$dr_id = $_SESSION['doctor_id'];
$request_id =$_POST['request_id'];
// Prepare the SQL statement to get the blood type from blood_donor table
$stmt = $conn->prepare("SELECT bloodGroup FROM blood_donor WHERE request_id = ?");
$stmt->bind_param("i", $request_id); // "i" indicates that the parameter is an integer

// Execute the statement
$stmt->execute();

// Bind the result to a variable
$stmt->bind_result($type);

// Fetch the result
if (!$stmt->fetch()) {
    die("No hospital found for Doctor ID $dr_id.");
}

// Close the statement
$stmt->close();

// Function to generate a unique blood ID
function generateBloodID() {
    return uniqid('BID', true); // Generates a unique ID
}

// Data to be inserted
$status ='<span class="badge badge-success">Donated</span>';; // Example status
$amount = $_POST['amount']; 
$entrydate = date('Y-m-d H:i:s');
$hospital_id=$_POST['hospital_id'];
// Generate a new blood ID
$blood_id = generateBloodID();

// Prepare and bind the insert statement
$status5="Available";
 // Validate input (basic validation)
 if (empty($request_id) || empty($hospital_id) || empty($amount)) {
    die("All fields are required.");
}
$stmt = $conn->prepare("INSERT INTO `blood` (`blood_id`, `request_id`, `hospital_id`, `dr_id`, `status`, `type`, `amount`, `entrydate`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $blood_id, $request_id, $hospital_id, $dr_id, $status5, $type, $amount, $entrydate);

// Execute the insert statement
if ($stmt->execute()) {
    echo "New record created successfully with Blood ID: " . $blood_id;

    // Prepare and bind the update statement for the blood_donor table
    $update_stmt = $conn->prepare("UPDATE blood_donor SET status = '$status', dr_id='$dr_id', hospital_id='$hospital_id' ,blood_id='$blood_id'  WHERE request_id = ?");
    $update_stmt->bind_param("s", $request_id); // Assuming donor_id is an integer

    // Execute the update statement
    if ($update_stmt->execute()) {
        echo "Status of donor ID $request_id updated to 'donated'.";
        header("Location: blood-inventory.php"); 
    } else {
        echo "Error updating donor status: " . $update_stmt->error;
    }

    // Close the update statement
    $update_stmt->close();
} else {
    echo "Error inserting blood record: " . $stmt->error;
}

// Close the insert statement and connection
$stmt->close();
$conn->close();
?>