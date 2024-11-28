<?php
// Include the database configuration file
include 'config.php';

// Include Composer's autoloader for PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data and sanitize input
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $date = htmlspecialchars($_POST['date']);
    $time = htmlspecialchars($_POST['time']);
    $service = htmlspecialchars($_POST['service']);
    $message = htmlspecialchars($_POST['form_message']);

    // Check for empty fields
    if (empty($name) || empty($email) || empty($date) || empty($time) || empty($service) || empty($message)) {
        echo "All fields are required!";
        exit;
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO appointment_form (name, email, date, time, service, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $date, $time, $service, $message);

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
            $mail->Subject = 'New Appointment Request: ' . $name;
            $mail->Body = "<b>Name:</b> $name<br>
                           <b>Email:</b> $email<br>
                           <b>Date:</b> $date<br>
                           <b>Time:</b> $time<br>
                           <b>Service:</b> $service<br>
                           <b>Message:</b> $message";

            // Send email
            $mail->send();
            echo "<h1>Appointment successfully booked!</h1>";
            echo "<script>
                setTimeout(function(){
                    window.location.href = 'http://localhost/doctor/mail-success.php' 
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
