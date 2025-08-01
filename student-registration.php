<?php
// Include required files
require 'config/connection.php';
require 'includes/database.php';
require 'includes/validators.php';

session_start();

$message = '';
$message_type = '';
$dbValidator = new DatabaseValidator($db);
$securityValidator = new SecurityValidator();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // CSRF protection
        if (!SecurityValidator::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception("Invalid request. Please try again.");
        }

        // Initialize validators
        $validator = new FormValidator($_POST);
        $dbValidator = new DatabaseValidator($db);

        // Validate all fields using the validator
        $validator
            ->validateUsername('username')
            ->validateEmail('email')
            ->validatePassword('password')
            ->validateName('first_name', 'First name')
            ->validateName('middle_name', 'Middle name')
            ->validateName('last_name', 'Last name')
            ->validateName('suffix', 'Suffix')
            ->validateDate('birthdate', 'Date of birth', 4)
            ->validateLength('address', 5, 500, 'Address')
            ->validateNumeric('department_id', 'Department')
            ->validateCustom('confirm_password', function ($value) {
                return $value === ($_POST['password'] ?? '');
            }, 'Passwords do not match');

        // Check if basic validation passed
        if (!$validator->isValid()) {
            throw new Exception($validator->getErrorsAsString());
        }

        // Get sanitized data
        $data = $validator->getSanitizedData();

        // Database-specific validations
        if (!$dbValidator->isEmailUnique($data['email'])) {
            throw new Exception("Email address is already registered. Please use a different email.");
        }

        if (!$dbValidator->isUsernameUnique($data['username'])) {
            throw new Exception("Username is already taken. Please choose a different username.");
        }

        if (!$dbValidator->departmentExists($data['department_id'])) {
            throw new Exception("Selected department is not valid.");
        }

        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $db->addPendingStudent(
            $data['username'],
            $hashed_password,
            $data['email'],
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['suffix'] ?? null,
            $data['birthdate'],
            $data['address'],
            $data['educational_attainment'],
            $data['department_id'],
            "Pending",
            date('Y-m-d')
        );

        $_SESSION['success_message'] = "Registration successful! Please wait for admin approval.";
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

// Generate CSRF token for the form
$csrf_token = SecurityValidator::generateCSRFToken();

// Get departments for dropdown
$departments = $db->getDepartments(true); // Get only active departments
?>
<!DOCTYPE html>
<html lang="en" data-theme="ark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/output.css">
    <title>Arkheion - Student Registration</title>
</head>

<body>
    <main class="grid place-items-center gap-8 min-h-screen bg-base-200">
        <div class="flex flex-col gap-8">
            <!-- Success Message -->
            <?php if ($success_message): ?>
                <div role="alert" class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo htmlspecialchars($success_message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if ($error_message): ?>
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="flex flex-col gap-2 p-6 max-w-xl rounded-box shadow-lg bg-base-100">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <h2 class="text-lg font-bold">Student Registration</h2>
                <input type="text" name="username" class="input w-full" placeholder="Username" required>
                <input type="email" name="email" class="input w-full" placeholder="Email" required>
                <input type="password" name="password" class="input w-full" placeholder="Password" required>
                <input type="password" name="confirm_password" class="input w-full" placeholder="Confirm Password" required>

                <p class="text-sm mt-2 ml-2">Student Information</p>
                <div class="grid grid-cols-2 gap-2 w-full">
                    <input type="text" name="first_name" class="input" placeholder="First Name" required>
                    <input type="text" name="middle_name" class="input" placeholder="Middle Name">
                </div>
                <input type="text" name="last_name" class="input w-full" placeholder="Last Name" required>
                <div class="grid grid-cols-2 gap-2 w-full">
                    <label class="input">
                        <input type="text" name="suffix" class="grow" placeholder="Jr" />
                        <span class="badge badge-neutral badge-xs">Optional</span>
                    </label>
                    <label class="input">
                        <span class="label">Birthdate</span>
                        <input name="birthdate" type="date" required />
                    </label>
                </div>
                <input type="text" name="address" class="input w-full" placeholder="Address" required>
                <div class="grid grid-cols-2 gap-2 w-full">
                    <select name="educational_attainment" class="select w-full" required>
                        <option value="" selected disabled>Educational Attainment</option>
                        <option value="elementary">Elementary</option>
                        <option value="high-school">High School</option>
                        <option value="senior-high-school">Senior High School</option>
                        <option value="college">College</option>
                        <option value="masters">Masters</option>
                        <option value="doctorate">Doctorate</option>
                    </select>
                    <select name="department_id" class="select w-full" required>
                        <option value="" selected disabled>Select Department</option>
                        <?php
                        foreach ($departments as $department) {
                            echo "<option value=\"{$department['id']}\">" . htmlspecialchars($department['department_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit Registration</button>
                <a href="index.php" class="link link-primary text-center">Return home!</a>
            </form>
        </div>
    </main>
</body>

</html>