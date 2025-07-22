
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Terms and Conditions</title>

</head>
<body>

  <header class="header">
   
  <div class="header-1">
      <a href="index.php" class="logo"><img src="image/LOGO.png" style="height: 70px;"></a>

        <form action="search4.php" method="GET" class="search-form">
            <input type="search" name="searchbar" placeholder="Search by title..." id="search-box">
            <button type="submit" class="fas fa-search" aria-label="Search"></button>
        </form>


        <div class="icons">
          <div id="search-btn" style="color: red;" class="fas fa-search"></div>
          <div id="login-btn" style="color: red;" class="fas fa-user">
          </div>
        </div>
    </div>

    <div class="header-2">
      <!-- <nav class="navbar">
        <a href="index.php">HOME</a>
      </nav> -->
    </div>

    

  </header>

  <div class="login-form-container">

    <div id="close-login-btn" class="fas fa-times">

    </div>
    <?php
// Include the connection file
require 'connection.php';

// Initialize variables
$username = "";
$password = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Example: Check if the username matches a record in the "admin" table
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch the user data
        $row = $result->fetch_assoc();
        $storedPasswordHash = $row['password'];

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $storedPasswordHash)) {
            // Authentication successful, redirect to index.php
            header("Location: dashboard.php");
            exit();
        } else {
            // Authentication failed, display error message
            $error = "Wrong Password Or Email. Try Once Again.";
        }
    } else {
        // No matching username found, display error message
        $error = "Wrong Password Or Email. Try Once Again.";
    }
}
?>
    <form action="" method="post">
    <?php if (isset($error)) : ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>
    <h3>Admin login</h3>
    <span>username</span>
    <input type="text" name="username" class="box" placeholder="Enter your username" id="">

    <span>password</span>
    <input type="password" name="password" class="box" placeholder="Enter your password" id="">

    <input type="submit" name="" value="login" class="btn">
    <p> <a href="index.php">Back to Homepage</a></p>
    </form>

    <?php
// Include the connection file
require 'connection.php';

// Initialize variables
$username = "";
$password = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password FROM faculty WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            // Authentication successful
            // Start the session
            session_start();

            // Store the employee_id in the session
            $_SESSION['employee_id'] = $row['id'];

            // Redirect to faculty.php
            header("Location: faculty.php");
            exit();
        } else {
            // Authentication failed, display error message
            $error = "Wrong Password Or Username. Try Once Again.";
        }
    } else {
        // Authentication failed, display error message
        $error = "Wrong Password Or Username. Try Once Again.";
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>

<form action="" method="post">
    <?php if (isset($error)) : ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>
    <h3>Staff login</h3>
    <span>username</span>
    <input type="text" name="username" class="box" placeholder="Enter your username" id="">

    <span>password</span>
    <input type="password" name="password" class="box" placeholder="Enter your password" id="">

    <input type="submit" name="" value="login" class="btn">
    <p> <a href="index.php">Back to Homepage</a></p>
    </form>
 
  </div>

  <section class="home" id="home">

    <div class="row">

      <div class="content">
        <h3>Terms and Conditions</h3>
        <p style="margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;">
        

<br>Please read these Terms and Conditions ("Terms and Conditions") carefully before using our website

<br>By accessing or using the Service, you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the Service.

<br>1. Capstone Project Data

<br>- The Capstone Archiving System is intended for the storage and retrieval of capstone project materials, including but not limited to reports, documents, code, and presentations.

<br>- Users are responsible for ensuring that their capstone project data complies with any institutional or legal requirements, including copyright and intellectual property rights.

<br>2. Submission of Manuscripts

<br>- Manuscripts may be submitted to the System for archiving by authors or other authorized users.

<br>- By submitting a manuscript, you certify and guarantee that you have the required rights and licenses to do so and that the manuscript does not infringe on the rights of any third party.

<br>3. Access to the System

<br>- In compliance with the archiving regulations, manuscripts submitted to the System will be preserved and made available to authorized users.

<br>- Unauthorized access, downloading, or sharing of manuscripts is strictly banned.

<br>4. Privacy Policy

<br>- We gather information such as your name, email address, and other pertinent contact information when you register for an account.
<br>- We utilize the data we gather to offer and improve the System, connect with you, and personalize your experience.
<br>- We take reasonable precautions to keep your information safe from unauthorized access or disclosure. However, no data transmission over the internet is totally safe, and we cannot guarantee the security of your data. 
<br>- Without the explicit written agreement of the Author, you may not duplicate, distribute, edit, or make derivative works from any material on the Service.

<br>If you have any questions or concerns about our Terms and Conditions, please contact us from the information provided.

        <br><br><br>
        <a href="index.php" style="color:blue; text-decoration:underline;"><--- Back to Homepage</a>
                
            
      </div>

  </section>

<!-- featured section start -->

<!-- Assuming you have established a database connection in your connection.php file -->

<section class="featured" id="featured">
    
</section>


<!-- featured section end -->

<section class="footer">
      <div class="box-container">
          <div class="box">
            <h3 style="color: #25ae60; font-size: 22px;">WE'RE LOCATED AT</h3>
            <a href="#" style="cursor:text;"><i class="fas fa-location-dot"></i> A. Bonifacio St., <br><br>Ormoc City, Leyte 6541 </a>
          </div>

          <div class="box">
            <h3 style="color: #25ae60; font-size: 22px;">Extras</h3>
            <a href="terms.php" target="_blank"><i class="fas fa-arrow-right"> </i> Privacy Policy</a>
            <a href="terms.php" target="_blank"><i class="fas fa-arrow-right"></i> Terms & Conditions</a>
          </div>

          <div class="box">
            <h3 style="color: #25ae60; font-size: 22px;">Contact Us</h3>
            <a href="#" style="cursor:text;"><i class="fas fa-phone" style="color:#25ae60; font-size: 15px;"></i> +63 9*** ** ****</a>
            <a href="#" target="_blank"><i class="fas fa-envelope"></i>arkheion@sample.com</a>
          </div>
      </div>

      <div class="share">
            <h3>Follow Us</h3>
            <a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a>
            <a href="https://www.google.com/" target="_blank"><i class="fab fa-google-plus"></i></a>
            <a href="https://www.twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a>
          </div>

      <div class="credit">Created by <span>Donaire & Ubay</span> | All rights reserved.</div>
</section>



<div class="loader-container">
  <img src="image/loading.gif" alt="">
  
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
<script src="script.js"></script>

</body>
</html>

