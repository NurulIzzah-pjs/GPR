<?php
session_start();
include '../db.php'; // Ensure the correct path to db.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // // Connect to the database
    // $servername = "localhost";
    // $username = "root";
    // $password = "izzy";
    // $dbname = "gpr";

    // $conn = new mysqli($servername, $username, $password, $dbname);

    // // Check connection
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }


    // Sanitize POST data
    $payment_method = $_POST['payment_method'];
    $package_type = $_SESSION['package'] ?? ''; // Check if package exists in session

    // Check if package data exists
    if (empty($package_type)) {
        echo json_encode(['error' => 'No package selected.']);
        exit();
    }

    // Set amount based on package_type
    switch ($package_type) {
        case 'basic':
            $amount = 15.00;
            break;
        case 'lite':
            $amount = 35.00;
            break;
        case 'pro':
            $amount = 50.00;
            break;
    }

    // Initialize variables for debit card details
    $name_on_card = $_POST['name_on_card'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $exp_month = $_POST['exp_month'] ?? '';
    $exp_year = $_POST['exp_year'] ?? '';

    // Handle different payment methods
    if ($payment_method == 'Debit Card') {
        $stmt = $conn->prepare("INSERT INTO payments (payment_method, amount, name_on_card, card_number, cvv, expiration_date) VALUES (?, ?, ?, ?, ?, ?)");
        $expiration_date = $exp_year . '-' . $exp_month . '-01'; // Date format: YYYY-MM-DD
        $stmt->bind_param("sdssss", $payment_method, $amount, $name_on_card, $card_number, $cvv, $expiration_date);
    } else {
        // For Online Banking or E-Wallet, no debit card details are required
        $stmt = $conn->prepare("INSERT INTO payments (payment_method, amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $payment_method, $amount);
    }

    
    if ($stmt->execute()) {
        // Get the last inserted Transaction ID
        $transaction_id = $conn->insert_id;

        // Check if user IdentificationNum is set in session
        if (empty($_SESSION['IdentificationNum'])) {
            echo json_encode(['error' => 'User not logged in or Identification number missing.']);
            exit();
        }

        // Retrieve the user's identification number from session
        $ic = $_SESSION['IdentificationNum'];

        // Prepare update statement for participant table
        $update_stmt = $conn->prepare("UPDATE participant SET PaymentID = ? WHERE IdentificationNum = ?");
        if ($update_stmt === false) {
            die('Error in preparing update statement: ' . $conn->error);
        }

        // Bind the parameters and execute the update statement
        $update_stmt->bind_param("is", $transaction_id, $ic);

        if ($update_stmt->execute()) {
            // Redirect to the success page with Transaction ID
            header('Location: ../Templates/success_payment.html?transaction_id=' . $transaction_id);
            exit();
        } else {
            echo "Error updating participant: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }


    // Close connection
    $stmt->close();
    $conn->close();
}
?>
