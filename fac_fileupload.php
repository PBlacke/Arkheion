<?php
require 'connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array();
    
    try {
        // Retrieve form data and sanitize
        $title = mysqli_real_escape_string($conn, $_POST["title"]);
        $description = mysqli_real_escape_string($conn, $_POST["description"]);
        $uploader = mysqli_real_escape_string($conn, $_POST["uploader"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $year = mysqli_real_escape_string($conn, $_POST["year"]);
        $department = mysqli_real_escape_string($conn, $_POST["department"]);
        $curriculum = mysqli_real_escape_string($conn, $_POST["curriculum"]);
        $status = mysqli_real_escape_string($conn, $_POST["status"]);

        // Debug log
        error_log("Received POST data: " . print_r($_POST, true));
        error_log("Received FILES data: " . print_r($_FILES, true));

        // Handle file uploads for manuscript and image
        $uploadDirectory = "uploads/"; // Set your desired upload directory
        
        // Check if directory exists and is writable
        if (!is_dir($uploadDirectory)) {
            throw new Exception("Upload directory does not exist");
        }
        if (!is_writable($uploadDirectory)) {
            throw new Exception("Upload directory is not writable");
        }

        $ManuFile = $_FILES["file"]["name"];
        $imageFileName = $_FILES["image"]["name"];

        // Manuscript file
        $manuscriptFile = $uploadDirectory . basename($ManuFile);
        $manuscriptFileType = strtolower(pathinfo($manuscriptFile, PATHINFO_EXTENSION));

        // Image file
        $imageFile = $uploadDirectory . basename($imageFileName);
        $imageFileType = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));

        error_log("File types - Manuscript: $manuscriptFileType, Image: $imageFileType");

        // Check if the files are of the correct types
        if ($manuscriptFileType == "pdf" && in_array($imageFileType, ["jpg", "jpeg", "png"], true)) {
            // Move uploaded files to the server
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $manuscriptFile) && 
                move_uploaded_file($_FILES["image"]["tmp_name"], $imageFile)) {
                
                // Insert the new record into the database
                $insertQuery = "INSERT INTO files (title, description, uploader, email, year, department, curriculum, status, file_path, filename, image)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($insertQuery);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }

                $imageFullPath = $uploadDirectory . $imageFileName;
                $stmt->bind_param("sssssssssss", $title, $description, $uploader, $email, $year, $department, $curriculum, $status, $ManuFile, $ManuFile, $imageFullPath);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Manuscript added successfully!";
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception("Error uploading files. File upload error codes - Manuscript: " . $_FILES["file"]["error"] . ", Image: " . $_FILES["image"]["error"]);
            }
        } else {
            throw new Exception("Invalid file types. Manuscript must be PDF and image must be JPG, JPEG, or PNG. Got manuscript: $manuscriptFileType, image: $imageFileType");
        }
    } catch (Exception $e) {
        error_log("Error in file upload: " . $e->getMessage());
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    }

    // Close the database connection
    $conn->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} else {
    // If the form is not submitted via POST, redirect to the dashboard
    header("Location: faculty.php");
    exit();
}
?>