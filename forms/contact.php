<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Gmail credentials
    $gmail_user = "your-gmail-username@gmail.com"; // your Gmail email
    $gmail_password = "your-gmail-password"; // your Gmail password

    // Email details
    $to = "maeunmyunramen@gmail.com"; // admin email
    $headers = "From: $email" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();
    $emailBody = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    // Send email using PHP's mail function
    if (mail($to, $subject, $emailBody, $headers)) {
        echo "Your message has been sent!";
    } else {
        echo "Error: Unable to send email.";
    }
}
?>
