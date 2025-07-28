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
$query = "SELECT * FROM files LIMIT $offset, $recordsPerPage";
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
$totalRecordsQuery = "SELECT COUNT(*) as total FROM files";
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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

            <?php include 'admin_nav.php'; ?>

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

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Retrieve form data and sanitize
                    $title = mysqli_real_escape_string($conn, $_POST["title"]);
                    $description = mysqli_real_escape_string($conn, $_POST["description"]);
                    $uploader = mysqli_real_escape_string($conn, $_POST["uploader"]);
                    $email = mysqli_real_escape_string($conn, $_POST["email"]);
                    $year = mysqli_real_escape_string($conn, $_POST["year"]);
                    $department = mysqli_real_escape_string($conn, $_POST["department"]);
                    $curriculum = mysqli_real_escape_string($conn, $_POST["curriculum"]);
                    $status = mysqli_real_escape_string($conn, $_POST["status"]);

                    // Handle file uploads for manuscript and image
                    $uploadDirectory = "uploads/"; // Set your desired upload directory
                    $ManuFile = $_FILES["file"]["name"];

                    // Manuscript file
                    $manuscriptFile = $uploadDirectory . basename($ManuFile);
                    $manuscriptFileType = strtolower(pathinfo($manuscriptFile, PATHINFO_EXTENSION));

                    // Image file
                    $imageFile = $uploadDirectory . basename($_FILES["image"]["name"]);
                    $imageFileType = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));

                    // Check if the files are of the correct types
                    if ($manuscriptFileType != "pdf" || !in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                        $message = "Only PDF files for manuscript and JPG, JPEG, or PNG files for images are allowed.";
                    } else {
                        // Move uploaded files to the server
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $manuscriptFile) && move_uploaded_file($_FILES["image"]["tmp_name"], $imageFile)) {
                            // Insert the new record into the database
                            $insertQuery = "INSERT INTO files (title, description, uploader, email, year, department, curriculum, status, file_path, image)
                            VALUES ('$title', '$description', '$uploader', '$email', '$year', '$department', '$curriculum', '$status', '$ManuFile', '$imageFile')";

                            if ($conn->query($insertQuery) === TRUE) {
                                $message = "Manuscript added successfully";
                            } else {
                                $message = "Error inserting record. " . $conn->error;
                            }
                        } else {
                            $message = "Error uploading files.";
                        }
                    }
                }

                // Close the database connection
                $conn->close();
                ?>

                <script>
                    // Display the message using JavaScript alert
                    <?php if (isset($message)) { ?>
                        alert("<?php echo $message; ?>");
                        window.location.href = "dashboard.php";
                    <?php } ?>
                </script>

                <script>
                    function displayMessage(message) {
                        // Create a centered message div
                        var messageDiv = document.createElement("div");
                        messageDiv.style.position = "fixed";
                        messageDiv.style.top = "50%";
                        messageDiv.style.left = "50%";
                        messageDiv.style.transform = "translate(-50%, -50%)";
                        messageDiv.style.backgroundColor = "maroon";
                        messageDiv.style.color = "#fff";
                        messageDiv.style.padding = "20px";
                        messageDiv.style.borderRadius = "10px";
                        messageDiv.style.boxShadow = "0 0 10px rgba(0, 0, 0, 0.3)";
                        messageDiv.style.zIndex = "1000";
                        messageDiv.innerHTML = message;

                        // Append the message div to the body
                        document.body.appendChild(messageDiv);

                        // Remove the message div after a certain duration (e.g., 3 seconds)
                        setTimeout(function() {
                            document.body.removeChild(messageDiv);
                        }, 3000); // Adjust the duration as needed
                    }

                    function uploadFile(event) {
                        event.preventDefault(); // Prevent the default form submission

                        var formData = new FormData(document.getElementById("uploadForm"));

                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "upload.php", true);

                        // Track the upload progress
                        xhr.upload.onprogress = function(event) {
                            if (event.lengthComputable) {
                                var percentComplete = (event.loaded / event.total) * 100;
                                var progressMessage = "Uploading: " + percentComplete.toFixed(2) + "%";
                                displayMessage(progressMessage);
                            }
                        };

                        // Handle a successful upload
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                var successMessage = "Upload complete! Manuscript added successfully";
                                displayMessage(successMessage);
                                // Redirect to dashboard.php after successful upload
                                window.location.href = "dashboard.php";
                            } else {
                                var errorMessage = "Upload failed. Please try again.";
                                displayMessage(errorMessage);
                            }
                        };

                        // Handle errors during the upload
                        xhr.onerror = function() {
                            var errorMessage = "Error during upload. Please try again.";
                            displayMessage(errorMessage);
                        };

                        xhr.send(formData);
                    }
                </script>

                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2><i class="fa fa-book fa-fw"></i>Add New Manuscript</h2>
                    <div class="w3-container">
                        <h5 class="w3-opacity"><b>Here you can Add New Manuscript to our Online Archiving Management System</b></h5>
                        <div style="text-align: center; overflow-x: auto;">
                            <form method="post" action="upload.php" enctype="multipart/form-data" id="uploadForm" onsubmit="uploadFile(event)">
                                <label for="title"><i class="fa fa-file-text-o"></i> Title:</label>
                                <input type="text" id="title" name="title" required>

                                <label for="description"><i class="fa fa-pencil"></i> Abstract:</label>
                                <textarea id="description" name="description" rows="30" cols="100" required></textarea>

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

                                <label for="uploader"><i class="fa fa-user"></i> Uploader:</label>
                                <input type="text" id="uploader" name="uploader" value="<?php echo $username; ?>" readonly>

                                <label for="email"><i class="fa fa-envelope"></i> Email:</label>
                                <input type="text" id="email" name="email" value="<?php echo $email; ?>" readonly>


                                <label for="year"><i class="fa fa-calendar"></i> Year:</label>
                                <input type="text" id="year" name="year" required>

                                <label for="department"><i class="fa fa-building"></i> Department:</label>
                                <select id="department" name="department" class="select-input" required>
                                    <?php
                                    require 'config/connection.php';

                                    // Fetch unique department options from the database with an Active status
                                    $departmentQuery = "SELECT DISTINCT department FROM curriculum WHERE status = 'Active'";
                                    $departmentResult = $conn->query($departmentQuery);

                                    // Check if there are any rows in the result
                                    if ($departmentResult->num_rows > 0) {
                                        // Fetch and display each unique department as an option
                                        while ($departmentRow = $departmentResult->fetch_assoc()) {
                                            $departmentName = $departmentRow['department'];
                                            echo "<option value=\"$departmentName\">$departmentName</option>";
                                        }
                                    } else {
                                        // If no active departments are found, you can display a default option or handle it as needed
                                        echo "<option value=\"\">No active departments found</option>";
                                    }
                                    ?>
                                </select>

                                <label for="curriculum"><i class="fa fa-book"></i> Curriculum:</label>
                                <select id="curriculum" name="curriculum" class="select-input" required>
                                    <?php
                                    require 'config/connection.php';

                                    // Fetch unique curriculum options from the database with an Active status
                                    $curriculumQuery = "SELECT DISTINCT curriculum FROM curriculum WHERE status2 = 'Active'";
                                    $curriculumResult = $conn->query($curriculumQuery);

                                    // Check if there are any rows in the result
                                    if ($curriculumResult->num_rows > 0) {
                                        // Fetch and display each unique curriculum as an option
                                        while ($curriculumRow = $curriculumResult->fetch_assoc()) {
                                            $curriculumName = $curriculumRow['curriculum'];

                                            // Check if curriculum name is not empty before displaying
                                            if (!empty($curriculumName)) {
                                                echo "<option value=\"$curriculumName\">$curriculumName</option>";
                                            }
                                        }
                                    } else {
                                        // If no active curricula are found, you can display a default option or handle it as needed
                                        echo "<option value=\"\">No active curriculum found</option>";
                                    }
                                    ?>
                                </select>

                                <label for="file"><i class="fa fa-file"></i> Manuscript (PDF only):</label>
                                <input type="file" id="file" name="file" accept=".pdf" required>

                                <!-- Image file input -->
                                <label for="image"><i class="fa fa-image"></i> Cover Photo (JPG, JPEG, and PNG only):</label>
                                <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" required>

                                <label for="status"><i class="fa fa-check"></i> Status:</label>
                                <select id="status" name="status" class="select-input" required>
                                    <option value="Unpublish" selected>Unpublish</option>
                                    <option value="Published">Published</option>
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

                                <button type="submit"><i class="fa fa-check"></i> Add Manuscript</button>
                            </form>
                        </div>
                        <hr>
                    </div>
                </div>
                <div id="progress"></div>
                <!-- End Grid -->
            </div>

            <!-- End Page Container -->
        </div>

</body>

</html>