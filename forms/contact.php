<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Prepare the response array
$response = ['status' => 'error', 'message' => 'Something went wrong.'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Database credentials
    $servername = "localhost";
    $username = "root";  // default username for MySQL
    $password = "";      // default password is empty for XAMPP
    $dbname = "gpr"; // your database name

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $response['message'] = 'Connection failed: ' . $conn->connect_error;
        echo json_encode($response);
        exit();
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO `contactmessages` (`name`, `email`, `subject`, `message`) VALUES (?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $name, $email, $subject, $message); // "ssss" stands for four string parameters

        // Execute the query
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = ''; // No message shown, just success
        }

        // Close statement
        $stmt->close();
    } else {
        $response['message'] = 'Error preparing the SQL statement: ' . $conn->error;
    }

    // Close connection
    $conn->close();
}

// Send the response back as JSON
echo json_encode($response);
?>
