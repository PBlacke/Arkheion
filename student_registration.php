<?php
// Include the connection file
require 'config/connection.php';

session_start();

$message = '';
$message_type = '';

// Function to sanitize input
function sanitize_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to validate email
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password strength
function validate_password($password)
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }

    return $errors;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Invalid request. Please try again.";
        $message_type = 'error';
    } else {
        // Retrieve and sanitize form data
        $username = sanitize_input($_POST['username']);
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $first_name = sanitize_input($_POST['first_name']);
        $middle_name = sanitize_input($_POST['middle_name']);
        $last_name = sanitize_input($_POST['last_name']);
        $suffix = sanitize_input($_POST['suffix']);
        $date_of_birth = sanitize_input($_POST['date_of_birth']);
        $address = sanitize_input($_POST['address']);
        $educational_attainment = sanitize_input($_POST['educational_attainment']);
        $department = sanitize_input($_POST['department_id']);

        // Validation array to collect all errors
        $validation_errors = [];

        // Validate required fields
        if (empty($username)) $validation_errors[] = "Username is required";
        if (empty($email)) $validation_errors[] = "Email is required";
        if (empty($password)) $validation_errors[] = "Password is required";
        if (empty($first_name)) $validation_errors[] = "First name is required";
        if (empty($last_name)) $validation_errors[] = "Last name is required";
        if (empty($date_of_birth)) $validation_errors[] = "Date of birth is required";
        if (empty($address)) $validation_errors[] = "Address is required";
        if (empty($educational_attainment)) $validation_errors[] = "Educational attainment is required";
        if (empty($department)) $validation_errors[] = "Department is required";

        // Validate username (alphanumeric and underscore only, 3-20 characters)
        if (!empty($username) && !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            $validation_errors[] = "Username must be 3-20 characters long and contain only letters, numbers, and underscores";
        }

        // Validate email
        if (!empty($email) && !validate_email($email)) {
            $validation_errors[] = "Please enter a valid email address";
        }

        // Validate password strength
        if (!empty($password)) {
            $password_errors = validate_password($password);
            $validation_errors = array_merge($validation_errors, $password_errors);
        }

        // Check if passwords match
        if ($password !== $confirm_password) {
            $validation_errors[] = "Passwords do not match";
        }

        // Validate date of birth (must be at least 13 years old)
        if (!empty($date_of_birth)) {
            $birth_date = new DateTime($date_of_birth);
            $today = new DateTime();
            $age = $today->diff($birth_date)->y;

            if ($age < 13) {
                $validation_errors[] = "You must be at least 13 years old to register";
            }
            if ($age > 120) {
                $validation_errors[] = "Please enter a valid date of birth";
            }
        }

        // If there are validation errors, display them
        if (!empty($validation_errors)) {
            $message = implode("<br>", $validation_errors);
            $message_type = 'error';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Begin transaction
                $conn->begin_transaction();

                // Check if username already exists
                $check_username = $conn->prepare("SELECT id FROM pending_students WHERE username = ?");
                $check_username->bind_param("s", $username);
                $check_username->execute();
                $username_result = $check_username->get_result();

                // Check if email already exists
                $check_email = $conn->prepare("SELECT id FROM pending_students WHERE email = ?");
                $check_email->bind_param("s", $email);
                $check_email->execute();
                $email_result = $check_email->get_result();

                if ($username_result->num_rows > 0) {
                    $message = "Username already exists. Please choose a different username.";
                    $message_type = 'error';
                } elseif ($email_result->num_rows > 0) {
                    $message = "Email already exists. Please use a different email address.";
                    $message_type = 'error';
                } else {
                    // Prepare and execute the SQL statement
                    $stmt = $conn->prepare("INSERT INTO pending_students (username, password, email, first_name, middle_name, last_name, suffix, date_of_birth, address, educational_attainment, department_id, status, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
                    $stmt->bind_param("sssssssssss", $username, $hashed_password, $email, $first_name, $middle_name, $last_name, $suffix, $date_of_birth, $address, $educational_attainment, $department);

                    if ($stmt->execute()) {
                        // Get the last inserted ID
                        // $last_id = $stmt->insert_id;

                        // // Notify teachers of the same department
                        // $notify_teachers = $conn->prepare("INSERT INTO notifications (faculty_id, message, type, reference_id, created_at) 
                        //     SELECT f.id, 
                        //            CONCAT(?, ' has registered as a student in ', ?) as message,
                        //            'student_registration' as type,
                        //            ? as reference_id,
                        //            NOW() as created_at
                        //     FROM faculty f 
                        //     WHERE f.department = ? AND f.status = 'Active'");

                        // if ($notify_teachers) {
                        //     $full_name = trim($first_name . ' ' . $middle_name . ' ' . $last_name);
                        //     $notify_teachers->bind_param("ssis", $full_name, $department, $last_id, $department);
                        //     $notify_teachers->execute();
                        // }

                        // Commit transaction
                        $conn->commit();

                        $message = "Registration successful! Please wait for a teacher from your department to approve your registration. You will receive an email notification once approved.";
                        $message_type = 'success';

                        // Clear form data on success
                        $_POST = array();
                    } else {
                        throw new Exception("Error during registration: " . $stmt->error);
                    }
                    $stmt->close();
                }

                $check_username->close();
                $check_email->close();
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error: " . $e->getMessage(); // Show full error for debugging
                $message_type = 'error';
            }
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="shortcut icon" type="x-icon" href="LOGO.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Arkheion - Student Registration</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }

        .registration-form {
            max-width: 900px;
            margin: 30px auto;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .required::after {
            content: " *";
            color: red;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e1e5e9;
            padding: 12px 15px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .form-control:focus {
            border-color: maroon;
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.15);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-valid {
            border-color: #28a745;
        }

        select.form-control {
            padding-right: 40px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        .btn-primary {
            background: linear-gradient(135deg, maroon 0%, #8B0000 100%);
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #8B0000 0%, #600 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 0, 0, 0.3);
        }

        .message {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            text-align: left;
            font-weight: 500;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-left: 5px solid #28a745;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-left: 5px solid #dc3545;
        }

        .header {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .logo i {
            color: maroon;
            margin-right: 10px;
        }

        .logo:hover {
            text-decoration: none;
            color: #333;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .password-strength {
            margin-top: 10px;
        }

        .strength-bar {
            height: 4px;
            background-color: #e1e5e9;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak {
            background-color: #dc3545;
            width: 25%;
        }

        .strength-fair {
            background-color: #ffc107;
            width: 50%;
        }

        .strength-good {
            background-color: #17a2b8;
            width: 75%;
        }

        .strength-strong {
            background-color: #28a745;
            width: 100%;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .page-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 2.5rem;
            position: relative;
        }

        .page-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, maroon 0%, #8B0000 100%);
            margin: 15px auto;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .registration-form {
                margin: 15px;
                padding: 25px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .page-title {
                font-size: 2rem;
            }
        }

        .nav-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }

        .nav-links a {
            color: maroon;
            text-decoration: none;
            font-weight: 500;
            margin: 0 15px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: #8B0000;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="index.php" class="logo">
                    <i class="fa fa-book fa-fw"></i> Arkheion
                </a>
                <div class="d-flex gap-3">
                    <a href="adminlogin.php" class="btn btn-outline-danger btn-sm">Admin Login</a>
                    <a href="facultylogin.php" class="btn btn-outline-danger btn-sm">Staff Login</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="registration-form">
            <h1 class="page-title">Student Registration</h1>

            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="fa <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" id="registrationForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="required">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                            value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name"
                            value="<?php echo isset($_POST['middle_name']) ? htmlspecialchars($_POST['middle_name']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="last_name" class="required">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                            value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="suffix">Suffix</label>
                        <select class="form-control" id="suffix" name="suffix">
                            <option value="">Select Suffix (Optional)</option>
                            <option value="Jr." <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'Jr.') ? 'selected' : ''; ?>>Jr.</option>
                            <option value="Sr." <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'Sr.') ? 'selected' : ''; ?>>Sr.</option>
                            <option value="II" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'II') ? 'selected' : ''; ?>>II</option>
                            <option value="III" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'III') ? 'selected' : ''; ?>>III</option>
                            <option value="IV" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'IV') ? 'selected' : ''; ?>>IV</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="required">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                        value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="address" class="required">Complete Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"
                        placeholder="Enter your complete address including barangay, city/municipality, and province" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="educational_attainment" class="required">Educational Attainment</label>
                        <select class="form-control" id="educational_attainment" name="educational_attainment" required>
                            <option value="">Select Educational Attainment</option>
                            <option value="Elementary" <?php echo (isset($_POST['educational_attainment']) && $_POST['educational_attainment'] === 'Elementary') ? 'selected' : ''; ?>>Elementary</option>
                            <option value="High School" <?php echo (isset($_POST['educational_attainment']) && $_POST['educational_attainment'] === 'High School') ? 'selected' : ''; ?>>High School</option>
                            <option value="Senior High School" <?php echo (isset($_POST['educational_attainment']) && $_POST['educational_attainment'] === 'Senior High School') ? 'selected' : ''; ?>>Senior High School</option>
                            <option value="College" <?php echo (isset($_POST['educational_attainment']) && $_POST['educational_attainment'] === 'College') ? 'selected' : ''; ?>>College</option>
                            <option value="Masters" <?php echo (isset($_POST['educational_attainment']) && $_POST['educational_attainment'] === 'Masters') ? 'selected' : ''; ?>>Masters</option>
                            <option value="Doctorate" <?php echo (isset($_POST['educational_attainment']) && $_POST['educational_attainment'] === 'Doctorate') ? 'selected' : ''; ?>>Doctorate</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="department" class="required">Department</label>
                        <select class="form-control" id="department" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM `department` WHERE 1");
                            $stmt->execute();

                            // Fetch all rows
                            $departments = $stmt->get_result();
                            echo $departments->num_rows > 0 ? '' : '<option value="">No departments available</option>';
                            while ($row = $departments->fetch_assoc()) {
                                $selected = (isset($_POST['department']) && $_POST['department'] === $row['id']) ? 'selected' : '';
                                echo "<option value=\"{$row['id']}\" $selected>{$row['department_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="username" class="required">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            pattern="[a-zA-Z0-9_]{3,20}" title="Username must be 3-20 characters long and contain only letters, numbers, and underscores" required>
                        <div class="form-text">3-20 characters, letters, numbers, and underscores only</div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="required">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="required">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="form-text" id="strengthText">Password must contain: uppercase, lowercase, number, and special character</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="required">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="form-text" id="passwordMatch"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Register Account
                </button>
            </form>

            <div class="nav-links">
                <a href="studentlogin.php"><i class="fa fa-sign-in-alt"></i> Already have an account? Login here</a>
                <a href="index.php"><i class="fa fa-home"></i> Back to Homepage</a>
            </div>
        </div>
    </div>

    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            let strength = 0;
            let feedback = [];

            if (password.length >= 8) strength++;
            else feedback.push('8+ characters');

            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('uppercase letter');

            if (/[a-z]/.test(password)) strength++;
            else feedback.push('lowercase letter');

            if (/[0-9]/.test(password)) strength++;
            else feedback.push('number');

            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('special character');

            // Update strength bar
            strengthFill.className = 'strength-fill';
            if (strength === 1) strengthFill.classList.add('strength-weak');
            else if (strength === 2) strengthFill.classList.add('strength-fair');
            else if (strength === 3) strengthFill.classList.add('strength-good');
            else if (strength >= 4) strengthFill.classList.add('strength-strong');

            // Update feedback text
            if (feedback.length > 0) {
                strengthText.textContent = 'Missing: ' + feedback.join(', ');
                strengthText.style.color = '#dc3545';
            } else {
                strengthText.textContent = 'Strong password!';
                strengthText.style.color = '#28a745';
            }
        });

        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchText.textContent = 'Passwords match!';
                    matchText.style.color = '#28a745';
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    matchText.textContent = 'Passwords do not match';
                    matchText.style.color = '#dc3545';
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                matchText.textContent = '';
                this.classList.remove('is-valid', 'is-invalid');
            }
        });

        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });

        // Auto-dismiss success messages
        setTimeout(function() {
            const successMessage = document.querySelector('.message.success');
            if (successMessage) {
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 300);
            }
        }, 5000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>