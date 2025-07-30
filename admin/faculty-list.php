<?php
session_start();
require '../config/connection.php';
require_once '../config/auth.php';
require '../includes/database.php';

requireRole(['admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Process the form
        $new_user_id = $db->addUser(
            $_POST['username'],
            $_POST['email'],
            $_POST['password']
        );

        $faculty_id = $db->addFaculty(
            $new_user_id,
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['last_name'],
            $_POST['suffix'] ?? null,
            $_POST['birthdate'],
            $_POST['address'],
            $_POST['phone'],
            $_POST['department'],
            $_POST['position'] ?? null,
            $_POST['specialization'] ?? null,
            date('Y-m-d'),
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

$faculties = $db->getFaculty();
$departments = $db->getDepartments();

// Get messages from session and clear them
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html data-theme="ark">

<head>
    <title>Staff List - Arkheion</title>
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen px-8 bg-base-200">
        <div class="grid grid-cols-dashboard gap-8 w-full max-w-[1440px]">
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
                        <span><?php echo htmlspecialchars($error_message); ?></span>
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
                                echo "<td>{$faculty['id']}</td>"; // Placeholder for checkbox or action buttons
                                echo "<td>{$faculty['first_name']}</td>";
                                echo "<td>{$faculty['middle_name']}</td>";
                                echo "<td>{$faculty['last_name']}</td>";
                                echo "<td>{$faculty['suffix']}</td>";
                                echo "<td>{$faculty['birthdate']}</td>";
                                echo "<td>{$faculty['address']}</td>";
                                echo "<td>{$faculty['phone']}</td>";
                                echo "<td>{$department['department_name']}</td>";
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
            <h2 class="text-lg font-bold">Add Faculty</h2>
            <input type="text" name="username" class="input w-full" placeholder="Username">
            <input type="email" name="email" class="input w-full" placeholder="Email">
            <input type="password" name="password" class="input w-full" placeholder="Password">
            <p class="text-sm mt-2 ml-2">User Information</p>
            <div class="grid grid-cols-2 gap-2 w-full">
                <input type="text" name="first_name" class="input" placeholder="First Name">
                <input type="text" name="middle_name" class="input" placeholder="Middle Name">
            </div>
            <div class="grid grid-cols-2 gap-2 w-full">
                <input type="text" name="last_name" class="input" placeholder="Last Name">
                <label class="input">
                    <input type="text" class="grow" placeholder="Ph.D" />
                    <span class="badge badge-neutral badge-xs">Optional</span>
                </label>
            </div>
            <input type="text" name="address" class="input w-full" placeholder="Address">
            <div class="grid grid-cols-2 gap-2 w-full">
                <label class="input">
                    <span class="label">Birthdate</span>
                    <input name="birthdate" type="date" />
                </label>
                <input type="text" name="phone" class="input" placeholder="Phone">
            </div>
            <div class="grid grid-cols-2 gap-2 w-full">
                <select name="department" class="select w-full">
                    <?php
                    foreach ($departments as $department) {
                        echo "<option value=\"{$department['id']}\">{$department['department_name']}</option>";
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