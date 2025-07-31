<?php
session_start();
require '../config/connection.php';
require_once '../config/auth.php';
require '../includes/database.php';
require '../includes/validators.php'; // Include the validator file

requireRole(['admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // CSRF protection
        if (!SecurityValidator::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception("Invalid request. Please try again.");
        }

        // Initialize validators
        $validator = new FormValidator($_POST);

        // Chain validation methods - only validate format/rules, not required fields
        $validator->validateName('department_code', 'Department Code')
            ->validateName('department_name', 'Department Name')
            ->validateCustom('description', function ($value) {
                return strlen($value) <= 255;
            }, 'Description must be 255 characters or less')
            ->validateName('status', 'Status');

        // Check if basic validation passed
        if (!$validator->isValid()) {
            throw new Exception($validator->getErrorsAsString());
        }

        // Get sanitized data
        $data = $validator->getSanitizedData();

        // Process the form
        $new_user_id = $db->addDepartment(
            $_POST['department_code'],
            $_POST['department_name'],
            $_POST['description'],
            !empty($_POST['head_faculty_id']) ? $_POST['head_faculty_id'] : null,
            $_POST['status']
        );

        // Set success message in session
        $_SESSION['success_message'] = "Faculty member added successfully!";

        // Redirect to prevent resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        // Set error message in session
        $_SESSION['error_message'] = "Error adding faculty: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$departments = $db->getDepartments();

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
    <title>Department List - Arkheion</title>
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen p-4 bg-base-200">
        <div class="grid grid-cols-dashboard gap-4 w-full">
            <?php include 'includes/nav.php'; ?>


            <div class="flex flex-col gap-4 p-8 bg-base-100 rounded-box shadow-lg">
                <div class="flex justify-between items-center w-full">
                    <h1 class="text-2xl font-bold">Department List</h1>
                    <label for="add_department_modal" class="btn btn-primary">Add Faculty</label>
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

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Head Faculty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($departments as $department) {
                                echo "<tr>";
                                echo "<td>{$department['id']}</td>";
                                echo "<td>" . htmlspecialchars($department['department_code']) . "</td>";
                                echo "<td>" . htmlspecialchars($department['department_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($department['description']) . "</td>";
                                echo "<td>" . htmlspecialchars($department['head_faculty_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($department['status']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <input type="checkbox" id="add_department_modal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <form action="" method="POST" class="flex flex-col gap-2 modal-box">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <h2 class="text-lg font-bold">Add Faculty</h2>
            <div class="grid grid-cols-2 gap-2 w-full">
                <input type="text" name="department_code" class="input w-full" placeholder="Code" required>
                <input type="text" name="department_name" class="input w-full" placeholder="Name" required>
            </div>
            <input type="text" name="description" class="input w-full" placeholder="Description" required>
            <div class="grid grid-cols-2 gap-2 w-full">
                <select name="head_faculty_id" class="select w-full">
                    <option value="" selected>Faculty Head (optional)</option>
                    <?php
                    $faculties = $db->getFacultyByStatus(["active", "on_leave"]);

                    foreach ($faculties as $faculty) {
                        echo "<option value=\"{$faculty['id']}\">" . htmlspecialchars($faculty['first_name']) . "</option>";
                    }
                    ?>
                </select>
                <select name="status" class="select w-full" required>
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit Faculty</button>
        </form>
        <label class="modal-backdrop" for="add_department_modal">Close</label>
    </div>
</body>

</html>