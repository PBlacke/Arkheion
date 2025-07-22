<?php
// search.php

if (isset($_GET['searchbar'])) {
    $searchTerm = $_GET['searchbar'];

    // Include your database connection file
    require 'connection.php';

    // Perform a search query based on the title and status 'Published'
    $sql = "SELECT * FROM files WHERE LOWER(title) LIKE '%$searchTerm%' AND status = 'Published'";
    $result = $conn->query($sql);

    // Close the database connection
    $conn->close();
} else {
    // Handle the case when 'searchbar' is not set
    echo "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="shortcut icon" type="x-icon" href="LOGO.png">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Arkheion Archiving System</title>

</head>
<body>

  <header class="header">
   
  <div class="header-1">
      <a href="index.php" class="logo"> <i style="color: red;" class="fa fa-bugs"></i> Arkheion</a>

        <form action="search4.php" method="GET" class="search-form">
            <input type="search" name="searchbar" placeholder="Search by title..." id="search-box">
            <button type="submit" class="fas fa-search" aria-label="Search"></button>
        </form>


        <div class="icons">
          <div id="search-btn" style="color: red;" class="fas fa-search"></div>
          <div id="login-btn" style="color: red;" ><a href='adminlogin.php'>Admin login</a></div>
        </div>
        <div class="icons">
          <div id="login-btn" style="color: red;" ><a href='facultylogin.php'>Staff Login</a></div>
        </div>
    </div>


    <div class="header-2">
      <!-- <nav class="navbar">
        <a href="index.php">HOME</a>
        <a href="facultylogin.php">FACULTY LOGIN</a>
        <a href="adminlogin.php">ADMIN LOGIN</a>
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

<style>
    .swiper-container {
        width: 100%;
        overflow: auto;
    }

    .swiper-wrapper {
        display: flex;
    }

    .swiper-slide {
        flex-shrink: 0;
        box-sizing: border-box;
        padding: 10px;
        width: 25%; /* Set the width to 25% for 4 columns */
    }

    .swiper-slide img {
        width: 200px;
        height: 210px;
    }
    .image:hover{
        cursor: pointer;
    }
</style>

<?php
if (isset($result) && $result->num_rows > 0) {
    $itemsPerRow = 4; // Number of items per row

    $rowCount = 0;
    echo '<div class="swiper-container">';
    while ($row = $result->fetch_assoc()) {
        if ($rowCount % $itemsPerRow == 0) {
            if ($rowCount > 0) {
                echo '</div>'; // Close the previous row
            }
            echo '<div class="swiper-wrapper">';
        }

        echo '<div class="swiper-slide box">';
        echo '<div class="icons"></div>';
        echo '<div class="image">';
        // Wrap the image in an anchor tag with the link to the new page
        echo '<a href="description.php?id=' . $row['id'] . '">';
        echo '<img src="' . $row['image'] . '" alt="' . $row['title'] . '">';
        echo '</a>';
        echo '</div>';
        echo '<div class="content">';
        echo '<h3 style="max-width: 200px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">' . $row['title'] . '</h3>';
        echo '<a href="view_file.php?id=' . $row['id'] . '" class="btn" target="_blank" style="font-size: 8px;">View</a>';
        echo '<a href="download.php?id=' . $row['id'] . '" class="btn" style="font-size: 8px; background:#25ae60;">Download</a>';
        echo '</div>';
        echo '</div>';

        $rowCount++;
    }

    echo '</div>'; // Close the last row
    echo '</div>'; // Close the swiper-container

} else {
    echo '<p>No results found.</p>';
}
?>


<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
<script src="script.js"></script>
</body>
</html>