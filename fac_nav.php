<?php
// Include the connection file
require 'connection.php';

// Check if the user is logged in
if (isset($_SESSION['employee_id'])) {
    // Retrieve user information based on the stored employee_id
    $employeeId = $_SESSION['employee_id'];
    $stmt = $conn->prepare("SELECT username, email FROM faculty WHERE id = ?");
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['username'];
        $email = $row['email'];
    } else {
        $username = "Default Username"; 
        $email = "Default Email"; 
    }

    $stmt->close();
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: facultylogin.php");
    exit();
}

// Get pending students for the modal
$pending_students_query = "SELECT * FROM pending_students WHERE department = ? AND status = 'Pending' ORDER BY id DESC";
$pending_stmt = $conn->prepare($pending_students_query);
$pending_stmt->bind_param("s", $_SESSION['department']);
$pending_stmt->execute();
$pending_result = $pending_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Add this to your existing styles */
        .list {
            position: relative;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: black;
            padding: 5px;
            transition: all 0.3s ease;
        }

        .list i {
            margin-right: 8px;
        }

        .w3-badge {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            padding: 2px 6px;
            border-radius: 50%;
            font-size: 12px;
        }

        /* Change hover style to target the anchor tag */
        a.list:hover {
            background-color: #6b7b9e;
            color: black;
            padding: 5px;
            border-radius: 10px;
            text-decoration: none;
        }

        /* Add style for the paragraph inside the link */
        .list p {
            margin: 0;
            padding: 0;
            flex-grow: 1;
        }

        /* Modal styles */
        .modal-header {
            background-color: #6b7b9e;
            color: white;
        }

        .btn-approve {
            background-color: #28a745;
            color: white;
        }

        .btn-reject {
            background-color: #dc3545;
            color: white;
        }

        .student-info {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .student-info:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<div class="w3-white w3-text-grey w3-card-4">
    <div class="w3-display-container">
        <div class="w3-display-bottomleft w3-container w3-text-black">
        </div>
    </div>
    <div class="w3-container">
        <p><i class="fa fa-briefcase fa-fw w3-margin-right w3-large w3-text-blue"></i><?php echo $username; ?></p>
        <p><i class="fa fa-home fa-fw w3-margin-right w3-large w3-text-blue"></i>Arkheion</p>
        <p><i class="fa fa-envelope fa-fw w3-margin-right w3-large w3-text-blue"></i><?php echo $email; ?></p>
        <hr>

        <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-blue"></i>Menu</b></p>

        <style>
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }

            .loading-spinner {
                border: 8px solid #f3f3f3;
                border-top: 8px solid #3498db;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>

        <script>
            function showLoading(url) {
                // Create loading overlay
                var overlay = document.createElement('div');
                overlay.className = 'loading-overlay';
                
                var spinner = document.createElement('div');
                spinner.className = 'loading-spinner';
                
                overlay.appendChild(spinner);
                document.body.appendChild(overlay);
                
                // Redirect after a short delay
                setTimeout(function() {
                    window.location.href = url;
                }, 500);
            }

            // Function to handle student approval/rejection
            function handleStudent(studentId, action) {
                // Show loading overlay
                var overlay = document.createElement('div');
                overlay.className = 'loading-overlay';
                document.body.appendChild(overlay);

                // Send AJAX request
                $.ajax({
                    url: 'process_student_approval.php',
                    type: 'POST',
                    data: {
                        student_id: studentId,
                        action: action
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            // Remove the student card from the modal
                            $('#student-' + studentId).fadeOut(300, function() {
                                $(this).remove();
                                // If no more students, update the modal
                                if ($('.student-info').length === 0) {
                                    $('#pendingStudentsModal .modal-body').html('<p class="text-center">No pending students.</p>');
                                }
                                // Update the badge count
                                var currentCount = parseInt($('#pendingCount').text());
                                if (currentCount > 0) {
                                    $('#pendingCount').text(currentCount - 1);
                                    if (currentCount - 1 === 0) {
                                        $('#pendingCount').hide();
                                    }
                                }
                            });
                            // Show success message
                            alert(action === 'approve' ? 'Student approved successfully!' : 'Student rejected successfully!');
                        } else {
                            alert('Error: ' + result.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    },
                    complete: function() {
                        // Remove loading overlay
                        overlay.remove();
                    }
                });
            }
        </script>

        <!-- Navigation Links -->
        <a href="javascript:void(0);" class="list" onclick="showLoading('fac_dash.php')">
            <i class="fa fa-dashboard fa-fw"></i>
            <p>Dashboard</p>
        </a>
        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

        <a href="javascript:void(0);" class="list" onclick="showLoading('fac_archlist.php')">
            <i class="fa fa-archive fa-fw"></i>
            <p>Archive List</p>
        </a>
        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

        <a href="javascript:void(0);" class="list" onclick="showLoading('fac_published.php')">
            <i class="fa fa-check-circle fa-fw"></i>
            <p>Published Files</p>
        </a>
        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

        <a href="#" class="list" data-bs-toggle="modal" data-bs-target="#pendingStudentsModal">
            <i class="fa fa-user-clock fa-fw"></i>
            <p>Pending Students</p>
            <?php
            // Get count of pending students in the same department
            $pending_count = $pending_result->num_rows;
            if ($pending_count > 0) {
                echo '<span class="w3-badge w3-red" id="pendingCount">' . $pending_count . '</span>';
            }
            ?>
        </a>
        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

        <br>

        <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-blue"></i>Settings</b></p>

        <a href="javascript:void(0);" class="list" onclick="showLoading('fac_settings.php')">
            <i class="fa fa-cogs fa-fw"></i>
            <p>Account Settings</p>
        </a>
        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

        <br>

        <a href="javascript:void(0);" class="list" onclick="showLoading('logout.php')">
            <i class="fa fa-sign-out fa-fw"></i>
            <p>Logout</p>
        </a>
        <div class="w3-light-grey w3-round-xlarge w3-small"></div>

        <br>
    </div>
</div>

<!-- Pending Students Modal -->
<div class="modal fade" id="pendingStudentsModal" tabindex="-1" aria-labelledby="pendingStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingStudentsModalLabel">Pending Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($pending_result->num_rows > 0): ?>
                    <?php while ($student = $pending_result->fetch_assoc()): ?>
                        <div class="student-info" id="student-<?php echo $student['id']; ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'] . ' ' . $student['suffix']); ?></h5>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                                    <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?></p>
                                    <p><strong>Educational Attainment:</strong> <?php echo htmlspecialchars($student['educational_attainment']); ?></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-approve btn-sm" onclick="handleStudent(<?php echo $student['id']; ?>, 'approve')">
                                        <i class="fa fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-reject btn-sm" onclick="handleStudent(<?php echo $student['id']; ?>, 'reject')">
                                        <i class="fa fa-times"></i> Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No pending students.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $pending_stmt->close(); ?> 