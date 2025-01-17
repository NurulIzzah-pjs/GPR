<?php
include "session_config.php";
include "session_check.php";
include "../db.php"; // Ensure this path is correct

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch user data (e.g., by session user ID)
if (isset($_SESSION['user_username']) && $_SESSION['user_type'] === 'participant') {
    $user_username = $_SESSION['user_username'];
    
    $user_query = "SELECT Name FROM participant WHERE Username= ?";
    $stmt = $conn->prepare($user_query);
    
    if ($stmt) {
        $stmt->bind_param("s", $user_username); 
        $stmt->execute();
        $user_result = $stmt->get_result();
        
        if ($user_result && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $name = $user['Name']; // Ensure this matches your database column case
        } else {
            $name = "Guest"; // Fallback if no name is found
        }

        $stmt->close();
    } else {
        $name = "Error fetching name"; // Fallback if statement fails
    }
} else {
    $name = "Guest"; // Fallback if session variable is not set
}

// Fetch the package details for the user
$package_query = "SELECT package.PackageName, package.PackagePrice, package.features
FROM package
JOIN participant ON package.PackageID = participant.PackageID
WHERE participant.Username = ?";
$stmt = $conn->prepare($package_query);

if ($stmt) {
    $stmt->bind_param("s", $user_username); 
    $stmt->execute();
    $package_result = $stmt->get_result();

    if ($package_result && $package_result->num_rows > 0) {
        $package = $package_result->fetch_assoc();
        $package_name = $package['PackageName']; // Get package name
        $price = $package['PackagePrice']; // Get package price
        $features = $package['features']; //Get package feature
    }

    $stmt->close();

}
// Fetch QR code path for the logged-in user
$qrQuery = "SELECT QRCodeStu FROM participant WHERE Username = ?";
$stmt = $conn->prepare($qrQuery);

if ($stmt) {
    $stmt->bind_param("s", $user_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $qrFilePath = $row['QRCodeStu'];
    } else {
        $qrFilePath = 'fallback-image.jpg'; // Set to a fallback image if no QR code is found
    }

    $stmt->close();
}

//Fetch schedule details
$schedule_query = "SELECT ActivityName, ActivityStart, ActivityEnd, Location FROM schedules ORDER BY ActivityStart";
$schedule_result = $conn->query($schedule_query);
$schedules = array();

if ($schedule_result && $schedule_result->num_rows > 0) {
    while ($row = $schedule_result->fetch_assoc()) {
        $schedules[] = $row;
    }
}

//Fetch event details
$event_query = "SELECT EventName, Location, StartDate, EndDate FROM eventdetails";
$event_result = $conn->query($event_query);

if ($event_result && $event_result->num_rows > 0) {
    $event = $event_result->fetch_assoc();

    // Format the date and time
    $event_name = $event['EventName'];
    $event_location = $event['Location'];
    $start_date = date("jS F Y", strtotime($event['StartDate']));
    $start_time = date("g:i A", strtotime($event['StartDate']));
    $end_time = date("g:i A", strtotime($event['EndDate']));
}

$conn->close();

// Display the last login time using the cookie
if (isset($_COOKIE['last_login'])) {
    $lastLoginMessage = "Your last visit was on " . htmlspecialchars($_COOKIE['last_login']);
} else {
    $lastLoginMessage = "This is your first login or cookies are not enabled.";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard</title>
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
  <link href="../assets/css/qr.css" rel="stylesheet">
  <style>
    body {
        font-family: var(--default-font);
    }

    .container {
        margin-top: 20px;
    }

    .container .row {
        display: flex;
        align-items: stretch; /* Ensures all cards in the row stretch to the same height */
    }

    .container .col-md-6, 
    .container .col-md-3, 
    .container .col-md-9 {
    display: flex;
    flex-direction: column;
    }

    .personal-card {
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        color: white;
        background: var(--surface-color);
        border-radius: 1rem;
        transition: all 0.3s ease;
        position: relative;
        flex-grow: 1; /* Allow the card to grow and fill available space */
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Ensures content is spaced inside the card */
        width: 100%;
        margin: 0;
    }

    .personal .personal-card:hover {
        box-shadow: 0 10px 30px rgba(255, 255, 255, 0.555);
    }
    .personal-personal h4 {
        margin-bottom: 10px;
    }

    .personal-card p {
        font-size: 20px;
    }

    .personal-card button {
        background-color: #b29dff;
        border: none;
        padding: 8px 15px;
        border-radius: 10px;
        cursor: pointer;
    }
    .personal-card .card-title {
    font-size: 28px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    border-bottom: 3px solid #b29dff; /* Adjust color and thickness */
    padding-bottom: 10px; /* Space between text and line */
    }

    h3 {
    color: white;
    font-family: var(--default-font);
    text-align: center;
    }

    .personal .personal-card .features-list {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
    }

.personal .personal-card .features-list li {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.personal .personal-card .features-list li i {
  color: var(--accent-color);
  margin-right: 0.75rem;
  font-size: 1.25rem;
}

    /* Table Styling */
.schedule-table {
    width: 100%; /* Full width inside the card */
    border-collapse: collapse; /* Merge table borders */
    margin: 10px 0;
    font-size: 14px; /* Adjust table font size */
    text-align: left; /* Align text to the left */
    background-color: rgba(255, 255, 255, 0.301); /* Dark background for table */
    border-radius: 8px; /* Optional: Add rounded corners */
    overflow: hidden; /* Ensure border radius applies to content */
}

.schedule-table th {
    background-color: rgba(0, 0, 0, 0.6); /* Darker background for headers */
    color: white; /* Header text color */
    font-weight: bold; /* Bold header */
    padding: 12px 8px; /* Spacing */
    border-bottom: 1px solid rgb(0, 0, 0); /* Light cell borders */

}

.schedule-table td {
    background-color: rgba(255, 255, 255, 0.5); /* Lighter background for cells */
    padding: 10px 8px; /* Cell spacing */
    color: rgb(0, 0, 0); /* Cell text color */
    
}

/* Alternating Row Colors */
.schedule-table tbody tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.6); /* Slightly different for even rows */
}

