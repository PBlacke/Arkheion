<?php
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

// Define the number of records per page
$recordsPerPage = 10;

// Determine the current page number
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $recordsPerPage;

// Query to fetch data from the database with pagination
$query = "SELECT * FROM curriculum LIMIT $offset, $recordsPerPage";
$result = $conn->query($query);

// Check if there are any rows in the result
$rows = [];
if ($result->num_rows > 0) {
    // Fetch all rows and store them in the $rows array
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

// Query to get the total number of records for pagination
$totalRecordsQuery = "SELECT COUNT(*) as total FROM curriculum";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Calculate the total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Arkheion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
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

                    // Insert the new record into the curriculum table
                    $insertCurriculumQuery = "INSERT INTO curriculum (department, status)
                                      VALUES ('$department', '$status')";

                    // Check if curriculum record insertion is successful
                    if ($conn->query($insertCurriculumQuery) === TRUE) {
                        // Get the curriculum ID of the inserted record
                        $curriculumId = $conn->insert_id;

                        // Insert the department into the department table
                        $insertDepartmentQuery = "INSERT INTO department (department, status)
                                          VALUES ('$department', '$status')";

                        // Check if department record insertion is successful
                        if ($conn->query($insertDepartmentQuery) === TRUE) {
                            $message = "Record inserted successfully";
                        } else {
                            $message = "Error inserting department record: " . $conn->error;
                        }
                    } else {
                        $message = "Error inserting curriculum record: " . $conn->error;
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
                    <h2><i class="fa fa-building fa-fw"></i>Add New Department</h2>
                    <div class="w3-container">
                        <h5 class="w3-opacity"><b>Here you can Add New Department to the System</b></h5>
                        <div style="text-align: center; overflow-x: auto;">
                            <form method="post" action="">
                                <label for="department"><i class="fa fa-building fa-fw"></i> Department:</label>
                                <input type="text" id="department" name="department" required>

                                <!-- <label for="curriculum"><i class="fa fa-pencil"></i> Curriculum:</label>
                        <textarea id="curriculum" name="curriculum" rows="" cols="89" required></textarea> -->

                                <label for="status"><i class="fa fa-check"></i> Status:</label>
                                <select id="status" name="status" class="select-input" required>
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
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

                                <!-- Add other input fields for uploader, year, department, curriculum, etc. -->

                                <button type="submit"><i class="fa fa-check"></i> Add Department</button>
                            </form>
                        </div>
                        <hr>
                    </div>
                </div>

                <!-- End Grid -->
            </div>

            <!-- End Page Container -->
        </div>

        <!-- <footer class="w3-container w3-red w3-center w3-margin-top">
  <p>Copyright © 2023. All rights reserved.</p>
  <p>EVSU-OC ONLINE ARCHIVING SYSTEM</p>
</footer> -->

</body>

</html>