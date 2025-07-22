<?php
// Include the connection file
require 'connection.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $suffix = mysqli_real_escape_string($conn, $_POST['suffix']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $educational_attainment = mysqli_real_escape_string($conn, $_POST['educational_attainment']);

    // Validate password length
    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    }
    else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username already exists
        $check_username = $conn->prepare("SELECT id FROM students WHERE username = ?");
        $check_username->bind_param("s", $username);
        $check_username->execute();
        $username_result = $check_username->get_result();

        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $email_result = $check_email->get_result();

        if ($username_result->num_rows > 0) {
            $message = "Username already exists.";
        }
        elseif ($email_result->num_rows > 0) {
            $message = "Email already exists.";
        }
        else {
            // Prepare and execute the SQL statement
            $stmt = $conn->prepare("INSERT INTO students (username, email, password, first_name, middle_name, last_name, suffix, date_of_birth, address, educational_attainment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $username, $email, $hashed_password, $first_name, $middle_name, $last_name, $suffix, $date_of_birth, $address, $educational_attainment);

            if ($stmt->execute()) {
                // Get the last inserted ID
                $last_id = $stmt->insert_id;
                
                try {
                    // Notify teachers of the same department
                    $notify_teachers = $conn->prepare("INSERT INTO notifications (faculty_id, message, type, reference_id) 
                        SELECT f.id, 
                               CONCAT(?, ' has registered as a student') as message,
                               'student_registration' as type,
                               ? as reference_id
                        FROM faculty f 
                        WHERE f.department = ? AND f.status = 'Active'");
                    
                    if ($notify_teachers) {
                        $full_name = $first_name . ' ' . $last_name;
                        $notify_teachers->bind_param("sis", $full_name, $last_id, $department);
                        $notify_teachers->execute();
                    }
                } catch (Exception $e) {
                    // Log the error but don't stop the registration process
                    error_log("Failed to send notification: " . $e->getMessage());
                }

                $message = "Registration successful! Please wait for a teacher from your department to approve your registration.";
                // Clear form data
                $_POST = array();
            } else {
                $message = "Error during registration. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
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
            background-color: #fff;
            font-family: 'Roboto', sans-serif;
        }
        .registration-form {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            width: 100%;
            height: auto;
        }
        .form-control:focus {
            border-color: maroon;
            box-shadow: 0 0 0 0.2rem rgba(128,0,0,0.15);
        }
        select.form-control {
            padding-right: 30px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
        .btn-primary {
            background-color: maroon;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: auto;
            min-width: 200px;
        }
        .btn-primary:hover {
            background-color: #600;
            transform: translateY(-1px);
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
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
            min-height: 100px;
            resize: vertical;
        }
        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        @media (max-width: 768px) {
            .registration-form {
                margin: 15px;
                padding: 20px;
            }
            .row {
                margin-left: -10px;
                margin-right: -10px;
            }
            .col-md-4, .col-md-6 {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-1">
            <a href="index.php" class="logo"> <i style="color: red;" class="fa fa-book fa-fw"></i> Arkheion</a>
            <div class="icons">
                <div id="search-btn" style="color: red;" class="fas fa-search"></div>
                <div id="login-btn" style="color: red;"><a href='adminlogin.php'>Admin Login</a></div>
            </div>
            <div class="icons">
                <div id="login-btn" style="color: red;"><a href='facultylogin.php'>Staff Login</a></div>
            </div>
        </div>
    </header>

    <div class="registration-form">
        <h2 class="text-center mb-4">Student Registration</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="middle_name">Middle Name (Optional):</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name">
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>

            <div class="form-group">
                <label for="suffix">Suffix (Optional):</label>
                <input type="text" class="form-control" id="suffix" name="suffix">
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
            </div>

            <div class="form-group">
                <label for="address">Complete Address:</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="educational_attainment">Educational Attainment:</label>
                <select class="form-control" id="educational_attainment" name="educational_attainment" required>
                    <option value="">Select Educational Attainment</option>
                    <option value="Elementary">Elementary</option>
                    <option value="High School">High School</option>
                    <option value="Senior High School">Senior High School</option>
                    <option value="College">College</option>
                    <option value="Masters">Masters</option>
                    <option value="Doctorate">Doctorate</option>
                </select>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password (minimum 8 characters):</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p class="text-center mt-3">Already have an account? <a href="studentlogin.php">Login here</a></p>
            <p class="text-center"><a href="index.php">Back to Homepage</a></p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 