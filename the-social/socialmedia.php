<?php
$servername = "localhost"; // Change if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "socialite";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save1'])) {
        // Clear previous entries
        if ($conn->query("DELETE FROM social_links") === FALSE) {
            $error = "Error clearing previous links: " . $conn->error;
        }

        // Save new links
        $platforms = ['Facebook', 'Instagram', 'Twitter', 'YouTube', 'GitHub'];
        $success = true; // Flag for tracking success

        foreach ($platforms as $platform) {
            $url = isset($_POST[strtolower($platform)]) ? $_POST[strtolower($platform)] : '';
            if (!empty($url)) {
                $stmt = $conn->prepare("INSERT INTO social_links (platform, url) VALUES (?, ?)");
                $stmt->bind_param("ss", $platform, $url);
                if (!$stmt->execute()) {
                    $success = false; // Set success flag to false if any insert fails
                    $error = "Error saving link for $platform: " . $stmt->error;
                    $stmt->close(); // Close the statement after execution
                }
            }
        }

        // Check if all inserts were successful
        if ($success) {
            $message = "Congratulations! Your links have been saved successfully.";
        }
    }

    // Handle Login button
    if (isset($_POST['login'])) {
        header("Location: login.php"); // Redirect to login page
        exit();
    }
}

// Fetch existing links
$socialLinks = [];
$query = "SELECT * FROM social_links";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $socialLinks[$row['platform']] = $row['url'];
}
?>

