<?php
require 'config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $uploader = mysqli_real_escape_string($conn, $_POST["uploader"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $year = mysqli_real_escape_string($conn, $_POST["year"]);
    $department = mysqli_real_escape_string($conn, $_POST["department"]);
    $curriculum = mysqli_real_escape_string($conn, $_POST["curriculum"]);
    $status = mysqli_real_escape_string($conn, $_POST["status"]);

    // Handle file uploads for manuscript and image
    $uploadDirectory = "uploads/"; // Set your desired upload directory
    $ManuFile = $_FILES["file"]["name"];

    // Manuscript file
    $manuscriptFile = $uploadDirectory . basename($ManuFile);
    $manuscriptFileType = strtolower(pathinfo($manuscriptFile, PATHINFO_EXTENSION));

    // Image file
    $imageFile = $uploadDirectory . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));

    // Check if the files are of the correct types
    if ($manuscriptFileType != "pdf" || !in_array($imageFileType, ["jpg", "jpeg", "png"])) {
        $message = "Only PDF files for manuscript and JPG, JPEG, or PNG files for images are allowed.";
    } else {
        // Move uploaded files to the server
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $manuscriptFile) && move_uploaded_file($_FILES["image"]["tmp_name"], $imageFile)) {
            // Insert the new record into the database
            $insertQuery = "INSERT INTO files (title, description, uploader, email, year, department, curriculum, status, file_path, filename, image)
                            VALUES ('$title', '$description', '$uploader', '$email', '$year', '$department', '$curriculum', '$status', '$ManuFile', '$ManuFile', '$uploadDirectory$imageFile')";

            if ($conn->query($insertQuery) === TRUE) {
                $message = "Record inserted successfully";
            } else {
                $message = "Error inserting record: " . $conn->error;
            }
        } else {
            $message = "Error uploading files.";
        }
    }

    // Close the database connection
    $conn->close();

    // Redirect back to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If the form is not submitted via POST, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}
