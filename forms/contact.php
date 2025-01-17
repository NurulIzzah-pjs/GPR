<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set header for JSON response
header('Content-Type: application/json');

// Initialize the response array without the default "error" message
$response = ['status' => '', 'message' => ''];

// Check if the form is submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // Validate form fields (basic validation)
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $response['status'] = 'error';
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit();
    }

    // Database credentials
    $servername = "localhost";
    $username = "root"; // Default for XAMPP
    $password = ""; // Default for XAMPP
    $dbname = "gpr"; // Your database name

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $response['status'] = 'error';
        $response['message'] = 'Database connection failed: ' . $conn->connect_error;
        echo json_encode($response);
        exit();
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO `contactmessages` (`name`, `email`, `subject`, `message`) VALUES (?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        // Execute the query
        if ($stmt->execute()) {
            $response['status'] = 'success'; // Success status
            $response['message'] = ''; // No message needed for success
        } else {
            $response['status'] = 'error'; // In case of failure in execution
            $response['message'] = 'Failed to save the message: ' . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        $response['status'] = 'error'; // In case of failure in preparing SQL
        $response['message'] = 'Failed to prepare SQL statement: ' . $conn->error;
    }

    // Close the connection
    $conn->close();
} else {
    // Handle invalid request methods
    http_response_code(405); // Method Not Allowed
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method. Use POST.';
}

// Send JSON response
echo json_encode($response);
