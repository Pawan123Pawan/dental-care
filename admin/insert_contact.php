<?php
// Include the database configuration file
include 'config.php';

// Include Composer's autoloader for PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Validate mandatory fields
    if (empty($name) || empty($email) || empty($message)) {
        echo "<h3>Please fill in all required fields!</h3>";
        exit;
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO contact (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Initialize PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'saurabh@pandaguys.in'; // Replace with your email
            $mail->Password = 'Saurav@123'; // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('saurabh@pandaguys.in', 'Your Website'); // Sender email and name
            $mail->addAddress('test705306@gmail.com', 'Saurav Singh'); // Recipient email and name

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission: ' . $subject;
            $mail->Body    = "
                <h3>Contact Form Submission</h3>
                <p><b>Name:</b> $name</p>
                <p><b>Email:</b> $email</p>
                <p><b>Phone:</b> $phone</p>
                <p><b>Subject:</b> $subject</p>
                <p><b>Message:</b><br>$message</p>
            ";

            // Send the email
            $mail->send();
            echo "<h1>Thank you for contacting us!</h1>";
            echo "<p>Your message has been successfully sent. We will get back to you shortly.</p>";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'http://yourwebsite.com'; // Redirect to homepage
                }, 3000); // Redirect after 3 seconds
            </script>";
        } catch (Exception $e) {
            echo "<h3>Message could not be sent. Mailer Error:</h3> <p>{$mail->ErrorInfo}</p>";
        }
    } else {
        echo "<h3>There was an error submitting your form. Please try again later.</h3>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "<h3>Invalid request method. Please submit the form correctly.</h3>";
}
