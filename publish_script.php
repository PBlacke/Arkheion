<?php
// Assume you have established a database connection in your connection.php file
require 'connection.php';

// Get the file ID from the URL
$fileId = isset($_GET['id']) ? $_GET['id'] : null;

// Validate the file ID (you should perform more validation as needed)
if ($fileId !== null) {
    // Update the status in the database to 'Published'
    $publishQuery = "UPDATE files SET status = 'Published' WHERE id = $fileId";

    if ($conn->query($publishQuery) === TRUE) {
        // Return a success message (this will be sent back to the AJAX request)
        echo "Paper published successfully";
    } else {
        // Return an error message (this will be sent back to the AJAX request)
        echo "Error publishing paper: " . $conn->error;
    }
} else {
    // Return an error message if the file ID is not provided
    echo "File ID not provided";
}

// Close the database connection
$conn->close();
?>