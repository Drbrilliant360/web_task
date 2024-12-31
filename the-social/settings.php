<?php
session_start();
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

// Initialize error and success message variables
$error_message = "";
$success_message = "";

// Fetch existing user data (assuming user is logged in)
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit();
}

$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user data was found
if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission for user data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    // Update user data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $gender = $_POST['gender'];
    $relationship = $_POST['relationship'];

    // Update the user data in the database
    $updateQuery = "UPDATE users SET username = ?, email = ?, bio = ?, gender = ?, relationship = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssssi", $username, $email, $bio, $gender, $relationship, $userId);

    if ($updateStmt->execute()) {
        $message = "Congratulations! Your data has been updated successfully.";
        echo "<script>alert('$message'); window.location.href = 'form-login.php';</script>";
        exit();
    } else {
        $message = "Error: " . $updateStmt->error;
    }
}

// Handle Cancel button for user data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel'])) {
    header("Location: form-login.php");
    exit();
}

// Handle form submission for social links
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save1'])) {
    // Clear previous entries
    if ($conn->query("DELETE FROM social_links") === FALSE) {
        $error_message = "Error clearing previous links: " . $conn->error;
    }

    // Save new links
    $platforms = ['Facebook', 'Instagram', 'Twitter', 'YouTube', 'GitHub'];
    $success = true; // Flag for tracking success

    foreach ($platforms as $platform) {
        $url = isset($_POST[$platform]) ? $_POST[$platform] : '';
        if (!empty($url)) {
            $stmt = $conn->prepare("INSERT INTO social_links (platform, url) VALUES (?, ?)");
            $stmt->bind_param("ss", $platform, $url);
            if (!$stmt->execute()) {
                $success = false; // Set success flag to false if any insert fails
                $error_message = "Error saving link for $platform: " . $stmt->error;
            }
            $stmt->close(); // Close the statement after execution
        }
    }

    // Check if all inserts were successful
    if ($success) {
        $message = "Congratulations! Your links have been saved successfully.";
        echo "<script>alert('$message'); window.location.href = 'form-login.php';</script>";
        exit();
    } else {
        echo "<script>alert('$error_message');</script>";
    }
}

// Handle Cancel button for social links
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel1'])) {
    header("Location: form-login.php"); // Redirect to login page
    exit();
}
// Fetch existing links
$socialLinks = [];
$query = "SELECT * FROM social_links";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $socialLinks[$row['platform']] = $row['url'];
}

// Handle form submission for password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save2'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';
    $two_factor = isset($_POST['two_factor']) ? (int)$_POST['two_factor'] : 0; // 1 for enabled, 0 for disabled

    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $repeat_password && !empty($new_password)) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $updatePasswordQuery = "UPDATE users SET password = ?, two_factor_enabled = ? WHERE id = ?";
            $updatePasswordStmt = $conn->prepare($updatePasswordQuery);
            if ($updatePasswordStmt) {
                $updatePasswordStmt->bind_param("ssi", $hashed_password, $two_factor, $userId);
                if ($updatePasswordStmt->execute()) {
                    $success_message = "Your password has been updated successfully.";
                } else {
                    $error_message = "Error updating password: " . $updatePasswordStmt->error;
                }
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
            }
        } else {
            $error_message = "New passwords do not match or are empty.";
        }
    } else {
        $error_message = "Current password is incorrect.";
    }
    // Display messages
    if (!empty($error_message)) {
        echo "<script>alert('$error_message');</script>";
    }
    if (!empty($success_message)) {
        echo "<script>alert('$success_message');</script>";
    }
}

// Handle Cancel button for password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_password'])) {
    header("Location: form-login.php"); // Redirect to login page
    exit();
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link href="assets/images/favicon.png" rel="icon" type="image/png">

    <!-- title and description-->
    <title>Socialite</title>
    <meta name="description" content="Socialite - Social sharing network HTML Template">
   
    <!-- css files -->
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <link rel="stylesheet" href="assets/css/style.css">  
    
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
 
