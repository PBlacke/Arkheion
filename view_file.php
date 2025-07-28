<?php
// Include the connection file
require 'config/connection.php';

// Assuming you have a unique identifier for the record, e.g., file_id
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Check if $id is not empty or null
if (!empty($id)) {
    // Use prepared statement to prevent SQL injection
    $selectFileQuery = "SELECT file_path FROM files WHERE id = ?";
    $stmt = $conn->prepare($selectFileQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fileRow = $result->fetch_assoc();
        $filePath = 'uploads/' . $fileRow['file_path'];  // Add uploads directory

        // Check if file exists
        if (file_exists($filePath)) {
            // Set headers for PDF display
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));

            // Output the file
            readfile($filePath);
            exit;
        } else {
            echo 'File not found on server: ' . htmlspecialchars($filePath);
        }
    } else {
        echo 'File record not found in database.';
    }
    $stmt->close();
} else {
    echo 'Invalid or missing file ID.';
}

$conn->close();
