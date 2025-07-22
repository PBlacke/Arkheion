<?php
// Start the session at the very beginning
session_start();

// Include the connection file
require 'connection.php';

// Check if the user is logged in and session is valid
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['username']) || !isset($_SESSION['last_activity'])) {
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

// Get search term
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Define the number of records per page
$recordsPerPage = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $recordsPerPage;

// Prepare the search query with status filter
$statusToDisplay = 'Published';
$searchTerm = "%{$search}%";

$query = "SELECT * FROM files 
          WHERE status = ? 
          AND department = ?
          AND (title LIKE ? OR uploader LIKE ? OR description LIKE ?)
          ORDER BY upload_date DESC 
          LIMIT ?, ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("sssssii", $statusToDisplay, $_SESSION['department'], $searchTerm, $searchTerm, $searchTerm, $offset, $recordsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Get total records for pagination
$totalQuery = "SELECT COUNT(*) as total FROM files 
               WHERE status = ? 
               AND department = ?
               AND (title LIKE ? OR uploader LIKE ? OR description LIKE ?)";

$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param("sssss", $statusToDisplay, $_SESSION['department'], $searchTerm, $searchTerm, $searchTerm);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results - Arkheion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
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
        .search-summary {
            margin: 20px 0;
            color: #666;
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
                    <i class="fa fa-search fa-fw w3-margin-right w3-xxlarge" style="color: maroon;"></i>
                    Search Results
                </h2>

                <!-- Search Container -->
                <div class="search-container">
                    <form action="fac_published_search.php" method="GET" class="d-flex">
                        <input type="text" placeholder="Search files..." name="search" class="form-control me-2" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-view">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Search Summary -->
                <div class="search-summary">
                    <?php
                    if ($search) {
                        echo "<p>Found " . $totalRecords . " result" . ($totalRecords != 1 ? "s" : "") . " for '" . htmlspecialchars($search) . "'</p>";
                    }
                    ?>
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
                            if ($result->num_rows > 0) {
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
                                echo "<tr><td colspan='5' class='text-center'>No results found</td></tr>";
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
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo ($current_page - 1); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo ($current_page + 1); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <!-- Back to Published Files -->
                <div class="text-center mb-4">
                    <a href="fac_published.php" class="btn btn-view">
                        <i class="fa fa-arrow-left"></i> Back to Published Files
                    </a>
                </div>
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
$stmt->close();
$totalStmt->close();
$conn->close();
?>