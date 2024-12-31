<?php
session_start();

// Database connection
require 'db.php'; // Ensure this file contains your database connection code

$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Email exists, redirect to reset password page
        $_SESSION['reset_email'] = $email; // Store email in session
        header("Location:reset_password.php");
        exit();
    } else {
        $error_message = "No account found with that email.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgotcss.css">
</head>
<body>
    <div class="forgot-container">
        <h1>Forgot Password</h1>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="forgot-btn">Next</button>
        </form>
        <div class="links">
            <a href="form-login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>