<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['employee_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: facultylogin.php");
    exit();
}

// Get the employee ID from the session
$employeeId = $_SESSION['employee_id'];

// Get current faculty data and department list
require 'config/connection.php';
$stmt = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();
$facultyData = $result->fetch_assoc();
$stmt->close();

// Get departments list
$deptResult = $conn->query("SELECT department FROM department WHERE status = 'Active' ORDER BY department");
$departments = [];
while ($row = $deptResult->fetch_assoc()) {
    $departments[] = $row['department'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Arkheion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

        button[type="submit"],
        .change-password-btn {
            background-color: #0c1776 !important;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover,
        .change-password-btn:hover {
            background-color: #0a1257 !important;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal.show {
            opacity: 1;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            position: relative;
            transform: translateY(-50px);
            transition: transform 0.3s ease-in-out;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .close {
            position: absolute;
            right: 10px;
            top: 5px;
            color: #aaa;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .close:hover {
            color: #666;
            background-color: #f0f0f0;
        }

        .password-requirements {
            font-size: 0.9em;
            color: #666;
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .requirement {
            margin: 5px 0;
        }

        .requirement.valid {
            color: green;
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
            <!-- End Left Column -->

            <!-- Right Column -->
            <div class="w3-twothird">

                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2 class="w3-padding-16"><i class="fa fa-user fa-fw"></i>Update Faculty Profile</h2>
                    <div class="w3-container">
                        <h5 class="w3-opacity"><b>Update your profile information</b></h5>
                        <div style="padding: 20px;">

                            <!-- Update Form -->
                            <form method="post" action="" id="profileForm">
                                <div style="margin-bottom: 20px;">
                                    <h4 style="color: #0c1776; text-align: left;">Personal Information</h4>

                                    <div class="form-row">
                                        <div class="form-group" style="flex: 2;">
                                            <label for="firstName">First Name:</label>
                                            <input type="text" id="firstName" name="firstName"
                                                placeholder="<?php echo htmlspecialchars($facultyData['first_name']); ?>"
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>
                                        <div class="form-group" style="flex: 2;">
                                            <label for="middleName">Middle Name:</label>
                                            <input type="text" id="middleName" name="middleName"
                                                placeholder="<?php echo htmlspecialchars($facultyData['middle_name']); ?>"
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>
                                        <div class="form-group" style="flex: 2;">
                                            <label for="lastName">Last Name:</label>
                                            <input type="text" id="lastName" name="lastName"
                                                placeholder="<?php echo htmlspecialchars($facultyData['last_name']); ?>"
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>
                                        <div class="form-group" style="flex: 1;">
                                            <label for="suffix">Suffix:</label>
                                            <input type="text" id="suffix" name="suffix"
                                                placeholder="<?php echo htmlspecialchars($facultyData['suffix']); ?>"
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="birthdate">Birthdate:</label>
                                            <input type="date" id="birthdate" name="birthdate"
                                                value="<?php echo htmlspecialchars($facultyData['birthdate']); ?>"
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>
                                        <div class="form-group">
                                            <label for="employeeId">Employee ID:</label>
                                            <input type="text" id="employeeId" name="employeeId"
                                                value="<?php echo htmlspecialchars($facultyData['employee_id']); ?>"
                                                readonly
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background-color: #f0f0f0;">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="department">Department:</label>
                                            <input type="text" id="department" name="department"
                                                list="departmentList"
                                                placeholder="<?php echo htmlspecialchars($facultyData['department']); ?>"
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                            <datalist id="departmentList">
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo htmlspecialchars($dept); ?>">
                                                    <?php endforeach; ?>
                                            </datalist>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" id="email" name="email"
                                                placeholder="<?php echo htmlspecialchars($facultyData['email']); ?>"
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address">Address:</label>
                                        <textarea id="address" name="address" rows="3" maxlength="256"
                                            placeholder="<?php echo htmlspecialchars($facultyData['address']); ?>"
                                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"></textarea>
                                    </div>

                                    <div class="button-group">
                                        <button type="button" class="change-password-btn" onclick="openPasswordModal()">
                                            <i class="fa fa-key"></i> Change Password
                                        </button>
                                        <button type="submit">
                                            <i class="fa fa-save"></i> Update Profile
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Password Change Modal -->
                            <div id="passwordModal" class="modal">
                                <div class="modal-content">
                                    <span class="close" onclick="closePasswordModal()">&times;</span>
                                    <h3 style="color: #0c1776;">Change Password</h3>
                                    <form id="passwordForm" onsubmit="return validatePasswordForm(event)">
                                        <div class="form-group" style="margin-bottom: 15px;">
                                            <label for="currentPassword">Current Password:</label>
                                            <input type="password" id="currentPassword" name="currentPassword" required
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>

                                        <div class="form-group" style="margin-bottom: 15px;">
                                            <label for="newPassword">New Password:</label>
                                            <input type="password" id="newPassword" name="newPassword" required
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"
                                                oninput="checkPasswordRequirements()">
                                        </div>

                                        <div class="password-requirements">
                                            <div id="lengthReq" class="requirement">✗ Minimum 8 characters</div>
                                            <div id="upperReq" class="requirement">✗ At least 1 uppercase letter</div>
                                            <div id="numberReq" class="requirement">✗ At least 1 number</div>
                                            <div id="specialReq" class="requirement">✗ At least 1 special character</div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 15px;">
                                            <label for="confirmNewPassword">Confirm New Password:</label>
                                            <input type="password" id="confirmNewPassword" name="confirmNewPassword" required
                                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                        </div>

                                        <button type="submit" style="width: 100%;">Update Password</button>
                                    </form>
                                </div>
                            </div>

                            <script>
                                // Modal functions
                                function openPasswordModal() {
                                    const modal = document.getElementById('passwordModal');
                                    modal.style.display = 'block';
                                    // Trigger reflow before adding the show class
                                    modal.offsetHeight;
                                    modal.classList.add('show');
                                }

                                function closePasswordModal() {
                                    const modal = document.getElementById('passwordModal');
                                    modal.classList.remove('show');
                                    // Wait for transition to complete before hiding
                                    setTimeout(() => {
                                        modal.style.display = 'none';
                                        document.getElementById('passwordForm').reset();
                                        resetRequirements();
                                    }, 300);
                                }

                                // Password validation
                                function checkPasswordRequirements() {
                                    const password = document.getElementById('newPassword').value;
                                    const requirements = {
                                        lengthReq: password.length >= 8,
                                        upperReq: /[A-Z]/.test(password),
                                        numberReq: /[0-9]/.test(password),
                                        specialReq: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                                    };

                                    for (const [req, valid] of Object.entries(requirements)) {
                                        const element = document.getElementById(req);
                                        element.innerHTML = `${valid ? '✓' : '✗'} ${element.innerHTML.substring(2)}`;
                                        element.className = `requirement ${valid ? 'valid' : ''}`;
                                    }

                                    return Object.values(requirements).every(req => req);
                                }

                                function resetRequirements() {
                                    const requirements = ['lengthReq', 'upperReq', 'numberReq', 'specialReq'];
                                    requirements.forEach(req => {
                                        const element = document.getElementById(req);
                                        element.innerHTML = `✗ ${element.innerHTML.substring(2)}`;
                                        element.className = 'requirement';
                                    });
                                }

                                function validatePasswordForm(event) {
                                    event.preventDefault();

                                    const currentPassword = document.getElementById('currentPassword').value;
                                    const newPassword = document.getElementById('newPassword').value;
                                    const confirmPassword = document.getElementById('confirmNewPassword').value;

                                    if (!checkPasswordRequirements()) {
                                        swal({
                                            title: "Invalid Password",
                                            text: "Please meet all password requirements",
                                            icon: "error"
                                        });
                                        return false;
                                    }

                                    if (newPassword !== confirmPassword) {
                                        swal({
                                            title: "Password Mismatch",
                                            text: "New passwords do not match",
                                            icon: "error"
                                        });
                                        return false;
                                    }

                                    // Send password update request via AJAX
                                    const formData = new FormData();
                                    formData.append('action', 'change_password');
                                    formData.append('current_password', currentPassword);
                                    formData.append('new_password', newPassword);

                                    fetch(window.location.href, {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Network response was not ok');
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            if (data.success) {
                                                swal({
                                                    title: "Success",
                                                    text: "Password updated successfully!",
                                                    icon: "success"
                                                }).then(() => {
                                                    closePasswordModal();
                                                });
                                            } else {
                                                swal({
                                                    title: "Error",
                                                    text: data.message || "Failed to update password",
                                                    icon: "error"
                                                });
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            swal({
                                                title: "Error",
                                                text: "An error occurred while updating password. Please try again.",
                                                icon: "error"
                                            });
                                        });

                                    return false;
                                }

                                // Close modal when clicking outside
                                window.onclick = function(event) {
                                    const modal = document.getElementById('passwordModal');
                                    if (event.target == modal) {
                                        closePasswordModal();
                                    }
                                }
                            </script>

                            <?php
                            require 'config/connection.php';

                            // Function to verify password requirements
                            function verifyPasswordRequirements($password)
                            {
                                return strlen($password) >= 8 &&
                                    preg_match('/[A-Z]/', $password) &&
                                    preg_match('/[0-9]/', $password) &&
                                    preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
                            }

                            // Handle password change request
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
                                header('Content-Type: application/json');
                                $response = array('success' => false);

                                try {
                                    $currentPassword = $_POST['current_password'];
                                    $newPassword = $_POST['new_password'];

                                    // Verify current password
                                    $stmt = $conn->prepare("SELECT password FROM faculty WHERE id = ?");
                                    if (!$stmt) {
                                        throw new Exception("Failed to prepare statement: " . $conn->error);
                                    }

                                    $stmt->bind_param("i", $employeeId);
                                    if (!$stmt->execute()) {
                                        throw new Exception("Failed to execute statement: " . $stmt->error);
                                    }

                                    $result = $stmt->get_result();
                                    $user = $result->fetch_assoc();
                                    $stmt->close();

                                    if (!$user) {
                                        throw new Exception("User not found");
                                    }

                                    if (!password_verify($currentPassword, $user['password'])) {
                                        $response['message'] = "Current password is incorrect";
                                    }
                                    // Verify password requirements
                                    elseif (
                                        strlen($newPassword) < 8 ||
                                        !preg_match('/[A-Z]/', $newPassword) ||
                                        !preg_match('/[0-9]/', $newPassword) ||
                                        !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $newPassword)
                                    ) {
                                        $response['message'] = "New password does not meet requirements";
                                    } else {
                                        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                                        $updateStmt = $conn->prepare("UPDATE faculty SET password = ? WHERE id = ?");
                                        if (!$updateStmt) {
                                            throw new Exception("Failed to prepare update statement: " . $conn->error);
                                        }

                                        $updateStmt->bind_param("si", $hashedPassword, $employeeId);
                                        if (!$updateStmt->execute()) {
                                            throw new Exception("Failed to update password: " . $updateStmt->error);
                                        }

                                        if ($updateStmt->affected_rows > 0) {
                                            $response['success'] = true;
                                            $response['message'] = "Password updated successfully";
                                        } else {
                                            throw new Exception("No changes were made to the password");
                                        }

                                        $updateStmt->close();
                                    }
                                } catch (Exception $e) {
                                    $response['message'] = "An error occurred: " . $e->getMessage();
                                    error_log("Password update error for user ID $employeeId: " . $e->getMessage());
                                }

                                echo json_encode($response);
                                exit;
                            }

                            // Handle profile update
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
                                // Initialize update fields array
                                $updateFields = array();
                                $params = array();
                                $types = '';

                                // Check each field and add to update only if it's not empty and different from current value
                                if (!empty($_POST["email"])) {
                                    $updateFields[] = "email = ?";
                                    $params[] = $_POST["email"];
                                    $types .= 's';
                                }
                                if (!empty($_POST["firstName"])) {
                                    $updateFields[] = "first_name = ?";
                                    $params[] = $_POST["firstName"];
                                    $types .= 's';
                                }
                                if (!empty($_POST["middleName"])) {
                                    $updateFields[] = "middle_name = ?";
                                    $params[] = $_POST["middleName"];
                                    $types .= 's';
                                }
                                if (!empty($_POST["lastName"])) {
                                    $updateFields[] = "last_name = ?";
                                    $params[] = $_POST["lastName"];
                                    $types .= 's';
                                }
                                if (!empty($_POST["suffix"])) {
                                    $updateFields[] = "suffix = ?";
                                    $params[] = $_POST["suffix"];
                                    $types .= 's';
                                }
                                // Only include birthdate if it's different from the current value
                                if (!empty($_POST["birthdate"]) && $_POST["birthdate"] !== $facultyData['birthdate']) {
                                    $updateFields[] = "birthdate = ?";
                                    $params[] = $_POST["birthdate"];
                                    $types .= 's';
                                }
                                if (!empty($_POST["address"])) {
                                    $updateFields[] = "address = LEFT(?, 256)";
                                    $params[] = $_POST["address"];
                                    $types .= 's';
                                }
                                if (!empty($_POST["department"])) {
                                    $updateFields[] = "department = ?";
                                    $params[] = $_POST["department"];
                                    $types .= 's';
                                }

                                // Only proceed if there are fields to update
                                if (!empty($updateFields)) {
                                    // Add the employee ID to the parameters
                                    $params[] = $employeeId;
                                    $types .= 'i';

                                    // Create the SQL query
                                    $updateSql = "UPDATE faculty SET " . implode(", ", $updateFields) . " WHERE id = ?";

                                    $stmt = $conn->prepare($updateSql);
                                    // Bind parameters dynamically
                                    $stmt->bind_param($types, ...$params);

                                    if ($stmt->execute()) {
                            ?>
                                        <script>
                                            swal({
                                                title: "Success!",
                                                text: "Profile updated successfully!",
                                                icon: "success",
                                            }).then(function() {
                                                // Redirect to prevent form resubmission
                                                window.location.href = 'fac_settings.php';
                                            });
                                        </script>
                                    <?php
                                    } else {
                                    ?>
                                        <script>
                                            swal({
                                                title: "Error!",
                                                text: "Failed to update profile. Please try again.",
                                                icon: "error"
                                            });
                                        </script>
                                    <?php
                                    }
                                    $stmt->close();
                                } else {
                                    ?>
                                    <script>
                                        swal({
                                            title: "Nothing to Update",
                                            text: "No changes were made. Please fill in at least one field to update your profile.",
                                            icon: "info"
                                        });
                                    </script>
                            <?php
                                }
                            }

                            // Add this JavaScript to prevent form resubmission when page is refreshed
                            ?>
                            <script>
                                if (window.history.replaceState) {
                                    window.history.replaceState(null, null, window.location.href);
                                }

                                // Store the initial birthdate value
                                const initialBirthdate = <?php echo json_encode($facultyData['birthdate']); ?>;

                                // Modify the form submission to prevent empty submissions
                                document.getElementById('profileForm').addEventListener('submit', function(e) {
                                    e.preventDefault();

                                    // Check if any field has been filled (excluding employee ID and unchanged birthdate)
                                    let hasChanges = false;
                                    const inputs = this.querySelectorAll('input, textarea');
                                    inputs.forEach(input => {
                                        // Skip submit buttons, employee ID field, unchanged birthdate, and empty fields
                                        if (input.type !== 'submit' &&
                                            input.type !== 'button' &&
                                            input.id !== 'employeeId' &&
                                            input.value.trim() !== '' &&
                                            !(input.id === 'birthdate' && input.value === initialBirthdate)) {
                                            hasChanges = true;
                                        }
                                    });

                                    if (hasChanges) {
                                        this.submit();
                                    } else {
                                        swal({
                                            title: "Nothing to Update",
                                            text: "No changes were made. Please fill in at least one field to update your profile.",
                                            icon: "info"
                                        });
                                    }
                                });

                                // Add change detection for birthdate
                                document.getElementById('birthdate').addEventListener('change', function() {
                                    // Mark as changed only if the value is different from initial
                                    this.dataset.changed = (this.value !== initialBirthdate).toString();
                                });
                            </script>

                        </div>
                        <hr>
                    </div>
                </div>

                <!-- End Grid -->
            </div>

            <!-- End Page Container -->
        </div>

</body>

</html>