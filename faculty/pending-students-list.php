<?php
session_start();
require '../config/connection.php';
require_once '../config/auth.php';
require '../includes/database.php';
require '../includes/validators.php';

requireRole(['admin', 'faculty']);

// Handle approval/rejection actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // CSRF protection
        if (!SecurityValidator::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception("Invalid request. Please try again.");
        }

        $action = $_POST['action'] ?? '';
        $student_id = (int)($_POST['student_id'] ?? 0);

        if (!$student_id || !in_array($action, ['approve', 'reject'])) {
            throw new Exception("Invalid action or student ID.");
        }

        // Get the pending student data
        $pending_student = $db->fetchById('pending_students', $student_id);
        if (!$pending_student) {
            throw new Exception("Pending student not found.");
        }

        // Check if already processed
        if ($pending_student['status'] !== 'Pending') {
            throw new Exception("This student registration has already been processed.");
        }

        if ($action === 'approve') {
            // Begin transaction for approval process
            $conn->begin_transaction();

            try {
                // Create user account (addUser will detect password is already hashed)
                $user_id = $db->addUser(
                    $pending_student['username'],
                    $pending_student['email'],
                    $pending_student['password'], // Already hashed from registration
                    'student',
                    'active'
                );

                // Create student record
                $student_id = $db->insert('students', [
                    'user_id' => $user_id,
                    'first_name' => $pending_student['first_name'],
                    'middle_name' => $pending_student['middle_name'],
                    'last_name' => $pending_student['last_name'],
                    'suffix' => $pending_student['suffix'],
                    'birthdate' => $pending_student['birthdate'],
                    'address' => $pending_student['address'],
                    'educational_attainment' => $pending_student['educational_attainment'],
                    'department_id' => $pending_student['department_id']
                ]);

                // Update pending student status
                $db->update(
                    'pending_students',
                    ['status' => 'Approved'],
                    ['id' => $pending_student['id']]
                );

                // Optional: Create notification for the student
                $db->insert('notifications', [
                    'recipient_id' => $user_id,
                    'title' => 'Registration Approved',
                    'message' => 'Your student registration has been approved. You can now log in to the system.',
                    'type' => 'account_approved',
                    'priority' => 'normal'
                ]);

                $conn->commit();
                $_SESSION['success_message'] = "Student registration approved successfully!";
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        } elseif ($action === 'reject') {
            // Update pending student status to rejected
            $db->update(
                'pending_students',
                ['status' => 'Rejected'],
                ['id' => $pending_student['id']]
            );

            $_SESSION['success_message'] = "Student registration rejected.";
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get messages from session and clear them
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Get pending students (only those with 'Pending' status)
$pending_students = $db->getPendingStudents('Pending');

// Generate CSRF token
$csrf_token = SecurityValidator::generateCSRFToken();
?>

<!DOCTYPE html>
<html data-theme="ark">

<head>
    <title>Pending Students - Arkheion</title>
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen p-4 bg-base-200">
        <div class="grid grid-cols-dashboard gap-4 w-full">
            <?php include 'includes/nav.php'; ?>

            <div class="flex flex-col gap-4 p-8 bg-base-100 rounded-box shadow-lg">
                <div class="flex justify-between items-center w-full">
                    <h1 class="text-2xl font-bold">Pending Students List</h1>
                    <div class="text-sm text-base-content/70">
                        <?php echo count($pending_students); ?> pending registrations
                    </div>
                </div>

                <!-- Success Message -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <span><?php echo htmlspecialchars($success_message); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <span><?php echo htmlspecialchars($error_message); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (empty($pending_students)): ?>
                    <div class="alert alert-info">
                        <span>No pending student registrations at this time.</span>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Educational Attainment</th>
                                    <th>Department</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_students as $pending_student): ?>
                                    <?php $department = $db->getDepartment($pending_student['department_id']); ?>
                                    <tr>
                                        <td><?php echo $pending_student['id']; ?></td>
                                        <td><?php echo htmlspecialchars($pending_student['username']); ?></td>
                                        <td>
                                            <?php
                                            $fullName = trim(
                                                htmlspecialchars($pending_student['first_name']) . ' ' .
                                                    htmlspecialchars($pending_student['middle_name']) . ' ' .
                                                    htmlspecialchars($pending_student['last_name']) . ' ' .
                                                    htmlspecialchars($pending_student['suffix'])
                                            );
                                            echo $fullName;
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($pending_student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($pending_student['educational_attainment']); ?></td>
                                        <td><?php echo htmlspecialchars($department['department_name'] ?? 'Unknown'); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($pending_student['registration_date'])); ?></td>
                                        <td>
                                            <div class="flex gap-2">
                                                <!-- Approve Button -->
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="student_id" value="<?php echo $pending_student['id']; ?>">
                                                    <button type="submit"
                                                        class="btn btn-success btn-sm"
                                                        onclick="return confirm('Are you sure you want to approve this student registration?')">
                                                        Approve
                                                    </button>
                                                </form>

                                                <!-- Reject Button -->
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="student_id" value="<?php echo $pending_student['id']; ?>">
                                                    <button type="submit"
                                                        class="btn btn-error btn-sm"
                                                        onclick="return confirm('Are you sure you want to reject this student registration?')">
                                                        Reject
                                                    </button>
                                                </form>

                                                <!-- View Details Button -->
                                                <label for="details_modal_<?php echo $pending_student['id']; ?>" class="btn btn-info btn-sm">
                                                    Details
                                                </label>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Details Modal for each student -->
                                    <input type="checkbox" id="details_modal_<?php echo $pending_student['id']; ?>" class="modal-toggle" />
                                    <div class="modal" role="dialog">
                                        <div class="modal-box">
                                            <h3 class="text-lg font-bold">Student Details</h3>
                                            <div class="py-4 space-y-2">
                                                <p><strong>Username:</strong> <?php echo htmlspecialchars($pending_student['username']); ?></p>
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($pending_student['email']); ?></p>
                                                <p><strong>Full Name:</strong> <?php echo $fullName; ?></p>
                                                <p><strong>Birthdate:</strong> <?php echo date('F j, Y', strtotime($pending_student['birthdate'])); ?></p>
                                                <p><strong>Address:</strong> <?php echo htmlspecialchars($pending_student['address']); ?></p>
                                                <p><strong>Educational Attainment:</strong> <?php echo htmlspecialchars($pending_student['educational_attainment']); ?></p>
                                                <p><strong>Department:</strong> <?php echo htmlspecialchars($department['department_name'] ?? 'Unknown'); ?></p>
                                                <p><strong>Registration Date:</strong> <?php echo date('F j, Y g:i A', strtotime($pending_student['registration_date'])); ?></p>
                                            </div>
                                            <div class="modal-action">
                                                <label for="details_modal_<?php echo $pending_student['id']; ?>" class="btn">Close</label>
                                            </div>
                                        </div>
                                        <label class="modal-backdrop" for="details_modal_<?php echo $pending_student['id']; ?>">Close</label>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>