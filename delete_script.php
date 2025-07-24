<?php
// Assume you have established a database connection in your config/connection.php file
require 'config/connection.php';

// Get the file ID from the URL
$fileId = isset($_GET['id']) ? $_GET['id'] : null;

// Validate the file ID (you should perform more validation as needed)
if ($fileId !== null) {
    // Perform the deletion in the database
    $deleteQuery = "DELETE FROM files WHERE id = $fileId";

    if ($conn->query($deleteQuery) === TRUE) {
        // Return a success message (this will be sent back to the AJAX request)
        echo "File deleted successfully";
    } else {
        // Return an error message (this will be sent back to the AJAX request)
        echo "Error deleting file: " . $conn->error;
    }
} else {
    // Return an error message if the file ID is not provided
    echo "File ID not provided";
}

// Close the database connection
$conn->close();
