<?php
// Include your database connection file
require 'connection.php';

// Function to validate and sanitize user inputs
function sanitizeInput($conn, $input) {
    return isset($input) ? mysqli_real_escape_string($conn, strtolower($input)) : '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form values with validation/sanitization
    $department = sanitizeInput($conn, $_POST['department']);
    $curriculum = sanitizeInput($conn, $_POST['curriculum']);
    $year = sanitizeInput($conn, $_POST['year']);

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM files WHERE LOWER(department) = LOWER(?) AND LOWER(curriculum) = LOWER(?) AND year LIKE ? AND status = 'Published'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $department, $curriculum, $year);

    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Check for errors in the query execution
        if (!$result) {
            // Log the error instead of displaying it
            error_log("Error: " . $conn->error);
            exit("An error occurred. Please try again later.");
        }

        // Rest of your code
        $stmt->close();
    } else {
        // Log the error instead of displaying it
        error_log("Execute failed: " . $stmt->error);
        exit("An error occurred. Please try again later.");
    }
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
// Check if the form is submitted
    require 'connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input if they are set
    // Retrieve user input if they are set
$username = isset($_POST["username"]) ? strtolower($_POST["username"]) : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';

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
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input if they are set
    $username = isset($_POST["username"]) ? $_POST["username"] : '';
    $password = isset($_POST["password"]) ? $_POST["password"] : '';

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
require 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form values with validation/sanitization
    $department = isset($_POST['department']) ? mysqli_real_escape_string($conn, strtolower($_POST['department'])) : '';
    $curriculum = isset($_POST['curriculum']) ? mysqli_real_escape_string($conn, strtolower($_POST['curriculum'])) : '';
    $year = isset($_POST['year']) ? mysqli_real_escape_string($conn, $_POST['year']) : '';

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM files WHERE LOWER(department) = LOWER(?) AND LOWER(curriculum) = LOWER(?) AND year LIKE ? AND status = 'Published'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $department, $curriculum, $year);

    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Check for errors in the query execution
        if (!$result) {
            // Log the error instead of displaying it
            error_log("Error: " . $conn->error);
            exit("An error occurred. Please try again later.");
        }

        // Rest of your code
        $stmt->close();
    } else {
        // Log the error instead of displaying it
        error_log("Execute failed: " . $stmt->error);
        exit("An error occurred. Please try again later.");
    }
}
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
echo '<a href="description2.php?id=' . $row['id'] . '">';
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

        echo '</div>'; // Close the swiper-wrapper
        echo '</div>'; // Close the swiper-container
    } else {
        // No results found
        echo '<p>No results found.</p>';
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="script.js"></script>
</section>
<style>

        div {
            text-align: center;
        }

        .aaa {
            display: inline-block;
            padding: 10px 20px;
            background-color: #25ae60;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .aaa:hover {
            background-color: #1a8741;
        }
    </style>
<div>
        <a class="aaa" href="archive.php">Back</a>
    </div>
</body>
</html>