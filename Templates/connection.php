<?php
$servername = "localhost";  // Change to your server if not localhost
$username = "root";         // Database username
$password = "izzy";             // Database password (empty for XAMPP default)
$dbname = "gpr"; // Replace with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
