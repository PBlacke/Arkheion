<?php
// Assume you have established a database connection in your connection.php file
require 'connection.php';

// Get the file ID from the URL
$fileId = isset($_GET['id']) ? $_GET['id'] : null;

// Validate the file ID (you should perform more validation as needed)
if ($fileId !== null) {
    // Fetch the department value from the curriculum table
    $departmentQuery = "SELECT department FROM department WHERE id = $fileId";
    $departmentResult = $conn->query($departmentQuery);

    if ($departmentResult && $departmentResult->num_rows > 0) {
        $departmentRow = $departmentResult->fetch_assoc();
        $departmentValue = $departmentRow['department'];

        // Perform the deletion in the curriculum table
        $deleteCurriculumQuery = "DELETE FROM department WHERE id = $fileId";
        if ($conn->query($deleteCurriculumQuery) === TRUE) {
            // Perform the deletion in the department table
            $deleteDepartmentQuery = "DELETE FROM curriculum WHERE department = '$departmentValue'";
            if ($conn->query($deleteDepartmentQuery) === TRUE) {
                // Return a success message (this will be sent back to the AJAX request)
                echo "File and corresponding department deleted successfully";
            } else {
                // Return an error message for the department deletion
                echo "Error deleting department: " . $conn->error;
            }
        } else {
            // Return an error message for the curriculum deletion
            echo "Error deleting file: " . $conn->error;
        }
    } else {
        // Return an error message if the department value is not found
        echo "Department value not found";
    }

    // Close the result set
    $departmentResult->close();
} else {
    // Return an error message if the file ID is not provided
    echo "File ID not provided";
}

// Close the database connection
$conn->close();
?>