</head>
<body>
 
    <div id="wrapper">

        <!-- header -->
        <header class="z-[100] h-[--m-top] fixed top-0 left-0 w-full flex items-center bg-white/80 sky-50 backdrop-blur-xl border-b border-slate-200 dark:bg-dark2 dark:border-slate-800">

            <div class="flex items-center w-full xl:px-6 px-2 max-lg:gap-10">

                <div class="2xl:w-[--w-side] lg:w-[--w-side-sm]">

                    <!-- left -->
                    <div class="flex items-center gap-1"> 

                        <!-- icon menu -->
                        <button uk-toggle="target: #site__sidebar ; cls :!-translate-x-0"
                                class="flex items-center justify-center w-8 h-8 text-xl rounded-full hover:bg-gray-100 xl:hidden dark:hover:bg-slate-600 group"> 
                                <ion-icon name="menu-outline" class="text-2xl group-aria-expanded:hidden"></ion-icon>
                                <ion-icon name="close-outline" class="hidden text-2xl group-aria-expanded:block"></ion-icon>
                        </button>
                        <div id="logo">
                            <a href="feed.php"> 
                                <img src="assets/images/logo.png" alt="" class="w-28 md:block hidden dark:!hidden">
                                <img src="assets/images/logo-light.png" alt="" class="dark:md:block hidden">
                                <img src="assets/images/logo-mobile.png" class="hidden max-md:block w-20 dark:!hidden" alt="">
                                <img src="assets/images/logo-mobile-light.png" class="hidden dark:max-md:block w-20" alt="">
                            </a>
                        </div>
                         
                    </div>

                </div>
                <div class="flex-1 relative">

                    <div class="max-w-[1220px] mx-auto flex items-center">

                        <!-- header icons -->
                        <div class="flex items-center sm:gap-4 gap-2 absolute right-5 top-1/2 -translate-y-1/2 text-black">
        
                            <!-- profile -->
                            <div  class="rounded-full relative bg-secondery cursor-pointer shrink-0">
                                <img src="assets/images/avatars/avatar-2.jpg" alt="" class="sm:w-9 sm:h-9 w-7 h-7 rounded-full shadow shrink-0"> 
                            </div>
                            <div  class="hidden bg-white rounded-lg drop-shadow-xl dark:bg-slate-700 w-64 border2"
                                uk-drop="offset:6;pos: bottom-right;animate-out: true; animation: uk-animation-scale-up uk-transform-origin-top-right ">
                                <a href="timeline.php">
                                    <div class="p-4 py-5 flex items-center gap-4">
                                        <img src="assets/images/avatars/avatar-2.jpg" alt="" class="w-10 h-10 rounded-full shadow">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-black">John Doe</h4>
                                            <div class="text-sm mt-1 text-blue-600 font-light dark:text-white/70">@johndoe</div>
                                        </div>
                                    </div>
                                </a>
                                <hr class="dark:border-gray-600/60">
                                <nav class="p-2 text-sm text-black font-normal dark:text-white">
                                    <a href="setting.php">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            My Account
                                        </div>
                                    </a>
                                    <hr class="-mx-2 my-2 dark:border-gray-600/60">
                                    <a href="form-login.php">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10"> 
                                            <svg class="w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Log Out 
                                        </div>
                                    </a>
                                </nav>
                            </div> 

                            <div class="flex items-center gap-2 hidden">
                                <img src="assets/images/avatars/avatar-2.jpg" alt="" class="w-9 h-9 rounded-full shadow">
        
                                <div class="w-20 font-semibold text-gray-600"> Hamse </div>
        
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg> 
        
                            </div> 
        
                        </div>

                    </div> 

                </div>

            </div>

        </header>
    
        <!-- sidebar -->
        <div id="site__sidebar" class="fixed top-0 left-0 z-[99] pt-[--m-top] overflow-hidden transition-transform xl:duration-500 max-xl:w-full max-xl:-translate-x-full">

            <!-- sidebar inner -->
            <div class="p-2 max-xl:bg-white shadow-sm 2xl:w-72 sm:w-64 w-[80%] h-[calc(100vh-64px)] relative z-30 max-lg:border-r dark:max-xl:!bg-slate-700 dark:border-slate-700">
        
                <div class="pr-4" data-simplebar>

                    <nav id="side">
                    
                        <ul>
                            <li class="active">
                                <a href="feed.php">
                                    <img src="assets/images/icons/home.png" alt="feeds" class="w-6">
                                    <span> Feed </span> 
                                </a>
                            </li>
                    </ul>
                    </nav>

                </div>

            </div>

            <!-- sidebar overly -->
            <div id="site__sidebar__overly" 
                class="absolute top-0 left-0 z-20 w-screen h-screen xl:hidden backdrop-blur-sm"
                uk-toggle="target: #site__sidebar ; cls :!-translate-x-0"> 
            </div>
        </div>

        <!-- main contents -->
        <main id="site__main" class="2xl:ml-[--w-side]  xl:ml-[--w-side-sm] p-2.5 h-[calc(100vh-var(--m-top))] mt-[--m-top]">

            <div class="max-w-3xl mx-auto">


                <div class="box relative rounded-lg shadow-md">
    
                    <div class="flex md:gap-8 gap-4 items-center md:p-8 p-6 md:pb-4">

                        <div class="relative md:w-20 md:h-20 w-12 h-12 shrink-0"> 
    
                            <label for="file" class="cursor-pointer">
                                <img id="img" src="assets/images/avatars/avatar-3.jpg" class="object-cover w-full h-full rounded-full" alt=""/>
                                <input type="file" id="file" class="hidden" />
                            </label>
      
                            <label for="file" class="md:p-1 p-0.5 rounded-full bg-slate-600 md:border-4 border-white absolute -bottom-2 -right-2 cursor-pointer dark:border-slate-700">
    
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="md:w-4 md:h-4 w-3 h-3 fill-white">
                                    <path d="M12 9a3.75 3.75 0 100 7.5A3.75 3.75 0 0012 9z" />
                                    <path fill-rule="evenodd" d="M9.344 3.071a49.52 49.52 0 015.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 01-3 3h-15a3 3 0 01-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 001.11-.71l.822-1.315a2.942 2.942 0 012.332-1.39zM6.75 12.75a5.25 5.25 0 1110.5 0 5.25 5.25 0 01-10.5 0zm12-1.5a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                                </svg>
    
                                <input id="file" type="file" class="hidden" />
            
                            </label>
    
                        </div>
    
                        <div class="flex-1">
                            <h3 class="md:text-xl text-base font-semibold text-black dark:text-white"> John Doe </h3>
                            <p class="text-sm text-blue-600 mt-1 font-normal">@johndoe</p>
                        </div>
                    </div>
    
                    <!-- nav tabs -->
                    <div class="relative border-b" tabindex="-1" uk-slider="finite: true">
    
                        <nav class="uk-slider-container overflow-hidden nav__underline px-6 p-0 border-transparent -mb-px">
            
                            <ul class="uk-slider-items w-[calc(100%+10px)] !overflow-hidden" 
                                uk-switcher="connect: #setting_tab ; animation: uk-animation-slide-right-medium, uk-animation-slide-left-medium"> 
                                
                                <li class="w-auto pr-2.5"> <a href="#"> Description </a> </li>
                                <li class="w-auto pr-2.5"> <a href="#"> Social</a> </li>
                                <li class="w-auto pr-2.5"> <a href="#"> Passwords</a> </li> 
                                
                            </ul>
                        
                        </nav>
                                
                        <a class="absolute -translate-y-1/2 top-1/2 left-0 flex items-center w-20 h-full p-2 py-1 justify-start bg-gradient-to-r from-white via-white dark:from-slate-800 dark:via-slate-800" href="#" uk-slider-item="previous"> <ion-icon name="chevron-back" class="text-2xl ml-1"></ion-icon> </a>
                        <a class="absolute right-0 -translate-y-1/2 top-1/2 flex items-center w-20 h-full p-2 py-1 justify-end bg-gradient-to-l from-white via-white dark:from-slate-800 dark:via-slate-800" href="#" uk-slider-item="next">  <ion-icon name="chevron-forward" class="text-2xl mr-1"></ion-icon> </a>
                
                    </div> 
    
    
                    <div id="setting_tab" class="uk-switcher md:py-12 md:px-20 p-6 overflow-hidden text-black text-sm"> 
                        
                        <!-- tab user basic info -->
                        <div>
    
                            <div>
                                
                                <div class="space-y-6">
