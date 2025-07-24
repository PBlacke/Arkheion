<?php
session_start();

// Include the connection file
require 'config/connection.php';

// Check if user is logged in and session is valid
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['username']) || !isset($_SESSION['department'])) {
    header("Location: facultylogin.php");
    exit();
}

// Check for session timeout (30 minutes)
$timeout = 30 * 60;
if (time() - $_SESSION['last_activity'] > $timeout) {
    session_unset();
    session_destroy();
    header("Location: facultylogin.php?timeout=1");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Define the number of records per page
$recordsPerPage = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $recordsPerPage;

// Define the status you want to display (Published)
$statusToDisplay = 'Published';

try {
    // Query to fetch data from the database with pagination and status filter
    $query = "SELECT * FROM files WHERE status = ? AND department = ? ORDER BY created_at DESC LIMIT ?, ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        throw new Exception("Error preparing query: " . $conn->error);
    }

    $stmt->bind_param("ssii", $statusToDisplay, $_SESSION['department'], $offset, $recordsPerPage);
    $stmt->execute();
    $result = $stmt->get_result();

    // Query to get the total number of records for pagination
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM files WHERE status = ? AND department = ?";
    $totalStmt = $conn->prepare($totalRecordsQuery);

    if ($totalStmt === false) {
        throw new Exception("Error preparing count query: " . $conn->error);
    }

    $totalStmt->bind_param("ss", $statusToDisplay, $_SESSION['department']);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRecords = $totalResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Published Files - Arkheion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

        .search-container {
            margin: 20px 0;
        }

        .search-container input[type=text] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-container button {
            padding: 8px 15px;
            background: maroon;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-container button:hover {
            background: #600;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .btn-view {
            background-color: maroon;
            color: white;
        }

        .btn-view:hover {
            background-color: #600;
            color: white;
        }

        .pagination {
            margin-top: 20px;
        }

        .page-link {
            color: maroon;
        }

        .page-item.active .page-link {
            background-color: maroon;
            border-color: maroon;
        }

        /* Fix for navigation icons */
        .fa,
        .fas {
            font-family: "Font Awesome 5 Free" !important;
            font-weight: 900;
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
                <?php include 'fac_nav.php'; ?>
            </div>

            <!-- Right Column -->
            <div class="w3-twothird">
                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2 class="w3-text-grey w3-padding-16">
                        <i class="fa fa-check-circle fa-fw w3-margin-right w3-xxlarge" style="color: maroon;"></i>
                        Published Files
                    </h2>

                    <!-- Search Container -->
                    <div class="search-container">
                        <form action="fac_published_search.php" method="GET" class="d-flex">
                            <input type="text" placeholder="Search files..." name="search" class="form-control me-2">
                            <button type="submit" class="btn btn-view">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Table Container -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Uploader</th>
                                    <th>Department</th>
                                    <th>Upload Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['uploader']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                                        echo "<td>" . date('M d, Y', strtotime($row['upload_date'])) . "</td>";
                                        echo "<td>
                                            <a href='view_file.php?id=" . $row['id'] . "' class='btn btn-sm btn-view'>
                                                <i class='fa fa-eye'></i> View
                                            </a>
                                          </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No published files found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($current_page - 1); ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($current_page + 1); ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</body>

</html>

<?php
// Close database connections
if (isset($stmt)) $stmt->close();
if (isset($totalStmt)) $totalStmt->close();
$conn->close();
?>