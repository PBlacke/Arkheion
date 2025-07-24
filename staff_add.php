<?php
// Include the connection file
require 'config/connection.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: index.php");
    exit();
}

// Initialize message variables
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $suffix = $_POST['suffix'];
        $birthdate = $_POST['birthdate'];
        $address = $_POST['address'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $department = $_POST['department'];

        // Generate a unique employee ID (current year + random number)
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $employee_id = $year . $random;

        // Check if the generated employee_id already exists
        $check_stmt = $conn->prepare("SELECT id FROM faculty WHERE employee_id = ?");
        $check_stmt->bind_param("s", $employee_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        // If employee_id exists, generate a new one
        while ($check_result->num_rows > 0) {
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $employee_id = $year . $random;
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
        }
        $check_stmt->close();

        // Insert into faculty table with employee_id
        $sql = "INSERT INTO faculty (first_name, middle_name, last_name, suffix, birthdate, address, username, email, password, department, employee_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error preparing statement: ' . $conn->error);
        }

        $stmt->bind_param(
            "sssssssssss",
            $first_name,
            $middle_name,
            $last_name,
            $suffix,
            $birthdate,
            $address,
            $username,
            $email,
            $password,
            $department,
            $employee_id
        );

        if (!$stmt->execute()) {
            throw new Exception('Error executing statement: ' . $stmt->error);
        }

        $success_message = 'Staff member added successfully! Employee ID: ' . $employee_id;
        $stmt->close();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get departments for dropdown
$dept_query = "SELECT DISTINCT department FROM department WHERE status = 'Active'";
$dept_result = $conn->query($dept_query);
$departments = [];
if ($dept_result && $dept_result->num_rows > 0) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Get curricula for dropdown
$curr_query = "SELECT DISTINCT curriculum FROM curriculum WHERE status2 = 'Active'";
$curr_result = $conn->query($curr_query);
$curricula = [];
if ($curr_result && $curr_result->num_rows > 0) {
    while ($row = $curr_result->fetch_assoc()) {
        $curricula[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Arkheion - Add Staff</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Add SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        h2 {
            color: #0c1776;
        }

        .form-container {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .name-group {
            display: flex;
            gap: 10px;
        }

        .name-group>div {
            flex: 1;
        }

        .suffix-field {
            flex: 0.5;
        }
    </style>
</head>

<body class="w3-light-grey">

    <?php if ($success_message): ?>
        <script>
            window.onload = function() {
                Swal.fire({
                    title: 'Success!',
                    text: <?php echo json_encode($success_message); ?>,
                    icon: 'success'
                }).then(function() {
                    window.location = 'facultylist2.php';
                });
            };
        </script>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <script>
            window.onload = function() {
                Swal.fire({
                    title: 'Error!',
                    text: <?php echo json_encode($error_message); ?>,
                    icon: 'error'
                });
            };
        </script>
    <?php endif; ?>

    <!-- Page Container -->
    <div class="w3-content w3-margin-top" style="max-width:1400px;">

        <!-- The Grid -->
        <div class="w3-row-padding">

            <?php include 'admin_nav.php'; ?>

            <!-- Right Column -->
            <div class="w3-twothird">

                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2 class="w3-text-grey w3-padding-16">
                        <i class="fa fa-user-plus fa-fw w3-margin-right w3-xxlarge w3-text-#0c1776"></i>Add New Staff
                    </h2>

                    <div class="form-container">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label>Name</label>
                                <div class="name-group">
                                    <div>
                                        <input type="text" name="first_name" placeholder="First Name" required>
                                    </div>
                                    <div>
                                        <input type="text" name="middle_name" placeholder="Middle Name">
                                    </div>
                                    <div>
                                        <input type="text" name="last_name" placeholder="Last Name" required>
                                    </div>
                                    <div class="suffix-field">
                                        <input type="text" name="suffix" placeholder="Suffix">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Birthdate</label>
                                <input type="date" name="birthdate" required>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" placeholder="Complete Address" required>
                            </div>

                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" required>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" required>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label>Department</label>
                                <select name="department" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept['department']); ?>">
                                            <?php echo htmlspecialchars($dept['department']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="w3-button w3-red">Add Staff</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- End Grid -->
        </div>

        <!-- End Page Container -->
    </div>

</body>

</html>