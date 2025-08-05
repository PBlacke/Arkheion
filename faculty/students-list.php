<?php
session_start();
require '../config/connection.php';
require_once '../config/auth.php';
require '../includes/database.php';
require '../includes/validators.php';

requireRole(['admin', 'faculty']);

// Handle student actions (if needed - like deactivating accounts)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // CSRF protection
        if (!SecurityValidator::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception("Invalid request. Please try again.");
        }

        $action = $_POST['action'] ?? '';
        $student_id = (int)($_POST['student_id'] ?? 0);

        if (!$student_id || !in_array($action, ['activate', 'deactivate'])) {
            throw new Exception("Invalid action or student ID.");
        }

        // Get the student data
        $student = $db->getStudent($student_id);
        if (!$student) {
            throw new Exception("Student not found.");
        }

        // Update user status
        $new_status = ($action === 'activate') ? 'active' : 'inactive';
        $db->update(
            'users',
            ['status' => $new_status],
            ['id' => $student['user_id']]
        );

        $action_text = ($action === 'activate') ? 'activated' : 'deactivated';
        $_SESSION['success_message'] = "Student account {$action_text} successfully!";

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get all students
$students = $db->getStudents();

// Get messages from session and clear them
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Generate CSRF token for the form
$csrf_token = SecurityValidator::generateCSRFToken();
?>

<!DOCTYPE html>
<html data-theme="ark">

<head>
    <title>Students List - Arkheion</title>
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen p-4 bg-base-200">
        <div class="grid grid-cols-dashboard gap-4 w-full">
            <?php include 'includes/nav.php'; ?>

            <div class="flex flex-col gap-4 p-8 bg-base-100 rounded-box shadow-lg">
                <div class="flex justify-between items-center w-full">
                    <h1 class="text-2xl font-bold">Students List</h1>
                    <div class="flex gap-2 items-center">
                        <div class="text-sm text-base-content/70">
                            <?php echo count($students); ?> total students
                        </div>
                        <!-- Optional: Add filter buttons -->
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-outline btn-sm">Filter</label>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                <li><a href="?filter=all">All Students</a></li>
                                <li><a href="?filter=active">Active Only</a></li>
                                <li><a href="?filter=inactive">Inactive Only</a></li>
                            </ul>
                        </div>
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

                <?php if (empty($students)): ?>
                    <div class="alert alert-info">
                        <span>No students found in the system.</span>
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
                                    <th>Birthdate</th>
                                    <th>Educational Attainment</th>
                                    <th>Department</th>
                                    <th>Account Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr class="<?php echo $student['account_status'] === 'inactive' ? 'opacity-60' : ''; ?>">
                                        <td><?php echo $student['id']; ?></td>
                                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                                        <td>
                                            <?php
                                            $fullName = trim(
                                                htmlspecialchars($student['first_name']) . ' ' .
                                                    htmlspecialchars($student['middle_name']) . ' ' .
                                                    htmlspecialchars($student['last_name']) . ' ' .
                                                    htmlspecialchars($student['suffix'])
                                            );
                                            echo $fullName;
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($student['birthdate'])); ?></td>
                                        <td><?php echo htmlspecialchars($student['educational_attainment']); ?></td>
                                        <td><?php echo htmlspecialchars($student['department_name']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $student['account_status'] === 'active' ? 'badge-success' : 'badge-error'; ?>">
                                                <?php echo ucfirst($student['account_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="flex gap-2">
                                                <!-- Toggle Status Button -->
                                                <?php if ($student['account_status'] === 'active'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <input type="hidden" name="action" value="deactivate">
                                                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                        <button type="submit"
                                                            class="btn btn-warning btn-sm"
                                                            onclick="return confirm('Are you sure you want to deactivate this student account?')">
                                                            Deactivate
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <input type="hidden" name="action" value="activate">
                                                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                        <button type="submit"
                                                            class="btn btn-success btn-sm"
                                                            onclick="return confirm('Are you sure you want to activate this student account?')">
                                                            Activate
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- View Details Button -->
                                                <label for="details_modal_<?php echo $student['id']; ?>" class="btn btn-info btn-sm">
                                                    Details
                                                </label>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Details Modal for each student -->
                                    <input type="checkbox" id="details_modal_<?php echo $student['id']; ?>" class="modal-toggle" />
                                    <div class="modal" role="dialog">
                                        <div class="modal-box">
                                            <h3 class="text-lg font-bold">Student Details</h3>
                                            <div class="py-4 space-y-2">
                                                <p><strong>Student ID:</strong> <?php echo $student['id']; ?></p>
                                                <p><strong>Username:</strong> <?php echo htmlspecialchars($student['username']); ?></p>
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                                                <p><strong>Full Name:</strong> <?php echo $fullName; ?></p>
                                                <p><strong>Birthdate:</strong> <?php echo date('F j, Y', strtotime($student['birthdate'])); ?></p>
                                                <p><strong>Age:</strong>
                                                    <?php
                                                    $birthdate = new DateTime($student['birthdate']);
                                                    $today = new DateTime();
                                                    $age = $today->diff($birthdate)->y;
                                                    echo $age . ' years old';
                                                    ?>
                                                </p>
                                                <p><strong>Address:</strong> <?php echo htmlspecialchars($student['address']); ?></p>
                                                <p><strong>Educational Attainment:</strong> <?php echo htmlspecialchars($student['educational_attainment']); ?></p>
                                                <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department_name']); ?></p>
                                                <p><strong>Account Status:</strong>
                                                    <span class="badge <?php echo $student['account_status'] === 'active' ? 'badge-success' : 'badge-error'; ?>">
                                                        <?php echo ucfirst($student['account_status']); ?>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="modal-action">
                                                <label for="details_modal_<?php echo $student['id']; ?>" class="btn">Close</label>
                                            </div>
                                        </div>
                                        <label class="modal-backdrop" for="details_modal_<?php echo $student['id']; ?>">Close</label>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Statistics Card (Optional) -->
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Total Students</div>
                        <div class="stat-value"><?php echo count($students); ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-title">Active Students</div>
                        <div class="stat-value text-success">
                            <?php echo count(array_filter($students, fn($s) => $s['account_status'] === 'active')); ?>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-title">Inactive Students</div>
                        <div class="stat-value text-error">
                            <?php echo count(array_filter($students, fn($s) => $s['account_status'] === 'inactive')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>