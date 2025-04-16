<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST['formType'];
    $emailOrId = isset($_POST['email']) ? $_POST['email'] : $_POST['id'];
    $password = $_POST['password'];

    // Determine the table and columns based on form type
    switch ($formType) {
        case 'user':
            $table = 'users';
            $idColumn = 'user_id';
            $emailColumn = 'email';
            break;
        case 'admin':
            $table = 'admin'; // Ensure you have this table or modify according to your actual admin table
            $idColumn = 'admin_id';
            $emailColumn = 'email';
            break;
        case 'hospital':
            $table = 'hospitals';
            $idColumn = 'hospital_id';
            $emailColumn = 'email';
            break;
        case 'doctor':
            $table = 'doctors';
            $idColumn = 'dr_id';
            $emailColumn = 'email';
            break;
        default:
            echo '<script>alert("Invalid form type"); window.location.href="index.html";</script>';
            exit();
    }

    // Prepare and execute the query
    if (filter_var($emailOrId, FILTER_VALIDATE_EMAIL)) {
        // Login by email
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $emailColumn = ?");
        $stmt->bind_param("s", $emailOrId);
    } else {
        // Login by ID
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $idColumn = ?");
        $stmt->bind_param("s", $emailOrId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Store user information in session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['fullName'] = $user['fullName']; // Common name variable
            
            switch ($formType) {
                case 'user':
                    $_SESSION['user_id'] = $user['user_id'];
                    header('Location: user/userdash.php');
                    break;
                case 'admin':
                    $_SESSION['admin_id'] = $user['admin_id'];
                    header('Location: admin/admindash.php');
                    break;
                case 'hospital':
                    $_SESSION['hospital_id'] = $user['hospital_id'];
                    header('Location: hospital/hospitaldash.php');
                    break;
                case 'doctor':
                    $_SESSION['doctor_id'] = $user['dr_id'];
                    header('Location: doctor/dashboard.php');
                    break;
            }
            exit();
        } else {
            echo '<script>alert("Incorrect password"); window.location.href="index.html";</script>';
        }
    } else {
        echo '<script>alert("No user found with these credentials"); window.location.href="index.html";</script>';
    }
}

$conn->close();
?>
