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
// Generate unique ID function with prefixes
function generateUniqueId($conn, $table, $prefix) {
    while (true) {
        // Generate a numeric part with a random number
        $numericPart = random_int(1000000, 9999999); // 7-digit number
        $id = $prefix . $numericPart;

        // Adjust the column name based on the table
        $columnName = $prefix === 'H' ? 'hospital_id' : ($prefix === 'dr' ? 'dr_id' : 'user_id');

        $result = $conn->query("SELECT * FROM $table WHERE $columnName='$id'");
        if ($result->num_rows == 0) {
            return $id;
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['formType'])) {
        $formType = $_POST['formType'];
        $password = $_POST['password'];
        
        switch ($formType) {
            case 'user':
                $id = generateUniqueId($conn, 'users', 'USER');
                $fullName = $_POST['fullName'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $dob = $_POST['dob'];
                $location = $_POST['location'];
                $bloodType = $_POST['bloodType'];
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $sql = "INSERT INTO users (user_id, fullName, email, phone, dob, location, bloodType, password) VALUES ('$id', '$fullName', '$email', '$phone', '$dob', '$location', '$bloodType', '$passwordHash')";
                break;

            case 'doctor':
                $id = generateUniqueId($conn, 'doctors', 'dr');
                $fullName = $_POST['fullName'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $specialization = $_POST['specialization'];
                $hospital = $_POST['hospital'];
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $sql = "INSERT INTO doctors (dr_id, fullName, email, phone, specialization, hospital, password) VALUES ('$id', '$fullName', '$email', '$phone', '$specialization', '$hospital', '$passwordHash')";
                break;

            case 'hospital':
                $id = generateUniqueId($conn, 'hospitals', 'H'); // Prefix changed to 'H'
                $hospitalName = $_POST['hospitalName'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $address = $_POST['address'];
                $type = $_POST['type'];
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $sql = "INSERT INTO hospitals (hospital_id, hospitalName, email, phone, address, type, password) VALUES ('$id', '$hospitalName', '$email', '$phone', '$address', '$type', '$passwordHash')";
                break;

            default:
                echo "Invalid form type";
                exit();
        }

        if ($conn->query($sql) === TRUE) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $_POST['fullName'];
            $_SESSION['id'] = $id;
            
            // Redirect based on form type
            switch ($formType) {
                case 'user':
                    header('Location: user/userdash.php');
                    break;
                case 'doctor':
                    header('Location: doctor/dashboard.php');
                    break;
                case 'hospital':
                    header('Location: hospital/hospitaldash.php');
                    break;
            }
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Form type not specified";
    }
}

$conn->close();
?>
