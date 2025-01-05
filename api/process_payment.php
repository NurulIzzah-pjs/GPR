<?php
include '../db.php'; // Ensure the correct path to db.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gpr";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize POST data
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'] ?? '100.00';  // Default to 100.00 if not provided

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

        // Redirect to the success page with Transaction ID
        header('Location: /GPR/Templates/success_payment.html?transaction_id=' . $transaction_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