<form method="post" action="settings.php" name="description">
                                    <div class="md:flex items-center gap-10">
                                        <label class="md:w-32 text-right"> Username </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <input type="text" id="username" name="username" placeholder="Monroe" class="lg:w-1/2 w-full" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="md:flex items-center gap-10">
                                        <label class="md:w-32 text-right"> Email </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <input type="text" id="email" name="email" placeholder="info@mydomain.com" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full">
                                        </div>
                                    </div> 
            
                                    <div class="md:flex items-start gap-10">
                                        <label class="md:w-32 text-right"> Bio </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <textarea class="w-full" id="bio" name="bio" rows="5" placeholder="Inter your Bio" required><?php echo htmlspecialchars($user['bio']); ?> </textarea>
                                        </div>
                                    </div> 

                                    <div class="md:flex items-center gap-10">
                                        <label class="md:w-32 text-right"> Gender </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <select class="!border-0 !rounded-md lg:w-1/2 w-full" name="gender">
                                                <option value="Male"<?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                                <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="md:flex items-center gap-10">
                                        <label class="md:w-32 text-right"> Relationship </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <select class="!border-0 !rounded-md lg:w-1/2 w-full" name="relationship">
                                                <option value="0">None</option>
                                                <option value="1" <?php if ($user['relationship'] == '1') echo 'selected'; ?>> Single</option>
                                                <option value="2" <?php if ($user['relationship'] == '2') echo 'selected'; ?>> In a relationship</option>
                                                <option value="3"  <?php if ($user['relationship'] == '3') echo 'selected'; ?>> Married</option>
                                                <option value="4" <?php if ($user['relationship'] == '4') echo 'selected'; ?>> Engaged</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="md:flex items-start gap-10 " hidden>
                                        <label class="md:w-32 text-right"> Avatar </label>
                                        <div class="flex-1 flex items-center gap-5 max-md:mt-4">
                                            <img src="assets/images/avatars/avatar-3.jpg" alt="" class="w-10 h-10 rounded-full">
                                            <button type="submit" class="px-4 py-1 rounded-full bg-slate-100/60 border dark:bg-slate-700 dark:border-slate-600 dark:text-white"> Change</button>
                                        </div>
                                    </div>
    
                                </div>
      
                                <div class="flex items-center gap-4 mt-16 lg:pl-[10.5rem]">
                                    <button type="submit" name="cancel" class="button lg:px-6 bg-secondery max-md:flex-1"> Cancel</button>
                                    <button type="submit" name="save" class="button lg:px-10 bg-primary text-white max-md:flex-1"> Save <span class="ripple-overlay"></span></button>
                                </div>
