<?php
session_start(); // Start the session at the top

// Check if a session already exists
if (isset($_SESSION['user_id'])) {
    header("Location: participant_dash.php"); // Redirect logged-in users
    exit();
}

if (isset($_POST['submit'])) {
    include "connection.php";
    include "../phpqrcode/qrlib.php"; // Include the QR code library

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Retrieve and sanitize form inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phonenum = mysqli_real_escape_string($conn, $_POST['phonenum']);
    $ic = mysqli_real_escape_string($conn, $_POST['ic']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $matricnum = isset($_POST['matricnum']) ? mysqli_real_escape_string($conn, $_POST['matricnum']) : null;
    $school = isset($_POST['school']) ? mysqli_real_escape_string($conn, $_POST['school']) : null;
    $campus = isset($_POST['campus']) ? mysqli_real_escape_string($conn, $_POST['campus']) : null;
    $package = mysqli_real_escape_string($conn, $_POST['package']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);

    // Validate required fields
    if (empty($name) || empty($phonenum) || empty($ic) || empty($role) || empty($package) || empty($username) || empty($password)) {
        echo '<script>
        alert("All required fields must be filled!");
        window.location.href = "registration.php";
        </script>';
        exit();
    }

    // Check if the user already exists
    $sql = "SELECT * FROM participant WHERE IdentificationNum = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ic);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Verify and fetch PackageID
        $sql = "SELECT PackageID FROM package WHERE PackageCode = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $package);
        $stmt->execute();
        $packageResult = $stmt->get_result();

        if ($packageResult->num_rows > 0) {
            $packageRow = $packageResult->fetch_assoc();
            $packageID = $packageRow['PackageID']; // Fetch PackageID
        } else {
            echo '<script>
            alert("Invalid package selected.");
            window.location.href = "registration.php";
            </script>';
            exit();
        }

        // Hash the password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Generate the QR code and save it
        $qrCodeDirectory = dirname(__FILE__) . "/qrcodes";
        if (!is_dir($qrCodeDirectory)) {
            mkdir($qrCodeDirectory, 0777, true); // Create the directory if it doesn't exist
        }

        // File path for the QR code
        $qrFileName = $qrCodeDirectory . DIRECTORY_SEPARATOR . $ic . ".png";
        $qrData = "Name: $name, IC: $ic"; // Custom data to encode in the QR code
        QRcode::png($qrData, $qrFileName, QR_ECLEVEL_L, 4);
        $relativeQRPath = "qrcodes/" . $ic . ".png"; // Store relative path for portability

        // Insert participant data into the database
        $sql = "INSERT INTO participant (Name, PhoneNum, IdentificationNum, Role, MatricNum, Campus, School, PackageID, Username, Password, QRCodeStu)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssss", 
            $name, 
            $phonenum, 
            $ic, 
            $role, 
            $matricnum, 
            $campus, 
            $school, 
            $packageID, 
            $username, 
            $hash, 
            $relativeQRPath
        );

        if ($stmt->execute()) {
            // Redirect to payment page
            header("Location: payment.html");
            exit();
        } else {
            echo '<script>
            alert("Failed to register. Please try again.");
            window.location.href = "registration.php";
            </script>';
        }
    } else {
        // User already exists
        echo '<script>
        alert("User already exists!!!");
        window.location.href = "participantlogin.php";
        </script>';
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en" style="font-size: 16px;">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="keywords" content="Book an Appointment, INTUITIVE">
    <title>Join US</title>
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
    <link href="../assets/css/joinus.css" rel="stylesheet">
</head>

<body class="join-body" data-lang="en">

    <header id="header" class="header d-flex align-items-center px-3 sticky-top">
        <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
    
          <a href="../index.html" class="logo d-flex align-items-center">
            <!-- Uncomment the line below if you also wish to use an image logo -->
            <!-- <img src="assets/img/logo.png" alt=""> -->
            <h1 class="sitename">3KPP USM</h1>
          </a>
          <nav id="navmenu" class="navmenu">
            <ul>
              <li><a href="../index.html" class="active">Home</a></li>
              <li><a href="../#about">About</a></li>
              <li><a href="../#contact">Contact</a></li>
              <li><a href="../#payment">Payment</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
          </nav>
    
        </div>
    </header>

  <section id="register" class="register section transparent-background">
    <div  class="container section-title">
        <h2>Register Now !</h2>
    </div>
    <div class="container form">
        <div class="row gy-4">
            <div class="col-lg-6">
                <form action="registerqr.php" method="POST" class="php-email-form">
                    <div class="col-md-6">
                        <label for="name" class="u-label">Name</label>
                        <input type="text" placeholder="Enter your Name" id="name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="u-label">Phone</label>
                        <input type="text" placeholder="Enter your number phone" id="phone" name="phonenum"  required>
                    </div>
                    <div class="col-md-6">
                        <label for="ic" class="u-label">Identification Card</label>
                        <input type="text" placeholder="Enter your IC" id="ic" name="ic" required>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="u-label">Role</label>
                        <select id="role" name="role" required>
                            <option value="" disabled selected>Select your role</option>
                            <option value="student">Student</option>
                            <option value="outsider">Non-Student</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="matricnum" class="u-label" name="matricnum"  style="display: none;">No.Matric</label>
                        <input type="text" placeholder="Enter your No. Matric" id="matricnum" name="matricnum"  style="display: none;" required>
                    </div>
                    <div class="col-md-6">
                        <label for="school" class="u-label" style="display: none;">School</label>
                        <input type="text" placeholder="Enter your school" id="school" name="school"  style="display: none;" required>
                    </div>
                    <div class="col-md-6">
                        <label for="campus" class="u-label" style="display: none;">Campus</label>
                        <select id="campus" name="campus"  style="display: none;"required>
                            <option value="" disabled selected>Select your campus</option>
                            <option value="induk">USM INDUK</option>
                            <option value="kejut">USM KEJURUTERAAN</option>
                            <option value="kesit">USM KESIHATAN</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="package" class="u-label">Package</label>
                        <select id="package" name="package" required>
                            <option value="" disabled selected>Select your package</option>
                            <option value="basic">GLOW-RIOUS STARTER</option>
                            <option value="lite">GLOW-RIOUS LITE</option>
                            <option value="pro">GLOW-RIOUS PRO</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="username" class="u-label">Username</label>
                        <input type="text" placeholder="Create Username" id="username" name="username"  required>
                    </div>
                    <div class="col-md-6">
                        <label for="pass" class="u-label">Password</label>
                        <input type="password" placeholder="Create Password" id="pass" name="pass"  required>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="submit" class="btn">Register</button>
                    </div>
                </form>
            </div>
        </div>
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

  <script>
    document.getElementById('role').addEventListener('change', function() {
    const matricnum = document.getElementById('matricnum');
    const matricnumLabel = document.querySelector('label[for="matricnum"]'); // Label for "No. Matric"
    const school = document.getElementById('school');
    const schoolLabel = document.querySelector('label[for="school"]'); // Label for "School"
    const campus = document.getElementById('campus');
    const campusLabel = document.querySelector('label[for="campus"]'); // Label for "Campus"
    const packageSelect = document.getElementById('package'); // Package select field

    // Package options
    const studentPackages = `
        <option value="" disabled selected>Select your package</option>
        <option value="basic">GLOW-RIOUS STARTER</option>
        <option value="lite">GLOW-RIOUS LITE</option>
        <option value="pro">GLOW-RIOUS PRO</option>
    `;
    const nonStudentPackages = `
        <option value="" disabled selected>Select your package</option>
        <option value="pro">GLOW-RIOUS PRO</option>
    `;

    if (this.value === 'student') {
        // Show fields for "student"
        matricnum.style.display = 'block';
        matricnumLabel.style.display = 'block';
        matricnum.setAttribute('required', 'required');
        school.style.display = 'block';
        schoolLabel.style.display = 'block';
        school.setAttribute('required', 'required');
        campus.style.display = 'block';
        campusLabel.style.display = 'block';
        campus.setAttribute('required', 'required');
        packageSelect.innerHTML = studentPackages; // Update package options for students
    } else {
        // Hide fields for non-student
        matricnum.style.display = 'none';
        matricnumLabel.style.display = 'none';
        matricnum.removeAttribute('required');
        school.style.display = 'none';
        schoolLabel.style.display = 'none';
        school.removeAttribute('required');
        campus.style.display = 'none';
        campusLabel.style.display = 'none';
        campus.removeAttribute('required');
        packageSelect.innerHTML = nonStudentPackages; // Update package options for non-students
    }
});

</script>



  </script>

</body>
</html>
