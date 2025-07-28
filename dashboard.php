<?php
// Include the connection file
require 'config/connection.php';

// Start the session
session_start();

// Check if the user is logged in
// if (!isset($_SESSION['admin_id'])) {
//     // Redirect to the login page if the user is not logged in
//     header("Location: index.php");
//     exit();
// }

// Define the number of records per page
$recordsPerPage = 10;

// Determine the current page number
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $recordsPerPage;

// Define the status you want to display (e.g., PUBLISHED)
$statusToDisplay = 'Unpublish';

// Query to fetch data from the database with pagination and status filter
$query = "SELECT * FROM files WHERE status = '$statusToDisplay' LIMIT $offset, $recordsPerPage";
$result = $conn->query($query);

// Check if there are any rows in the result
$rows = [];
if ($result->num_rows > 0) {
    // Fetch all rows and store them in the $rows array
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

// Query to get the total number of records for pagination with status filter
$totalRecordsQuery = "SELECT COUNT(*) as total FROM files WHERE status = '$statusToDisplay'";
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
    <link rel="stylesheet" href="css/output.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnc.heyzine.com/release/jquery.pdfflipbook.3.js"></script>
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

            <?php include 'admin_nav.php'; ?>

            <!-- Right Column -->
            <div class="w3-twothird">

                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2 style="color: #0c1776;" class="w3-padding-16"><i class="fa fa-dashboard fa-fw"></i>Dashboard</h2>
                    <div class="w3-container">
                        <h5 class="w3-opacity"><b>Welcome to Arkheion; Comprehensive Research Repository </b></h5>
                        <style type="text/css">
                            .clickable-box {
                                cursor: pointer;
                                padding: 1px;
                                width: 15%;
                                background-color: #4caf50;
                                /* Green color, you can change it */
                                color: white;
                                border: none;
                                border-radius: 4px;
                                text-align: center;
                                transition: background-color 0.3s;
                                float: left;
                                /* Float the box to the left */
                                margin-bottom: 10px;
                                /* Add margin at the bottom */
                            }

                            .clickable-box:hover {
                                background-color: #45a049;
                                /* Darker shade on hover, you can change it */
                            }

                            .search-container {
                                float: right;
                                /* Float the search container to the right */
                                margin-top: 10px;
                                margin-right: 20px;
                            }

                            .search-container input[type=text] {
                                padding: 6px;
                                margin-top: 2px;
                                font-size: 14px;
                                border: none;
                            }

                            .search-container button {
                                padding: 6px 10px;
                                margin-top: 2px;
                                background: #4CAF50;
                                color: white;
                                font-size: 14px;
                                border: none;
                                cursor: pointer;
                            }

                            .search-container button:hover {
                                background: #45a049;
                            }

                            .search-container input[type=text] {
                                padding: 6px;
                                margin-top: 2px;
                                font-size: 14px;
                                border: 1px solid #ccc;
                                /* Add a border to the input */
                                border-radius: 4px;
                                /* Optional: Add rounded corners to the input */
                            }
                        </style>

                        <div class="clickable-box" onclick="location.href='add.php';">
                            <h5><b>Add New</b></h5>
                        </div>

                        <!-- Add the search bar with search icon to the right -->
                        <div class="search-container">
                            <form action="search.php" method="GET">
                                <input type="text" placeholder="Search Title..." name="search">
                                <button type="submit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>

                        <div style="clear: both;"></div> <!-- Add a clearing element to clear the floats -->

                        <br>

                        <div style="text-align: center; overflow-x: auto;">
                            <table class="w3-table-all">
                                <thead>
                                    <tr class="w3-light-grey">
                                        <th>TITLE</th>
                                        <th>UPLOADER</th>
                                        <th>STATUS</th>
                                        <th>DEPARTMENT</th>
                                        <th>CURRICULUM</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row) : ?>
                                        <tr>
                                            <td><?php echo $row['title']; ?></td>
                                            <td><?php echo $row['uploader']; ?></td>
                                            <td><?php echo $row['status']; ?></td>
                                            <td><?php echo $row['department']; ?></td>
                                            <td><?php echo $row['curriculum']; ?></td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="w3-button w3-blue">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                <!-- Add this code for each row where you want to include the publish button -->
                                                <button class="w3-button w3-green" onclick="confirmPublish(<?php echo $row['id']; ?>)"><i class="fa fa-check"></i></button>

                                                <script>
                                                    function confirmPublish(fileId) {
                                                        var confirmPublish = confirm("Are you sure you want to publish this paper?");

                                                        if (confirmPublish) {
                                                            // If the user clicks 'OK' (true), initiate the AJAX request
                                                            publishPaper(fileId);
                                                        } else {
                                                            // If the user clicks 'Cancel' (false), do nothing or provide feedback
                                                            alert("Publishing canceled");
                                                        }
                                                    }

                                                    function publishPaper(fileId) {
                                                        // Perform an AJAX request to the server-side script that handles publishing
                                                        var xhr = new XMLHttpRequest();

                                                        xhr.onreadystatechange = function() {
                                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                                if (xhr.status === 200) {
                                                                    // Successful response from the server
                                                                    alert("You have successfully published this paper");
                                                                    window.location.reload(); // Reload the page after successful publishing
                                                                } else {
                                                                    // Handle errors or display appropriate feedback
                                                                    alert("Error publishing paper: " + xhr.responseText);
                                                                }
                                                            }
                                                        };

                                                        // Replace 'publish_script.php' with the actual server-side script handling publishing
                                                        xhr.open("GET", "publish_script.php?id=" + fileId, true);
                                                        xhr.send();
                                                    }
                                                </script>

                                                <!-- Add this code for each row where you want to include the delete button -->
                                                <button class="w3-button w3-red" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="fa fa-trash"></i></button>

                                                <script>
                                                    function confirmDelete(fileId) {
                                                        var confirmDelete = confirm("Are you sure you want to delete this?");

                                                        if (confirmDelete) {
                                                            // If the user clicks 'OK' (true), initiate the AJAX request
                                                            deleteFile(fileId);
                                                        } else {
                                                            // If the user clicks 'Cancel' (false), do nothing or provide feedback
                                                            alert("Deletion canceled");
                                                        }
                                                    }

                                                    function deleteFile(fileId) {
                                                        // Perform an AJAX request to the server-side script that handles deletion
                                                        var xhr = new XMLHttpRequest();

                                                        xhr.onreadystatechange = function() {
                                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                                if (xhr.status === 200) {
                                                                    // Successful response from the server
                                                                    alert("File deleted successfully");
                                                                    window.location.reload(); // Reload the page after successful deletion
                                                                } else {
                                                                    // Handle errors or display appropriate feedback
                                                                    alert("Error deleting file: " + xhr.responseText);
                                                                }
                                                            }
                                                        };

                                                        // Replace 'delete_script.php' with the actual server-side script handling deletion
                                                        xhr.open("GET", "delete_script.php?id=" + fileId, true);
                                                        xhr.send();
                                                    }
                                                </script>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- Pagination links -->
                            <div class="w3-margin-top">
                                <?php if ($current_page > 1) : ?>
                                    <a href="?page=<?php echo ($current_page - 1); ?>" class="w3-button w3-black">Previous</a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                    <a href="?page=<?php echo $i; ?>" class="w3-button <?php echo ($current_page == $i) ? 'w3-blue' : 'w3-black'; ?>"><?php echo $i; ?></a>
                                <?php endfor; ?>

                                <?php if ($current_page < $totalPages) : ?>
                                    <a href="?page=<?php echo ($current_page + 1); ?>" class="w3-button w3-black">Next</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>

                <!-- End Grid -->
            </div>

            <!-- End Page Container -->
        </div>

<<<<<<< HEAD
=======
        <!-- <footer class="w3-container w3-red w3-center w3-margin-top">
  <p>Copyright © 2023. All rights reserved.</p>
  <p>EVSU-OC ONLINE ARCHIVING SYSTEM</p>
</footer> -->

>>>>>>> d7e7207fa2a6626e7a9e8c310c1d967b0ad2637c
</body>

</html>