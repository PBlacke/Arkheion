<?php
// edit.php
session_start();

// Include the connection file
require 'connection.php';

// Retrieve the ID from the URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Check if the ID is provided
if ($id) {
    // Query to fetch data based on the provided ID
    $query = "SELECT * FROM files WHERE id = $id";
    $result = $conn->query($query);

    // Check if the query was successful
    if ($result && $result->num_rows > 0) {
        // Fetch the data from the result set
        $row = $result->fetch_assoc();

        // Now you can use $row['title'], $row['uploader'], etc., to display the data in your form
        $title = $row['title'];
        $description = $row['description'];
        $uploader = $row['uploader'];
        $email = $row['email'];
        $year = $row['year'];
        $department = $row['department'];
        $curriculum = $row['curriculum'];
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

// Process form submission
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

    // Update the record in the database
    $updateQuery = "UPDATE files SET 
                    title = '$title',
                    description = '$description',
                    uploader = '$uploader',
                    email = '$email',
                    year = '$year',
                    department = '$department',
                    curriculum = '$curriculum',
                    status = '$status'
                    WHERE id = $id";

    if ($conn->query($updateQuery) === TRUE) {
        ?>
        <script>
            swal({
                title: "Success!",
                text: "Record updated successfully!",
                icon: "success",
            }).then(function() {
                window.location.href = "fac_archlist.php";
            });
        </script>
        <?php
    } else {
        $message = "Error updating record: " . $conn->error;
    }
}

// Fetch all records from the database for display
$selectQuery = "SELECT * FROM files";
$result = $conn->query($selectQuery);
$records = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $result->close();
}

// Close the database connection at the end
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Update Manuscript</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="shortcut icon" type="x-icon" href="LOGO.png">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
</style>
</head>
<body class="w3-light-grey">

<!-- Page Container -->
<div class="w3-content w3-margin-top" style="max-width:1400px;">

  <!-- The Grid -->
  <div class="w3-row-padding">
  
    <!-- Left Column -->
    <div class="w3-third">
        <?php include 'fac_nav.php'; ?>
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

    <script>
        // Display the message using JavaScript alert
        <?php if(isset($message)) { ?>
            alert("<?php echo $message; ?>");
            window.location.href = "fac_archlist.php";
        <?php } ?>
    </script>

    <div class="w3-container w3-card w3-white w3-margin-bottom">
        <h2><i class="fa fa-dashboard fa-fw"></i> Update Manuscript</h2>
        <div class="w3-container">
            <h5 class="w3-opacity"><b>Here are more details about the paper and you can also edit some information</b></h5>
            <div style="text-align: center; overflow-x: auto;">
                <form method="post" action="">
                    <label for="title"><i class="fa fa-file-text-o"></i> Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo $title; ?>" required>

                    <label for="description"><i class="fa fa-pencil"></i> Abstract:</label>
                    <textarea id="description" name="description" rows="30" cols="100" required><?php echo $description; ?></textarea>

                    <label for="uploader"><i class="fa fa-user"></i> Uploader:</label>
                    <input type="text" id="uploader" name="uploader" value="<?php echo $uploader; ?>" readonly required>

                    <label for="email"><i class="fa fa-envelope"></i> Email:</label>
                    <input type="text" id="email" name="email" value="<?php echo $email; ?>" readonly required>

                    <label for="year"><i class="fa fa-calendar"></i> Year:</label>
                    <input type="text" id="year" name="year" value="<?php echo $year; ?>" readonly required>

                    <label for="department"><i class="fa fa-building"></i> Department:</label>

                    <input id="selectedDepartment" name="department" value="<?php echo $department; ?>" readonly>

                    <label for="curriculum"><i class="fa fa-book"></i> Program:</label>

                    <input id="selectedcurriculum" name="curriculum" value="<?php echo $curriculum; ?>" readonly>

                    <label for="status"><i class="fa fa-check"></i> Status:</label>

                    <!-- Dropdown menu for status with options -->
                    <select id="statusDropdown" name="status" required>
                        <?php
                        $statusOptions = ['Unpublish', 'Published']; // Define available status options

                        // Loop through options and generate dropdown menu
                        foreach ($statusOptions as $option) {
                            $isSelected = ($status == $option) ? 'selected' : '';
                            echo "<option value=\"$option\" $isSelected>$option</option>";
                        }
                        ?>
                    </select>
                    <style type="text/css">
                        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }

        input[type=text], select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }
                    </style>

                    <!-- Add other input fields for uploader, year, department, curriculum, etc. -->
                    <a href="view_file.php?id=<?php echo $id; ?>" class="w3-button w3-blue" target="_blank"><i class="fa fa-eye"></i> View</a>
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