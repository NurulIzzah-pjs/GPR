<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'], $_POST['new_password'])) {
    // Database credentials
    $servername = "localhost";
    $username = "root";
    $password = ""; // Default password for XAMPP
    $dbname = "gpr";

    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize and validate input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $new_password = $_POST['new_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p class='text-danger'>Invalid email address.</p>";
    } elseif (empty($new_password)) {
        echo "<p class='text-danger'>Password cannot be empty.</p>";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Check if the email exists
        $stmt = $conn->prepare("SELECT ParticipantID FROM participant WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the password in the participant table
            $stmt = $conn->prepare("UPDATE participant SET Password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            if ($stmt->execute()) {
                echo "<p class='text-success'>Your password has been successfully reset.</p>";
            } else {
                echo "<p class='text-danger'>Failed to update the password. Please try again later.</p>";
            }
        } else {
            echo "<p class='text-danger'>No account found with that email address.</p>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!-- Reset Password HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Reset Password</title>
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
</head>
<body>
  <section class="p-3 p-md-4 p-xl-5" style="width: 100%; height: 100vh; background-color: #f8f9fa;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <h2 class="text-center">Reset Password</h2>
              <form action="reset_password.php" method="POST">
                <div class="form-group mb-3">
                  <label for="email">Email Address</label>
                  <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group mb-3">
                  <label for="new_password">New Password</label>
                  <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter new password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
