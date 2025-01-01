<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    include "connection.php"; // Adjust the path as necessary
    
    if ($conn->connect_error) {
      error_log("Database connection failed: " . $conn->connect_error);
  }  

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE AdminUsername = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
      if ($password == $row["AdminPassword"]) {
            $_SESSION['user_username'] = $username;
            header("Location: admin_dashboard.html");
            exit();
        } else {
            echo '<script>
            alert("Invalid username or password!");
            window.location.href = "adminlogin.php";
            </script>';
        }
    } else {
        echo '<script>
        alert("Invalid username or password!");
        window.location.href = "adminlogin.php";
        </script>';
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Login</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">
  <link href="../assets/css/login.css" rel="stylesheet">
  <style>

  </style>
</head>

<body class="login-body" data-lang="en">

    <header id="header" class="header d-flex align-items-center px-3 sticky-top">
        <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
    
          <a href="../index.html" class="logo d-flex align-items-center">
            <!-- Uncomment the line below if you also wish to use an image logo -->
            <!-- <img src="assets/img/logo.png" alt=""> -->
            <h1 class="sitename">3KPP USM</h1>
          </a>
          <nav id="navmenu" class="navmenu">
            <ul>
              <li><a href="index.html" class="active">Home</a></li>
              <li><a href="../#about">About</a></li>
              <li><a href="../#contact">Contact</a></li>
              <li><a href="../#payment">Payment</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
          </nav>
    
        </div>
    </header>

    <section id="login" class="login section transparent-background">
       <div class="container form">
        <div class="section-title">
          <h2>Admin</h2>
          <h2>Log In</h2>
          <p>Please log in using your username and password</p>
        </div>
        <form class="log-in-form" method="POST" action="adminlogin.php">
          <div class="fillin">
            <div class="col-md-6">
              <label for="username" class="u-label">Username</label>
              <input type="text" id="username" name="username" required>
            </div>
            <div class="col-md-6">
              <label for="pass" class="u-label">Password</label>
              <input type="password" id="pass" name="pass" required>
            </div>
            <div class="col-md-6">
              <!-- <a class="btn" href="admin_dashboard.html" type="button">Log In</a> -->
              <button type="submit" name="submit" class="btn">Log In</button>
            </div>
          </div>
        </form>
       </div>
    </section> 

    <footer id="footer" class="footer transparent-background">
      <div class="container">
        <div class="copyright text-center ">
          <p>© <span>Copyright</span> <strong class="px-1 sitename">ComingSoon</strong> <span>All Rights Reserved</span></p>
        </div>
        <div class="social-links d-flex justify-content-center">
          <a href="https://m.facebook.com/profile.php?id=100085816266970"><i class="bi bi-facebook"></i></a>
          <a href="https://www.instagram.com/3kpp_usm/?hl=en"><i class="bi bi-instagram"></i></a>
          <a href="https://www.tiktok.com/@3kpp_usm?_t=8rUfpqcwQLv&_r=1"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>
    </footer>
    
</body>
</html>
