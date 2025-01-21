<?php 
session_start(); 

// Session timeout logic 
$inactive = 15; // testing 15 seconds 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) { 
    session_unset();  
    session_destroy();  
    header("Location: latestlogin.php"); 
    exit(); 
} 
$_SESSION['last_activity'] = time(); 

// Check if the user is logged in 
if (!isset($_SESSION['user_username'])) { 
    header("Location: latestlogin.php"); 
    exit(); 
} 
?> 