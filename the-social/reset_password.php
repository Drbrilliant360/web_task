<?php
session_start();

// Database connection
require 'db.php';

$error_message = "";
$success_message = "";

// Check if the user is trying to reset the password
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php"); // Redirect if no email is set
    exit();
}

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        // Update password in the database
        $sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Congratulations! Your password has been reset.";
            unset($_SESSION['reset_email']); // Clear the session variable
        } else {
            $error_message = "Error updating password: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="forgotcss.css">
</head>
<body>
    <div class="forgot-container">
        <h1>Reset Password</h1>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php else: ?>
            <form action="" method="POST">
                <div class="input-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="forgot-btn">Reset Password</button>
            </form>
        <?php endif; ?>
        <div class="links">
            <a href="form-login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>