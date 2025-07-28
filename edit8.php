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
    $query = "SELECT * FROM department WHERE id = $id";
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
    <title>Arkheion</title>
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
            <div class="w3-third">

                <div class="w3-white w3-text-grey w3-card-4">
                    <div class="w3-display-container">
                        <div class="w3-display-bottomleft w3-container w3-text-black">
                        </div>
                    </div>
                    <div class="w3-container">
                        <?php
                        require 'config/connection.php';

                        $sql = "SELECT username, email FROM admin WHERE id = 1";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $username = $row['username'];
                            $email = $row['email'];
                        } else {
                            $username = "Default Username";
                            $email = "Default Email";
                        }

                        $conn->close();
                        ?>

                        <p><i class="fa fa-briefcase fa-fw w3-margin-right w3-large w3-text-red"></i><?php echo $username; ?></p>
                        <p><i class="fa fa-home fa-fw w3-margin-right w3-large w3-text-red"></i>Arkheion</p>
                        <p><i class="fa fa-envelope fa-fw w3-margin-right w3-large w3-text-red"></i><?php echo $email; ?></p>
                        <hr>

                        <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-red"></i>
                                Options</b></p>

                        <style>
                            .list:hover {
                                background-color: lightseagreen;
                                /* Change the hover background color to green */
                                color: white;
                                /* Set the text color to white on hover */
                                padding: 5px;
                                border-radius: 10px;
                                text-decoration: none;
                            }

                            .list {
                                display: flex;
                                align-items: center;
                                text-decoration: none;
                                color: black;
                                /* Add your preferred text color */
                            }

                            .list i {
                                margin-right: 8px;
                                /* Adjust the margin as needed */
                            }
                        </style>

                        <style>
                            .loading-overlay {
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(255, 255, 255, 0.8);
                                /* Semi-transparent white background */
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                z-index: 9999;
                            }

                            .loading-spinner {
                                border: 8px solid #f3f3f3;
                                /* Light gray border */
                                border-top: 8px solid #3498db;
                                /* Blue border on top */
                                border-radius: 50%;
                                width: 50px;
                                height: 50px;
                                animation: spin 1s linear infinite;
                                /* Spin animation */
                            }

                            @keyframes spin {
                                0% {
                                    transform: rotate(0deg);
                                }

                                100% {
                                    transform: rotate(360deg);
                                }
                            }
                        </style>

                        <script>
                            function showLoading(link) {
                                // Show loading overlay with spinner
                                var loadingOverlay = document.createElement('div');
                                loadingOverlay.className = 'loading-overlay';
                                var spinner = document.createElement('div');
                                spinner.className = 'loading-spinner';
                                loadingOverlay.appendChild(spinner);
                                document.body.appendChild(loadingOverlay);

                                // Set a timeout to simulate a loading time (2 to 3 seconds)
                                setTimeout(function() {
                                    // After 2 to 3 seconds, navigate to the specified link
                                    window.location.href = link;
                                }, Math.floor(Math.random() * 1000) + 500); // Random delay between 2 to 3 seconds
                            }
                        </script>

                        <!-- Update your links to call the showLoading function -->
                        <a href="javascript:void(0);" class="list" onclick="showLoading('dashboard.php')">
                            <i class="fa fa-dashboard fa-fw"></i>
                            <p class="list">Dashboard</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="javascript:void(0);" class="list" onclick="showLoading('facultylist2.php')">
                            <i class="fa fa-users fa-fw"></i>
                            <p class="list">Staff List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="javascript:void(0);" class="list" onclick="showLoading('registration.php')">
                            <i class="fa fa-user-plus fa-fw"></i>
                            <p class="list">Add Staff</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="javascript:void(0);" class="list" onclick="showLoading('archivelist.php')">
                            <i class="fa fa-archive fa-fw"></i>
                            <p class="list">Archive List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <br>

                        <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-red"></i>Settings</b></p>
                        <style>
                            .loading-overlay {
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(255, 255, 255, 0.8);
                                /* Semi-transparent white background */
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                z-index: 9999;
                            }

                            .loading-spinner {
                                border: 8px solid #f3f3f3;
                                /* Light gray border */
                                border-top: 8px solid #3498db;
                                /* Blue border on top */
                                border-radius: 50%;
                                width: 50px;
                                height: 50px;
                                animation: spin 1s linear infinite;
                                /* Spin animation */
                            }

                            @keyframes spin {
                                0% {
                                    transform: rotate(0deg);
                                }

                                100% {
                                    transform: rotate(360deg);
                                }
                            }
                        </style>

                        <script>
                            function showLoading(link) {
                                // Show loading overlay with spinner
                                var loadingOverlay = document.createElement('div');
                                loadingOverlay.className = 'loading-overlay';
                                var spinner = document.createElement('div');
                                spinner.className = 'loading-spinner';
                                loadingOverlay.appendChild(spinner);
                                document.body.appendChild(loadingOverlay);

                                // Set a timeout to simulate a loading time (2 to 3 seconds)
                                setTimeout(function() {
                                    // After 2 to 3 seconds, navigate to the specified link
                                    window.location.href = link;
                                }, Math.floor(Math.random() * 1000) + 500); // Random delay between 2 to 3 seconds
                            }
                        </script>

                        <!-- Update your links to call the showLoading function -->
                        <a href="javascript:void(0);" class="list" onclick="showLoading('departmentlist.php')">
                            <i class="fa fa-building fa-fw"></i>
                            <p class="list">Department List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="javascript:void(0);" class="list" onclick="showLoading('curriculumlist.php')">
                            <i class="fa fa-book fa-fw"></i>
                            <p class="list">Program List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="javascript:void(0);" class="list" onclick="showLoading('setting.php')">
                            <i class="fa fa-cogs fa-fw"></i>
                            <p class="list">Settings</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="javascript:void(0);" class="list" onclick="showLoading('records.php')">
                            <i class="fa fa-archive fa-fw"></i>
                            <p class="list">Records</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="javascript:void(0);" class="list" onclick="showLoading('logout.php')">
                            <i class="fa fa-sign-out fa-fw"></i>
                            <p class="list">Logout</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <br>
                    </div>
                </div><br>

                <!-- End Left Column -->
            </div>

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
                        color: red;
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
                            // $message = "Records updated successfully";

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