/* Hover Effect */
.schedule-table tr:hover {
    background-color: rgba(255, 255, 255, 0.2); /* Highlight row on hover */
}

.modal {
  display: none; /* Hidden by default */
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto; /* Enable scroll if needed */
  background-color: rgba(0, 0, 0, 0.8); /* Black with opacity */
}

.modal-content {
  margin: 10% auto;
  display: flex;
  justify-content: center;
  align-items: center;
  max-width: 80%;
  animation: fadeIn 0.3s;
}

#enlarged-qr {
  width: 50%; /* Adjust the percentage as needed for a larger image */
  height: auto; /* Maintain aspect ratio */
  max-width: 90%; /* Prevent it from exceeding the viewport */
  max-height: 90vh; /* Prevent it from exceeding the viewport height */
  border-radius: 8px; /* Optional: Keep rounded corners */
}
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #fff;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

.btnjoinus {
    display: flex;
    justify-content: center; /* Centers the button horizontally */
    margin-top: 20px; /* Adjusts spacing from other elements */
}

.notification {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(90deg, rgba(173,24,237,1) 0%, rgba(255,105,180,1) 100%);
    color: white;
    font-family: 'Arial', sans-serif;
    font-size: 16px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    display: none;
    text-align: center;
    animation: fadeIn 1s ease-out;
}

.notification button {
    background: #fff;
    color: #ad18ed;
    border: none;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 15px;
}

