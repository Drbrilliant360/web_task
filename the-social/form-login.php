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

// Initialize error message variable
$error_message = "";

// Login logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a session
            $_SESSION['user_id'] = $id;
            header("Location:feed.php"); // Redirect to feed.php
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>
<!-- HTML Form -->
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

  <div class="sm:flex">
    
    <div class="relative lg:w-[580px] md:w-96 w-full p-10 min-h-screen bg-white shadow-xl flex items-center pt-10 dark:bg-slate-900 z-10">

      <div class="w-full lg:max-w-sm mx-auto space-y-10" uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true">

        <!-- logo image-->
        <a href="#"> <img src="assets/images/ifm1.png" class="w-28 absolute top-10 left-10 dark:hidden" alt=""></a>
        <a href="#"> <img src="assets/images/ifm2.png" class="w-28 absolute top-10 left-10 hidden dark:!block" alt=""></a>

        <!-- logo icon optional -->
        <div class="hidden">
          <img class="w-12" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&amp;shade=600" alt="Socialite html template">
        </div>

        <!-- title -->
        <div>
          <h2 class="text-2xl font-semibold mb-1.5"> Sign in to your account </h2>
          <p class="text-sm text-gray-700 font-normal">If you haven’t signed up yet. <a href="form-register.php" class="text-blue-700">Register here!</a></p>
        </div>
 

        <!-- form -->
        <form method="post" action="form-login.php" class="space-y-7 text-sm text-black font-medium dark:text-white"  uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true">
            
          <!-- email -->
          <div>
              <label for="email" class="">Email address</label>
              <div class="mt-2.5">
                  <input id="email" name="email" type="email" autofocus=""  placeholder="Email" required="" class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5"> 
              </div>
          </div>
          <!-- password -->
          <div>
            <label for="email" class="">Password</label>
            <div class="mt-2.5">
                <input id="password" name="password" type="password" placeholder="***"  class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5">  
            </div>
          </div>

          <div class="flex items-center justify-between">

            <div class="flex items-center gap-2.5">
              <input id="rememberme" name="rememberme" type="checkbox">
              <label for="rememberme" class="font-normal">Remember me</label>
            </div>
            <a href="forgot_password.php" class="text-blue-700">Forgot password </a>
          </div>

          <!-- submit button -->
          <div>
            <button type="submit" class="button bg-primary text-white w-full">Sign in</button>
            <?php if (!empty($error_message)): ?>
            <div class="error-message" style="color: red;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
          </div>
          
        </form>


      </div>

    </div>

    <!-- image slider -->
    <div class="flex-1 relative bg-primary max-md:hidden">


      <div class="relative w-full h-full" tabindex="-1" uk-slideshow="animation: slide; autoplay: true">
    
        <ul class="uk-slideshow-items w-full h-full"> 
            <li class="w-full">
                <img src="assets/images/ifm.png"  alt="" class="w-full h-full object-cover uk-animation-kenburns uk-animation-reverse uk-transform-origin-center-left">
                <div class="absolute bottom-0 w-full uk-tr ansition-slide-bottom-small z-10">
                    <div class="max-w-xl w-full mx-auto pb-32 px-5 z-30 relative"  uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true" > 
                        <img class="w-12" src="assets/images/logo-icon.png" alt="Socialite html template">
                        <h4 class="!text-white text-2xl font-semibold mt-7"  uk-slideshow-parallax="y: 600,0,0">  welcome to ifm chat app </h4> 
                        <p class="!text-white text-lg mt-7 leading-8"  uk-slideshow-parallax="y: 800,0,0;"> Welcome to IFM Chat!

We're thrilled to have you here. Whether you're reconnecting with old friends or making new ones, IFM Chat is your space to share, laugh, and stay connected. Dive into conversations, explore new ideas, and enjoy the camaraderie that brings us all together. Thank you for joining our community – your presence makes it even more special!</p>   
                    </div> 
                </div>
                <div class="w-full h-96 bg-gradient-to-t from-black absolute bottom-0 left-0"></div>
            </li>
            <li class="w-full">
              <img src="assets/images/post/ifm.png"  alt="" class="w-full h-full object-cover uk-animation-kenburns uk-animation-reverse uk-transform-origin-center-left">
              <div class="absolute bottom-0 w-full uk-tr ansition-slide-bottom-small z-10">
                  <div class="max-w-xl w-full mx-auto pb-32 px-5 z-30 relative"  uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true" > 
                      <img class="w-12" src="assets/images/logo-icon.png" alt="Socialite html template">
                      <h4 class="!text-white text-2xl font-semibold mt-7"  uk-slideshow-parallax="y: 800,0,0">  welcome to ifm chat app </h4> 
                      <p class="!text-white text-lg mt-7 leading-8"  uk-slideshow-parallax="y: 800,0,0;"> Welcome to IFM Chat!

We're thrilled to have you here. Whether you're reconnecting with old friends or making new ones, IFM Chat is your space to share, laugh, and stay connected. Dive into conversations, explore new ideas, and enjoy the camaraderie that brings us all together. Thank you for joining our community – your presence makes it even more special!</p>   
                  </div> 
              </div>
              <div class="w-full h-96 bg-gradient-to-t from-black absolute bottom-0 left-0"></div>
          </li>
        </ul>
 
        <!-- slide nav -->
        <div class="flex justify-center">
            <ul class="inline-flex flex-wrap justify-center  absolute bottom-8 gap-1.5 uk-dotnav uk-slideshow-nav"> </ul>
        </div>
      
        
    </div>
  

    </div>
  
  </div>
  
   
    <!-- Uikit js you can use cdn  https://getuikit.com/docs/installation  or fine the latest  https://getuikit.com/docs/installation -->
    <script src="assets/js/uikit.min.js"></script>
    <script src="assets/js/script.js"></script>

    <!-- Ion icon -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

      <!-- Dark mode -->
      <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
        } else {
        document.documentElement.classList.remove('dark')
        }

        // Whenever the user explicitly chooses light mode
        localStorage.theme = 'light'

        // Whenever the user explicitly chooses dark mode
        localStorage.theme = 'dark'

        // Whenever the user explicitly chooses to respect the OS preference
        localStorage.removeItem('theme')
    </script>

</body>
</html>