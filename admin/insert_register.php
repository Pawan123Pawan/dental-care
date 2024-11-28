<?php
// Include the database configuration file
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $news = isset($_POST['news']) ? 1 : 0;

    // Validate passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO register (first_name, last_name, phone, email, password, news) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $firstName, $lastName, $phone, $email, $hashedPassword, $news);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<h1>Thanks for registering!</h1>";
        echo "<p>You will be redirected shortly...</p>";
        echo "<script>
            setTimeout(function(){
                window.location.href = 'http://localhost/doctor/mail-success.html';
            }, 3000); // Redirect after 3 seconds
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
