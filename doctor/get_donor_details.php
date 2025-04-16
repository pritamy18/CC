<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: ../index.html');
    exit();
} 
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

// Get request_id from the query string
$request_id = isset($_GET['request_id']) ? $_GET['request_id'] : '';

if ($request_id === '') {
    echo json_encode(["error" => "No request_id provided"]);
    exit;
}

// Fetch donor details
$sql = "SELECT * FROM blood_donor WHERE request_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $request_id);
$stmt->execute();
$result = $stmt->get_result();

// Return JSON response
if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "No records found"]);
}

$stmt->close();
$conn->close();
?>
