<?php

// include '../db.php'; // Ensure the correct path to db.php
// header('Content-Type: application/json');

// // Database connection
// $servername = "localhost";
// $username = "root";
// $password = "izzy";
// $dbname = "gpr";

//Teha's Database
//include '../db.php'; // Ensure the correct path to db.php

    // Database credentials
    $servername = "sql210.infinityfree.com";
    $username = "if0_38125659";  // default username for MySQL
    $password = "jUcqjO8Y2ocKpE";      // default password is empty for XAMPP
    $dbname = "if0_38125659_gpr"; // your database name

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);
header('Content-Type: application/json');



// Fetch packages from the database
$sql = "SELECT * FROM package";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

$packages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ensure that features are stored as an array (split by commas)
        $features = explode(',', $row['features']);
        
        $packages[] = [
            'name' => $row['PackageName'],          // Package name
            'description' => $row['description'],   // Description
            'price' => $row['PackagePrice'],        // Price
            'image' => $row['image'],               // Image path
            'features' => $features,                // Features list
            'isPopular' => (bool)$row['is_popular'],// Whether it's popular (0 or 1)
            'delay' => $row['delay']                // Delay
        ];
    }
}

echo json_encode($packages);

$conn->close();
?>
