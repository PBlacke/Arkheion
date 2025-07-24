<?php
// download.php

if (isset($_GET['id'])) {
    $fileId = $_GET['id'];

    // Include database connection
    require 'config/connection.php';

    // Fetch file details from the database based on $fileId
    $sql = "SELECT * FROM files WHERE id = $fileId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $fileDetails = $result->fetch_assoc();

        // Set appropriate headers for download (PDF)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileDetails['title'] . '.pdf"');

        // Specify the full path to the file in the "uploads" directory
        $filePath = 'uploads/' . $fileDetails['file_path'];

        readfile($filePath);

        // Update download count in the database
        $newDownloadCount = $fileDetails['download_count'] + 1;
        $updateSql = "UPDATE files SET download_count = $newDownloadCount WHERE id = $fileId";
        $conn->query($updateSql);

        // Close the database connection
        $conn->close();

        exit;
    } else {
        // Handle the case when the file with the given id is not found
        echo "File not found.";
    }
} else {
    // Handle the case when 'id' is not set
    echo "Invalid request.";
}
