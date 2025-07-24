<?php
// edit.php

// Include the connection file
require 'config/connection.php';

// Retrieve the ID from the URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Check if the ID is provided
if ($id) {
    // Query to fetch data based on the provided ID
    $query = "SELECT * FROM files WHERE id = $id";
    $result = $conn->query($query);

    // Check if the query was successful
    if ($result && $result->num_rows > 0) {
        // Fetch the data from the result set
        $row = $result->fetch_assoc();

        // Now you can use $row['title'], $row['uploader'], etc., to display the data in your form
        $title = $row['title'];
        $description = $row['description'];
        $uploader = $row['uploader'];
        $email = $row['email'];
        $year = $row['year'];
        $department = $row['department'];
        $curriculum = $row['curriculum'];
        $status = $row['status'];

        // Close the result set
        $result->close();
    } else {
        // Handle the case where the ID doesn't match any record
        echo "Record not found";
    }
} else {
    // Handle the case where no ID is provided
    echo "Invalid ID";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="shortcut icon" type="x-icon" href="LOGO.png">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Arkheion Archiving System</title>

</head>

<body>

    <header class="header">

        <div class="header-1">
            <a href="index.php" class="logo"> <i style="color: red;" class="fa fa-book fa-fw"></i> Arkheion</a>

            <div class="icons">
                <div id="search-btn" style="color: red;" class="fas fa-search"></div>
                <div id="login-btn" style="color: red;"><a href='adminlogin.php'>Admin login</a></div>
            </div>
            <div class="icons">
                <div id="login-btn" style="color: red;"><a href='facultylogin.php'>Staff Login</a></div>
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
        require 'config/connection.php';

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
        require 'config/connection.php';

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
                <?php
                // Include the connection file
                require 'config/connection.php';
                // Check if the ID is provided in the URL
                if (isset($_GET['id'])) {
                    // Retrieve the ID from the URL
                    $id = $_GET['id'];

                    // Fetch the title and description based on the provided ID
                    $sql = "SELECT title, description FROM files WHERE id = $id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Fetch the title and description
                        $row = $result->fetch_assoc();
                        $title = $row['title'];
                        $description = $row['description'];

                        // Display the title and description
                        echo '<h3>' . $title . '</h3>';
                        echo '<p>' . $description . '</p>';
                        echo '<div style="text-align: center; margin-top: 20px;">';
                        echo '<button style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 5px;" onclick="window.location.href=\'archive.php\'">Go Back</button>';
                        echo "<br>";
                        echo '<a style="margin: 5px 5px;" href="view_file.php?id=' . $id . '" class="btn" target="_blank" style="font-size: 1.5vw;"><i class="fa fa-eye"></i> View File</a>';
                        echo '<a style="margin: 5px 5px;" href="download.php?id=' . $id . '" class="btn" style="font-size: 1.5vw;"><i class="fas fa-download"></i> Download File</a>';
                        echo '</div>';
                    } else {
                        echo '<p>No information found for the provided ID.</p>';
                    }
                } else {
                    echo '<p>No ID provided.</p>';
                }

                // Close the database connection
                $conn->close();
                ?>
            </div>


            <!-- Assuming you have established a database connection in your config/connection.php file -->
            <?php require 'config/connection.php'; ?>

            <div class="swiper books-slider">
                <div class="swiper-wrapper">
                    <?php
                    // Check if the ID is provided in the URL
                    if (isset($_GET['id'])) {
                        // Retrieve the ID from the URL
                        $id = $_GET['id'];

                        // Fetch the image and other information based on the provided ID
                        $sql = "SELECT image, title FROM files WHERE id = $id";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Fetch the image path and title
                            $row = $result->fetch_assoc();
                            $imagePath = $row['image'];
                            $title = $row['title'];

                            // Display the image with the specified size
                            echo '<div class="swiper-slide">';
                            echo '<img src="' . $imagePath . '" alt="" width="320" height="510">';
                            echo '<div class="buttons">';
                            echo '</div>';
                            echo '</div>';
                        } else {
                            echo '<p>No information found for the provided ID.</p>';
                        }
                    } else {
                        echo '<p>No ID provided.</p>';
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </div>
            </div>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <script src="script.js"></script>
</body>

</html>