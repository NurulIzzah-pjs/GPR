<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

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

    // Check if the token is valid
    $stmt = $conn->prepare("SELECT email FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $email = $result->fetch_assoc()['email'];
        // Valid token, show the reset password form
        $validToken = true;
    } else {
        // Invalid token
        $validToken = false;
    }

    $stmt->close();
    $conn->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['password'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "gpr");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the password and clear the reset token
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $hashedPassword, $token);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $message = "Your password has been reset successfully.";
    } else {
        $message = "Invalid or expired token.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <?php if (isset($validToken) && $validToken): ?>
        <h3>Reset Your Password</h3>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Password</button>
        </form>
    <?php elseif (isset($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <a href="latestforgotpass.php" class="btn btn-link">Back to Forgot Password</a>
    <?php else: ?>
        <div class="alert alert-danger">Invalid or expired token.</div>
    <?php endif; ?>
</div>
</body>
</html>