</form>
                            </div> 
    
                        </div>
    
                        <!-- tab socialinks -->   
                        <div>
    
                            <div class="max-w-md mx-auto">
    
                                <div class="font-normal text-gray-400">
                                
                                    <div>
                                        <h4 class="text-xl font-medium text-black dark:text-white"> Social Links </h4>
                                        <p class="mt-3 font-normal text-gray-600 dark:text-white">We may still send you important notifications about your account and content outside of you preferred notivications settings</p>
                                    </div>
    
                                    <div class="space-y-6 mt-8">
<form action="settings.php" method="POST" name="social">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-blue-50 rounded-full p-2 flex ">
                                                <ion-icon name="logo-facebook" class="text-2xl text-blue-600"></ion-icon> 
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" name="Facebook" id="facebook" class="w-full" placeholder="http://www.facebook.com/myname" value="<?php echo htmlspecialchars($socialLinks['Facebook'] ?? ''); ?>" required  >
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="bg-pink-50 rounded-full p-2 flex ">
                                                <ion-icon name="logo-instagram" class="text-2xl text-pink-600"></ion-icon> 
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" name="Instagram" id="Instagram" class="w-full" placeholder="http://www.instagram.com/myname" value="<?php echo htmlspecialchars($socialLinks['Instagram'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="bg-sky-50 rounded-full p-2 flex ">
                                                <ion-icon name="logo-twitter" class="text-2xl text-sky-600"></ion-icon> 
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" name="Twitter" id="Twitter" class="w-full" placeholder="http://www.twitter.com/myname" value="<?php echo htmlspecialchars($socialLinks['Twitter'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="bg-red-50 rounded-full p-2 flex ">
                                                <ion-icon name="logo-youtube" class="text-2xl text-red-600"></ion-icon> 
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" name="Youtube" id="Youtube" class="w-full" placeholder="http://www.youtube.com/myname" value="<?php echo htmlspecialchars($socialLinks['Youtube'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="bg-slate-50 rounded-full p-2 flex ">
                                                <ion-icon name="logo-github" class="text-2xl text-black"></ion-icon> 
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" name="Github" id="Github" class="w-full" placeholder="http://www.github.com/myname" value="<?php echo htmlspecialchars($socialLinks['Github'] ?? ''); ?>" required>
                                            </div>
                                        </div>

    
                                    </div> 
                                   
                                </div> 
                                
                                <div class="flex items-center justify-center gap-4 mt-16">
                                    <button type="submit" name="cancel1" class="button lg:px-6 bg-secondery max-md:flex-1"> Cancel</button>
                                    <button type="submit" name="save1"  class="button lg:px-10 bg-primary text-white max-md:flex-1"> Save</button>
                                </div>
