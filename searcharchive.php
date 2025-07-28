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

// Check if the search query is set in the URL
if (isset($_GET['search'])) {
    // Sanitize the search query to prevent SQL injection
    $searchTitle = mysqli_real_escape_string($conn, $_GET['search']);

    // Define the status you want to display (e.g., PUBLISHED)
    $statusToDisplay = 'Published';

    // Query to fetch data from the database with pagination, status, and search filter
    $query = "SELECT * FROM files WHERE status = '$statusToDisplay' AND title LIKE '%$searchTitle%' LIMIT $offset, $recordsPerPage";
    $result = $conn->query($query);

    // Check if there are any rows in the result
    $rows = [];
    if ($result->num_rows > 0) {
        // Fetch all rows and store them in the $rows array
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    // Query to get the total number of records for pagination with status and search filter
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM files WHERE status = '$statusToDisplay' AND title LIKE '%$searchTitle%'";
    $totalRecordsResult = $conn->query($totalRecordsQuery);
    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

    // Calculate the total number of pages
    $totalPages = ceil($totalRecords / $recordsPerPage);
} else {
    // Redirect to the main page if no search query is provided
    header("Location: your_main_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Search</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

                        <a href="dashboard.php" class="list">
                            <i class="fa fa-dashboard fa-fw"></i>
                            <p class="list">Dashboard</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="facultylist2.php" class="list">
                            <i class="fa fa-users fa-fw"></i>
                            <p class="list">Staff List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="registration.php" class="list">
                            <i class="fa fa-user-plus fa-fw"></i>
                            <p class="list">Add Staff</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="archivelist.php" class="list">
                            <i class="fa fa-archive fa-fw"></i>
                            <p class="list">Archive List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <br>

                        <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-red"></i>Settings</b></p>
                        <a href="departmentlist.php" class="list">
                            <i class="fa fa-building fa-fw"></i>
                            <p class="list">Department List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="curriculumlist.php" class="list">
                            <i class="fa fa-book fa-fw"></i>
                            <p class="list">Program List</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="setting.php" class="list">
                            <i class="fa fa-cogs fa-fw"></i>
                            <p class="list">Settings</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="records.php" class="list">
                            <i class="fa fa-archive fa-fw"></i>
                            <p class="list">Records</p>
                        </a>
                        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

                        <a href="logout.php" class="list">
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

                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2 style="color: red;" class="w3-padding-16"><i class="fa fa-search"></i> Search Results</h2>
                    <div class="w3-container">
                        <h5 class="w3-opacity"><b>Search Results for '<?php echo $searchTitle; ?>'</b></h5>
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

                        <!-- Include the search bar with search icon to the right -->
                        <div class="search-container">
                            <form action="searcharchive.php" method="GET">
                                <input type="text" placeholder="Search Title..." name="search" value="<?php echo $searchTitle; ?>">
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

                                                <a href="view_file2.php?id=<?php echo $row['id']; ?>" class="w3-button w3-red" target="_blank">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- Pagination links -->
                            <div class="w3-margin-top">
                                <?php if ($current_page > 1) : ?>
                                    <a href="?page=<?php echo ($current_page - 1); ?>&search=<?php echo $searchTitle; ?>" class="w3-button w3-black">Previous</a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo $searchTitle; ?>" class="w3-button <?php echo ($current_page == $i) ? 'w3-blue' : 'w3-black'; ?>"><?php echo $i; ?></a>
                                <?php endfor; ?>

                                <?php if ($current_page < $totalPages) : ?>
                                    <a href="?page=<?php echo ($current_page + 1); ?>&search=<?php echo $searchTitle; ?>" class="w3-button w3-black">Next</a>
                                <?php endif; ?>
                            </div>
                            <br>
                            <style>
                                /* Add this style in the head section of your HTML or in your CSS file */
                                a.back-link {
                                    display: inline-block;
                                    padding: 10px 20px;
                                    background-color: #4CAF50;
                                    /* Green color, you can change it */
                                    color: white;
                                    text-decoration: none;
                                    border-radius: 5px;
                                    transition: background-color 0.3s;
                                }

                                a.back-link:hover {
                                    background-color: #45a049;
                                    /* Darker shade on hover, you can change it */
                                }
                            </style>

                            <!-- Add this link wherever you want the "Back" link to appear -->
                            <a href="archivelist.php" class="back-link">Back</a>
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