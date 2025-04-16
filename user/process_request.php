<?php
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $bloodType = $_POST['bloodType'];
    $unitsRequested = $_POST['unitsRequested'];
    $hospitalName = $_POST['hospitalName'];
    $hospitalID = $_POST['hospitalID'];
    $urgencyLevel = $_POST['urgencyLevel'];

    // Prepare and bind the insert statement
    $stmt = $conn->prepare("INSERT INTO blood_request (blood_type, units_requested, hospital_name, hospital_id, urgency_level) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $bloodType, $unitsRequested, $hospitalName, $hospitalID, $urgencyLevel);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back or display a success message
        echo "<div class='alert alert-success'>Blood request submitted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
