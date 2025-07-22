<?php
// Include the connection file
require 'connection.php';

// Assuming you have a unique identifier for the record, e.g., file_id
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Check if $id is not empty or null
if (!empty($id)) {
    // Use a prepared statement to avoid SQL injection
    $selectFileQuery = "SELECT file_path FROM files WHERE id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($selectFileQuery)) {
        // Bind the parameter
        $stmt->bind_param("i", $id);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Bind the result
            $stmt->bind_result($filePath);
            
            // Fetch the result
            if ($stmt->fetch()) {
                // Display the file
                header('Content-type: application/pdf'); // Adjust the content type based on your file type
                header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
                @readfile($filePath);
            } else {
                echo 'File not found.';
            }
        } else {
            echo 'Error in SQL execution: ' . $stmt->error;
        }
        
        // Close the statement
        $stmt->close();
    } else {
        echo 'Error in SQL preparation: ' . $conn->error;
    }
} else {
    echo 'Invalid or missing file ID.';
}

// Close the database connection
$conn->close();
?>