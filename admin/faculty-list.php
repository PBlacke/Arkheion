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

        // Rate limiting (optional)
        if (!SecurityValidator::checkRateLimit($_SERVER['REMOTE_ADDR'] . '_faculty_add', 3, 300)) {
            throw new Exception("Too many attempts. Please wait 5 minutes before trying again.");
        }

        // Initialize validators
        $validator = new FormValidator($_POST);
        $dbValidator = new DatabaseValidator($db);

        // Chain validation methods - only validate format/rules, not required fields
        $validator->validateUsername('username')
            ->validateEmail('email')
            ->validatePassword('password')
            ->validateName('first_name', 'First name')
            ->validateName('middle_name', 'Middle name')
            ->validateName('last_name', 'Last name')
            ->validateName('suffix', 'Suffix')
            ->validateDate('birthdate')
            ->validateLength('address', 10, 255, 'Address')
            ->validatePhone('phone')
            ->validateNumeric('department', 'Department')
            ->validateLength('position', 2, 100, 'Position')
            ->validateLength('specialization', 2, 255, 'Specialization');

        // Check if basic validation passed
        if (!$validator->isValid()) {
            throw new Exception($validator->getErrorsAsString());
        }

        // Get sanitized data
        $data = $validator->getSanitizedData();

        // Database-specific validations
        if (!$dbValidator->isEmailUnique($data['email'])) {
            throw new Exception("Email address is already registered");
        }

        if (!$dbValidator->isUsernameUnique($data['username'])) {
            throw new Exception("Username is already taken");
        }

        if (!$dbValidator->departmentExists($data['department'])) {
            throw new Exception("Selected department is not valid");
        }

        // If validation passes, process the form
        $new_user_id = $db->addUser(
            $data['username'],
            $data['email'],
            $data['password']
        );

        $faculty_id = $db->addFaculty(
            $new_user_id,
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['suffix'] ?? null,
            $data['birthdate'],
            $data['address'],
            $data['phone'],
            $data['department'],
            $data['position'] ?? null,
            $data['specialization'] ?? null,
            date('Y-m-d')
        );

        $_SESSION['success_message'] = "Faculty member added successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$faculties = $db->getFaculty();
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
    <title>Staff List - Arkheion</title>
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen p-4 bg-base-200">
        <div class="grid grid-cols-dashboard gap-4 w-full">
            <?php include './nav.php'; ?>

            <div class="flex flex-col gap-4 p-8 bg-base-100">
                <div class="flex justify-between items-center w-full">
                    <h1 class="text-2xl font-bold">Faculty List</h1>
                    <label for="add_faculty_modal" class="btn btn-primary">Add Faculty</label>
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
                        <span><?php echo $error_message; ?></span>
                    </div>
                <?php endif; ?>

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Suffix</th>
                                <th>Birthdate</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Department</th>
                                <th>Hire Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($faculties as $faculty) {
                                $department = $db->getDepartment($faculty['department_id']);

                                echo "<tr>";
                                echo "<td>{$faculty['id']}</td>";
                                echo "<td>" . htmlspecialchars($faculty['first_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($faculty['middle_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($faculty['last_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($faculty['suffix']) . "</td>";
                                echo "<td>{$faculty['birthdate']}</td>";
                                echo "<td>" . htmlspecialchars($faculty['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($faculty['phone']) . "</td>";
                                echo "<td>" . htmlspecialchars($department['department_name']) . "</td>";
                                echo "<td>{$faculty['hire_date']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <input type="checkbox" id="add_faculty_modal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <form action="" method="POST" class="flex flex-col gap-2 modal-box">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <h2 class="text-lg font-bold">Add Faculty</h2>
            <input type="text" name="username" class="input w-full" placeholder="Username" required>
            <input type="email" name="email" class="input w-full" placeholder="Email" required>
            <input type="password" name="password" class="input w-full" placeholder="Password" required>

            <p class="text-sm mt-2 ml-2">User Information</p>
            <div class="grid grid-cols-2 gap-2 w-full">
                <input type="text" name="first_name" class="input" placeholder="First Name" required>
                <input type="text" name="middle_name" class="input" placeholder="Middle Name">
            </div>
            <div class="grid grid-cols-2 gap-2 w-full">
                <input type="text" name="last_name" class="input" placeholder="Last Name" required>
                <label class="input">
                    <input type="text" name="suffix" class="grow" placeholder="Ph.D" />
                    <span class="badge badge-neutral badge-xs">Optional</span>
                </label>
            </div>
            <input type="text" name="address" class="input w-full" placeholder="Address" required>
            <div class="grid grid-cols-2 gap-2 w-full">
                <label class="input">
                    <span class="label">Birthdate</span>
                    <input name="birthdate" type="date" required />
                </label>
                <input type="text" name="phone" class="input" placeholder="Phone" required>
            </div>
            <div class="grid grid-cols-2 gap-2 w-full">
                <select name="department" class="select w-full" required>
                    <option value="">Select Department</option>
                    <?php
                    foreach ($departments as $department) {
                        echo "<option value=\"{$department['id']}\">" . htmlspecialchars($department['department_name']) . "</option>";
                    }
                    ?>
                </select>
                <input type="text" name="position" class="input w-full" placeholder="Position">
            </div>
            <input type="text" name="specialization" class="input w-full" placeholder="Specialization">
            <button type="submit" class="btn btn-primary">Submit Faculty</button>
        </form>
        <label class="modal-backdrop" for="add_faculty_modal">Close</label>
    </div>
</body>

</html>