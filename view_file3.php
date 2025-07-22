<?php
// view_file.php

// Include the connection file
require 'connection.php';

// Assuming you have a unique identifier for the record, e.g., file_id
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Check if $id is not empty or null
if (!empty($id)) {
    // Retrieve file_path from the database
    $selectFileQuery = "SELECT file_path FROM files WHERE id = $id";

    // Check for SQL errors
    if ($result = $conn->query($selectFileQuery)) {
        if ($result->num_rows > 0) {
            $fileRow = $result->fetch_assoc();
            $filePath = $fileRow['file_path'];

            // Display the file
            header('Content-type: application/pdf'); // Adjust the content type based on your file type
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            @readfile($filePath);
        } else {
            echo 'File not found.';
        }
        $result->close(); // Close the result set
    } else {
        echo 'Error in SQL query: ' . $conn->error;
    }
} else {
    echo 'Invalid or missing file ID.';
}

// Close the database connection
$conn->close();
?>