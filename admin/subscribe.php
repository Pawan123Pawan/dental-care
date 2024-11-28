<?php
// Include the database configuration file
include 'config.php';

// Include Composer's autoloader for PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data and sanitize input
    $name = htmlspecialchars($_POST['form_name']);
    $email = htmlspecialchars($_POST['form_email']);
    $phone = htmlspecialchars($_POST['form_phn']);

    // Check for empty fields
    if (empty($name) || empty($email) || empty($phone)) {
        echo "All fields are required!";
        exit;
    }

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit;
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (name, email, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $phone);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        // Send the email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP(); // Use SMTP
            $mail->Host = 'smtp.hostinger.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'saurabh@pandaguys.in'; // Your email address
            $mail->Password = 'Saurav@123'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('saurabh@pandaguys.in', 'Your Name'); // Your email
            $mail->addAddress('test705306@gmail.com', 'Saurav Singh'); // Recipient's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Subscriber: ' . $name;
            $mail->Body = "<b>Name:</b> $name<br>
                           <b>Email:</b> $email<br>
                           <b>Phone:</b> $phone";

            // Send email
            $mail->send();
            echo "<h1>Thank you for subscribing!</h1>";
            echo "<script>
                setTimeout(function(){
                    window.location.href = 'http://localhost/doctor/mail-success.php';
                }, 3000);
            </script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
