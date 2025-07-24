<?php
// edit.php

// Include the connection file
require 'config/connection.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: index.php");
    exit();
}

// Retrieve the ID from the URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Check if the ID is provided
if ($id) {
    // Query to fetch data based on the provided ID
    $query = "SELECT * FROM curriculum WHERE id = $id";
    $result = $conn->query($query);

    // Check if the query was successful
    if ($result && $result->num_rows > 0) {
        // Fetch the data from the result set
        $row = $result->fetch_assoc();

        // Now you can use $row['title'], $row['uploader'], etc., to display the data in your form
        $department = $row['department'];
        $status = $row['status'];

        // Close the result set
        $result->close();
    } else {
        // Handle the case where the ID doesn't match any record
        echo "Record not found";
    }
} else {
    // Handle the case where no ID is provided
    echo "Invalid ID";
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>EVSU-OC</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="shortcut icon" type="x-icon" href="LOGO.png">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        html,
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Roboto", sans-serif
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

                <style>
                    body {
                        font-family: Arial, sans-serif;
                    }

                    .w3-container {
                        max-width: 800px;
                        margin: 0 auto;
                    }

                    .w3-card {
                        border-radius: 10px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }

                    h2 {
                        color: #0c1776;
                    }

                    form {
                        text-align: left;
                        margin-top: 20px;
                    }

                    label {
                        display: block;
                        margin-top: 10px;
                    }

                    input {
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                        margin-bottom: 15px;
                        box-sizing: border-box;
                    }

                    button {
                        background-color: #4CAF50;
                        color: white;
                        padding: 10px 15px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                    }

                    button:hover {
                        background-color: #45a049;
                    }
                </style>

                <?php
                // Assume you have established a database connection in your config/connection.php file
                require 'config/connection.php';

                // Check if the form is submitted
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Retrieve form data and sanitize
                    $department = mysqli_real_escape_string($conn, $_POST["department"]);
                    $status = mysqli_real_escape_string($conn, $_POST["status"]);

                    // Assuming you have a unique identifier for the record, e.g., curriculum_id
                    $id = isset($_GET['id']) ? $_GET['id'] : null;

                    // Update the record in the curriculum table
                    $updateCurriculumQuery = "UPDATE curriculum SET 
                              department = '$department',
                              status = '$status'
                              WHERE department = $id";

                    // Check if curriculum record update is successful
                    if ($conn->query($updateCurriculumQuery) === TRUE) {
                        // Update the record in the department table
                        $updateDepartmentQuery = "UPDATE department SET 
                                  department = '$department',
                                  status = '$status'
                                  WHERE department = $id";

                        // Check if department record update is successful
                        if ($conn->query($updateDepartmentQuery) === TRUE) {
                ?>
                            <script>
                                swal({
                                    title: "Success!",
                                    text: "Department updated successfully!",
                                    icon: "success",
                                });
                            </script>

                <?php

                            // If the status is set to "Inactive," update the status in the curriculum and department tables
                            if ($status == 'Inactive') {
                                $updateStatusQuery = "UPDATE curriculum SET status = 'Inactive' WHERE department = '$department'";
                                $conn->query($updateStatusQuery);

                                $updateStatusQuery = "UPDATE department SET status = 'Inactive' WHERE department = '$department'";
                                $conn->query($updateStatusQuery);
                            }
                            if ($status == 'Active') {
                                $updateStatusQuery = "UPDATE curriculum SET status = 'Active' WHERE department = '$department'";
                                $conn->query($updateStatusQuery);

                                $updateStatusQuery = "UPDATE department SET status = 'Active' WHERE department = '$department'";
                                $conn->query($updateStatusQuery);
                            }
                        } else {
                            $message = "Error updating department record: " . $conn->error;
                        }
                    } else {
                        $message = "Error updating curriculum record: " . $conn->error;
                    }
                }

                // Fetch all records from the curriculum table for display
                $selectQuery = "SELECT * FROM curriculum";
                $result = $conn->query($selectQuery);
                $records = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $records[] = $row;
                    }
                }

                // Close the database connection
                $conn->close();
                ?>

                <script>
                    // Display the message using JavaScript alert
                    <?php if (isset($message)) { ?>
                        alert("<?php echo $message; ?>");
                        window.location.href = "departmentlist.php";
                    <?php } ?>
                </script>

                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2><i class="fa fa-building fa-fw"></i> Update Department</h2>
                    <div class="w3-container">
                        <h5 class="w3-opacity"><b>Here you can update the Department information</b></h5>
                        <div style="text-align: center; overflow-x: auto;">
                            <form method="post" action="">
                                <label for="department"><i class="fa fa-building fa-fw"></i> Department:</label>
                                <input type="text" id="department" name="department" value="<?php echo $department; ?>" readonly required>

                                <label for="status"><i class="fa fa-check"></i> Status:</label>
                                <select id="status" name="status" class="select-input" required>
                                    <option value="Active" <?php echo ($status == 'Active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo ($status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>

                                <style>
                                    /* Add this style in the head section of your HTML or in your CSS file */
                                    .select-input {
                                        width: 100%;
                                        padding: 8px;
                                        margin: 5px 0;
                                        display: inline-block;
                                        border: 1px solid #ccc;
                                        border-radius: 4px;
                                        box-sizing: border-box;
                                    }
                                </style>
                                <style>
                                    /* Style for the BACK link */
                                    a.back-link {
                                        display: inline-block;
                                        padding: 10px 15px;
                                        background-color: #4CAF50;
                                        color: #fff;
                                        text-decoration: none;
                                        border-radius: 5px;
                                        transition: background-color 0.3s;
                                    }

                                    a.back-link:hover {
                                        background-color: #45a049;
                                        /* Darker shade of maroon on hover */
                                    }
                                </style>
                                <!-- Add other input fields for uploader, year, department, curriculum, etc. -->
                                <a href="departmentlist.php" class="back-link">BACK</a>
                                <button type="submit"><i class="fa fa-check"></i> Update</button>
                            </form>
                        </div>
                        <hr>
                    </div>
                </div>

                <!-- End Grid -->
            </div>

            <!-- End Page Container -->
        </div>

</body>

</html>