</Form>

                            
    
                            </div>
    
                        </div>
                        
                        <!-- tab password-->
                        <div>
    
                            <div>
                                
                                <div class="space-y-6 max-w-lg mx-auto">
 <form action="settings.php" method="POST" >
                                    <div class="md:flex items-center gap-16 justify-between max-md:space-y-3">
                                        <label class="md:w-40 text-right"> Current Password </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <input type="password" name="current_password" placeholder="******" class="w-full">
                                        </div>
                                    </div>
                                  
                                    <div class="md:flex items-center gap-16 justify-between max-md:space-y-3">
                                        <label class="md:w-40 text-right"> New password </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <input type="password" name="new_password" placeholder="******" class="w-full">
                                        </div>
                                    </div>
    
                                    <div class="md:flex items-center gap-16 justify-between max-md:space-y-3">
                                        <label class="md:w-40 text-right"> Repeat password </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <input type="password" name="repeat_password" placeholder="******" class="w-full">
                                        </div>
                                    </div>
    
                                    <hr class="border-gray-100 dark:border-gray-700">
    
                                    <div class="md:flex items-center gap-16 justify-between">
                                        <label class="md:w-40 text-right"> Two-factor authentication </label>
                                        <div class="flex-1 max-md:mt-4">
                                            <select  id="two_factor" name="two_factor" class="w-full !border-0 !rounded-md">
                                            <option value="1" <?php echo (isset($user['two_factor_enabled']) && $user['two_factor_enabled'] == 1) ? 'selected' : ''; ?>>Enable</option>
                                            <option value="0" <?php echo (isset($user['two_factor_enabled']) && $user['two_factor_enabled'] == 0) ? 'selected' : ''; ?>>Disable</option>
                                            
                                            </select>
                                        </div>
                                    </div>

    
                                </div>
                                
                                <div class="flex items-center justify-center gap-4 mt-16">
                                    <button type="submit" name="cancel_password" class="button lg:px-6 bg-secondery max-md:flex-1"> Cancel</button>
                                    <button type="submit" name="save2" class="button lg:px-10 bg-primary text-white max-md:flex-1"> Save</button>
 </form>

                                </div>
     
                            </div>
                            
                        </div>
                    </div>
                    
                
                </div>
    
                
            </div>
            
        </main>

    </div>



    <!-- Javascript  -->
    <script src="assets/js/uikit.min.js"></script>
    <script src="assets/js/simplebar.js"></script>
    <script src="assets/js/script.js"></script>
 
 
    <!-- Ion icon -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>