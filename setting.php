<? 
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Arkheion</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="shortcut icon" type="x-icon" href="image/favicon.png">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
h2 {
    color: #0c1776;
}
</style>
</head>
<body class="w3-light-grey">

<!-- Page Container -->
<div class="w3-content w3-margin-top" style="max-width:1400px;">

  <!-- The Grid -->
  <div class="w3-row-padding">
  
    <!-- Left Column -->
    <?php include 'admin_nav.php'; ?>
    <!-- End Left Column -->

    <!-- Right Column -->
    <div class="w3-twothird">
    
    <div class="w3-container w3-card w3-white w3-margin-bottom">
        <h2 style="color: #0c1776;" class="w3-padding-16"><i class="fa fa-user fa-fw"></i>Update Admin</h2>
        <div class="w3-container">
            <h5 class="w3-opacity"><b>Here you can update the credentials for the admin</b></h5>
                <div style="text-align: center; overflow-x: auto; padding: 20px;">

<!-- Update Form -->
<form method="post" action="">
    <label for="updateUsername">New Username:</label>
    <input type="text" id="updateUsername" name="updateUsername" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">

    <label for="updateEmail">New Email:</label>
    <input type="email" id="updateEmail" name="updateEmail" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">

    <label for="updatePassword">New Password (at least 8 characters):</label>
    <input type="password" id="updatePassword" name="updatePassword" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">

    <label for="confirmUpdatePassword">Confirm Password:</label>
    <input type="password" id="confirmUpdatePassword" name="confirmUpdatePassword" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">

    <button type="submit" style="background-color: #4caf50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%;">Update Admin</button>
</form>

<?php
require 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $updateUsername = $_POST["updateUsername"];
    $updateEmail = $_POST["updateEmail"];
    $updatePassword = $_POST["updatePassword"];
    $confirmUpdatePassword = $_POST["confirmUpdatePassword"];

    // Check if passwords meet the length requirement and match
    if (strlen($updatePassword) >= 8 && $updatePassword === $confirmUpdatePassword) {
        // Hash the password
        $hashedUpdatePassword = password_hash($updatePassword, PASSWORD_BCRYPT);

        // Assuming you have a unique identifier for the record, e.g., user_id
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        // Update data in the database
        $updateSql = "UPDATE admin SET 
                username = '$updateUsername',
                email = '$updateEmail',
                password = '$hashedUpdatePassword'
                WHERE id = 1";  // Assuming you want to update the admin with id = 1, change it accordingly

        if ($conn->query($updateSql) === TRUE) {
             ?>
                                <script>
                                swal({
                                title: "Success!",
                                text: "Account updated successfully!",
                                icon: "success",
                                        });
                                </script>

                                <?php
            // echo "<p style='color: green; text-align: center; margin-top: 10px;'>Admin Successfully Updated</p>";
        } else {
            echo "<p style='color: red; text-align: center; margin-top: 10px;'>Error updating admin: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center; margin-top: 10px;'>Password does not meet the minimum length requirement or does not match</p>";
    }

    // Close the database connection
    $conn->close();
}
?>

    
  <!-- End Grid -->
  </div>
  
  <!-- End Page Container -->
</div>

<!-- <footer class="w3-container w3-red w3-center w3-margin-top">
  <p>Copyright © 2023. All rights reserved.</p>
  <p>EVSU-OC ONLINE ARCHIVING SYSTEM</p>
</footer> -->

<p>Arkheion ONLINE ARCHIVING SYSTEM</p>

</body>
</html>