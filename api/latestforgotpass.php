<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
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

    // Sanitize and validate email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p class='text-danger'>Invalid email address.</p>";
    } else {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));

        // Check if the email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the reset_token in the users table
            $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
            $stmt->bind_param("ss", $token, $email);
            if ($stmt->execute()) {
                // Send the email
                $resetLink = "http://localhost/your_project/reset_password.php?token=" . $token;
                $subject = "Password Reset Request";
                $message = "Click the link below to reset your password:\n" . $resetLink;
                $headers = "From: noreply@yourdomain.com";

                if (mail($email, $subject, $message, $headers)) {
                    echo "<p class='text-success'>A password reset link has been sent to your email.</p>";
                } else {
                    echo "<p class='text-danger'>Failed to send the email.</p>";
                }
            } else {
                echo "<p class='text-danger'>Failed to update the reset token. Please try again later.</p>";
            }
        } else {
            echo "<p class='text-danger'>No account found with that email address.</p>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>


<!-- Password Reset 7  -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Glow Paint Run</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href=".//assets/img/favicon.png" rel="icon">

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
  <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/password-resets/password-reset-7/assets/css/password-reset-7.css">
</head>
<body>
  <section class="p-3 p-md-4 p-xl-5" style="width: 100%; height: 100vh; background: url('../path-to-your-background.jpg') no-repeat center center fixed; background-size: cover; margin: 0; padding: 0;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
          <div class="card border-dark rounded-4" style="background-color: rgba(0, 0, 0, 0.6); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);">
            <div class="card-body p-3 p-md-4 p-xl-5">
              <div class="row">
                <div class="col-12">
                  <div class="mb-5">
                    <h2 class="text-center">Password Reset</h2>
                    <h3 class="fs-6 fw-normal text-light text-center m-0">Provide the email address associated with your account to recover your password.</h3>
                  </div>
                </div>
              </div>
              <form action="latestforgotpass.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                    <label for="email" class="form-label">Email</label>
                </div>
                <div class="d-grid">
                    <button class="btn bsb-btn-xl" type="submit" style="background-color: #ad18ed; color: white; margin-top: 20px;">Reset Password</button>
                </div>
              </form>
              <div class="row">
                <div class="col-12">
                  <hr class="mt-5 mb-4 border-light-subtle">
                  <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-end">
                    <a href="usertype.html" class="link-light text-decoration-none">Login</a>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <p class="mt-5 mb-5 text-light">Or continue with</p>
                  <div class="d-flex gap-2 gap-sm-3 justify-content-center">
                    <a href="#!" class="btn btn-lg btn-outline-danger p-3 lh-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                        <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z" />
                      </svg>
                    </a>
                    <a href="#!" class="btn btn-lg btn-outline-primary p-3 lh-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                      </svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>