<?php
session_start();
require_once '../config/auth.php';
?>

<!DOCTYPE html>
<html data-theme="ark">

<head>
    <title>Arkheion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen p-4 bg-base-200">
        <div class="grid grid-cols-dashboard gap-4 w-full">
            <?php include 'includes/nav.php'; ?>

            <div class="flex flex-col gap-4 p-8 bg-base-100 rounded-box shadow-lg">
                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2 style="color: red;" class="w3-padding-16"><i class="fa fa-dashboard fa-fw"></i>Dashboard</h2>
                    <div class="w3-container">
                        <h5 class="w3-opacity"><b>Welcome to Arkheion Online Archiving Management System</b></h5>
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

                        <div class="clickable-box" onclick="location.href='fac_fileadd.php';">
                            <h5><b>Add New</b></h5>
                        </div>

                        <!-- Add the search bar with search icon to the right -->
                        <div class="search-container">
                            <form action="fac_dashsearch.php" method="GET">
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
                                        <th>PROGRAM</th>
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
                                                <a href="edit6.php?id=<?php echo $row['id']; ?>" class="w3-button w3-blue">
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

        <!-- <footer class="w3-container w3-red w3-center w3-margin-top">
  <p>Copyright © 2023. All rights reserved.</p>
  <p>Arkheion ONLINE ARCHIVING SYSTEM</p>
</footer> -->

        <!-- Modal -->
        <div class="modal fade" id="pendingStudentsModal" tabindex="-1" aria-labelledby="pendingStudentsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pendingStudentsModalLabel">Pending Student Registrations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php
                        // Get pending students for the current department
                        $pending_query = $conn->prepare("SELECT * FROM pending_students WHERE department = ? AND status = 'Pending' ORDER BY registration_date DESC");
                        $pending_query->bind_param("s", $_SESSION['department']);
                        $pending_query->execute();
                        $pending_result = $pending_query->get_result();

                        if ($pending_result->num_rows > 0) {
                            echo '<div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Educational Attainment</th>
                                        <th>Registration Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';

                            while ($student = $pending_result->fetch_assoc()) {
                                $full_name = $student['first_name'] . ' ' .
                                    ($student['middle_name'] ? $student['middle_name'] . ' ' : '') .
                                    $student['last_name'] .
                                    ($student['suffix'] ? ' ' . $student['suffix'] : '');

                                echo '<tr>
                                <td>' . htmlspecialchars($full_name) . '</td>
                                <td>' . htmlspecialchars($student['email']) . '</td>
                                <td>' . htmlspecialchars($student['educational_attainment']) . '</td>
                                <td>' . date('M d, Y', strtotime($student['registration_date'])) . '</td>
                                <td>
                                    <button class="btn btn-sm btn-success approve-student" data-id="' . $student['id'] . '">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-student" data-id="' . $student['id'] . '">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </td>
                            </tr>';
                            }

                            echo '</tbody></table></div>';
                        } else {
                            echo '<div class="alert alert-info">No pending student registrations.</div>';
                        }
                        $pending_query->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

        <script>
            $(document).ready(function() {
                // Handle approve/reject buttons
                $('.approve-student, .reject-student').click(function() {
                    const studentId = $(this).data('id');
                    const action = $(this).hasClass('approve-student') ? 'approve' : 'reject';
                    const actionText = action === 'approve' ? 'approve' : 'reject';

                    if (confirm(`Are you sure you want to ${actionText} this student?`)) {
                        $.ajax({
                            url: 'process_student_approval.php',
                            method: 'POST',
                            data: {
                                student_id: studentId,
                                action: action
                            },
                            success: function(response) {
                                try {
                                    const result = JSON.parse(response);
                                    if (result.success) {
                                        alert(`Student has been ${actionText}ed successfully.`);
                                        location.reload(); // Refresh the page to update the list
                                    } else {
                                        alert('Error: ' + result.message);
                                    }
                                } catch (e) {
                                    alert('An error occurred while processing the response.');
                                }
                            },
                            error: function() {
                                alert('An error occurred. Please try again.');
                            }
                        });
                    }
                });
            });
        </script>

</body>

</html>