<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'], $_GET['email'])) {
    // Database credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gpr";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $new_password = $_POST['new_password'];
    $email = $_GET['email']; // Get email from the URL

    if (empty($new_password)) {
        echo "<p class='text-danger'>Password cannot be empty.</p>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the participant table
        $stmt = $conn->prepare("UPDATE participant SET Password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        if ($stmt->execute()) {
            echo "<p class='text-success'>Your password has been successfully reset.</p>";
        } else {
            echo "<p class='text-danger'>Failed to update the password. Please try again later.</p>";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!-- Reset Password Form -->
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
  <section class="p-3 p-md-4 p-xl-5" style="width: 100%; height: 100vh; background: url('../path-to-your-background.jpg') no-repeat center center fixed; background-size: cover; margin: 0; padding: 0;">
    <div class="container">
      <div class="row justify-content-center">
      <div class="col-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
      <div class="card border-dark rounded-4" style="background-color: rgba(0, 0, 0, 0.6); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);">
      <div class="card-body p-3 p-md-4 p-xl-5 text-center">
              <h2 class="text-center">Reset Password</h2>
              <form action="reset_password.php?email=<?php echo urlencode($_GET['email']); ?>" method="POST">
                <div class="form-group mb-3">
                  <label for="new_password" class="fs-6 fw-normal text-light text-center m-0">New Password</label>
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