.notification button:hover {
    background: #ad18ed;
    color: #fff;
    transition: 0.3s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

  </style>
</head>

<body class="index-page">
    <!-- Notification Popup -->
    <div id="notification" class="notification">
    <span id="notification-message"></span>
    <button onclick="closeNotification()">Close</button>
</div>


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
          <li><a href="../#hero">About</a></li>
          <li><a href="../#hero">Contact</a></li>
          <li><a href="#" onclick="confirmLogout()">Logout</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>
  <main class="main">
    <section id="personal" class="personal section transparent-background">

    <!-- Section Title -->
    <div class="section-title" data-aos="fade-up">
      <h2>Welcome,  <?php echo htmlspecialchars($name); ?>!</h2>
      <h3>Here is Personalized Dashboard. Enjoy & Have Fun!</h3>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row">
          <!-- Left Side: Package Container -->
          <div class="col-lg-6">
              <div class="personal-card">
                  <h3><?php echo htmlspecialchars($package_name); ?></h3>
                  <div class="price">
                      <span class="currency">RM</span>
                      <span class="amount"><?php echo htmlspecialchars($price); ?></span>
                      <span class="period">/ person</span>
                  </div>
                  <img src="../assets/img/starterpack.png" alt="Glow Starter Package" class="description-image">
                  
                  <?php
                  $features_list = explode (',', $features); //split features by comma
                  ?>
  
                  <h4>Running Kit Included:</h4>
                  <ul class="features-list">
                    <?php foreach ($features_list as $features):?>
                        <li><i class="bi bi-check-circle-fill"></i><?php echo htmlspecialchars(trim($features)); ?></li>
                    <?php endforeach;?>
                      <!-- <li><i class="bi bi-check-circle-fill"></i> LED Stick</li>
                      <li><i class="bi bi-check-circle-fill"></i> Refreshments</li>
                      <li><i class="bi bi-check-circle-fill"></i> Drawstring Bag</li>
                      <li><i class="bi bi-check-circle-fill"></i> Face Paint Service</li>
                      <li><i class="bi bi-check-circle-fill"></i> Lucky Draw Ticket</li>
                      <li><i class="bi bi-check-circle-fill"></i> Wristband</li> -->
                  </ul>
              </div>
          </div>
  
          <!-- Right Side: Event Info and QR Code -->
          <div class="col-lg-6 d-flex flex-column">
              <div class="personal-card mb-3">
                  <h3 class="card-title">Event Information</h3>
                  <p style="font-size: 2.5rem; text-align: center;"><strong><?php echo $event_name;?></strong></p>
                  <p style="font-size: 1.8rem; text-align: center;"><strong><?php echo $start_date;?></strong></p>
                  <p style="font-size: 1.8rem; text-align: center;"><strong><?php echo $start_time . "-" . $end_time;?></strong></p>
                  <p style="font-size: 1.8rem; text-align: center;"><strong><?php echo $event_location; ?></strong></p>
              </div>
              <div class="personal-card">
                  <h3 class="QR-text">Your QR Code</h3>
                  <div id="box" class="text-center">
                  <img src="<?php echo htmlspecialchars($qrFilePath); ?>" alt="QR Code" id="qr-image" onerror="this.src='fallback-image.jpg';">
                  <p style="color: #333; font-size: 14px; margin-top: 10px;">Scan to view details</p>
                      <button class="btn btn-primary" id="download-btn">Download</button>
                      <button class="btn btn-primary" id="enlarge-btn">Enlarge</button>
                  </div>

                  <!-- Modal -->
                  <div id="qrModal" class="modal">
                    <div class="modal-content">
                      <span class="close">&times;</span>
                      <img id="enlarged-qr" src="<?php echo htmlspecialchars($qrFilePath); ?>" alt="Enlarged QR Code">
                      </div>
                  </div>
              </div>
          </div>
      </div>
  
      <!-- Full-Width Row: Event Schedule -->
       <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">
        <div class="row mt-4">
          <div class="col-12 w-100">
              <div class="personal-card">
                  <h3 class="card-title">Event Schedules</h3>
                  <table class="table table-striped">
                      <thead>
                          <tr>
                              <th>Time</th>
                              <th>Activity</th>
                              <th>Location</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php foreach ($schedules as $schedule): ?>
                              <tr>
                                  <td><?php echo date('h:i A', strtotime($schedule['ActivityStart'])); ?></td>
                                  <td><?php echo htmlspecialchars($schedule['ActivityName']); ?></td>
                                  <td><?php echo htmlspecialchars($schedule['Location']); ?></td>
                              </tr>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                  <!-- <table class="schedule-table w-100">
                      <thead>
                          <tr>
                              <th scope="col">Time</th>
                              <th scope="col">Activity</th>
                              <th scope="col">Location</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td>7.30 PM</td>
                              <td>Registration Opens</td>
                              <td>In front of The Brick</td>
                          </tr>
                          <tr>
                              <td>8.00 PM</td>
                              <td>Warm Up</td>
                              <td>In front of The Brick</td>
                          </tr>
                          <tr>
                              <td>8.15 PM</td>
                              <td>Flag Off</td>
                              <td>Starting Line</td>
                          </tr>
                          <tr>
                              <td>10.00 PM</td>
                              <td>Participant Finish</td>
                              <td>Finish Line</td>
                          </tr>
                          <tr>
                              <td>10.15 PM</td>
                              <td>Medal Distribution</td>
                              <td>Medal Counter</td>
                          </tr>
                      </tbody>
                  </table> -->
              </div>
          </div>
      </div>
       </div>
      
  </div>
  <div class="btnjoinus"> 
    <a class="btn btn-light" href="logout.php" role="button" style="background-color: #ad18ed; color: white; margin-top: 20px;">Log Out</a>
  </div>
    </section>
  </main>

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

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>


  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>

  <script>
document.addEventListener("DOMContentLoaded", function () {
    const lastLoginMessage = "<?php echo addslashes($lastLoginMessage); ?>";

    if (lastLoginMessage) {
        const notification = document.getElementById("notification");
        const messageSpan = document.getElementById("notification-message");

        messageSpan.textContent = lastLoginMessage;
        notification.style.display = "block";
    }
});

function closeNotification() {
    const notification = document.getElementById("notification");
    notification.style.display = "none";
}
</script>



  <script>
    document.getElementById("download-btn").addEventListener("click", function () {
    var qrImage = document.getElementById("qr-image");

    // Check if the image source is available
    if (qrImage.src) {
        var link = document.createElement("a");
        link.href = qrImage.src;
        link.download = "GPR_QR_Code.jpeg"; // Ensure the file has an extension
        link.click();
    } else {
        alert("QR code image is not available!");
    }
});
</script>

<script>
 // Get the modal
 var modal = document.getElementById("qrModal");

// Get the modal image
var enlargedQR = document.getElementById("enlarged-qr");

// Get the "Enlarge" button
var enlargeBtn = document.getElementById("enlarge-btn");

// Get the <span> element to close the modal
var closeModal = document.getElementsByClassName("close")[0];

// When the user clicks on the "Enlarge" button, open the modal
enlargeBtn.onclick = function () {
  modal.style.display = "block";
  enlargedQR.src = document.getElementById("qr-image").src; // Set the modal image to the QR code source
};

// When the user clicks on <span> (x), close the modal
closeModal.onclick = function () {
  modal.style.display = "none";
};

// Close the modal if the user clicks outside the image
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'logout.php';
        }
    }

</script>


</body>
</html>