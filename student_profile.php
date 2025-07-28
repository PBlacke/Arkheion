<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: studentlogin.php");
    exit();
}

// Include the connection file
require 'config/connection.php';

// Initialize message variable
$message = '';

// Get student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    // Handle error - student not found
    header("Location: logout.php");
    exit();
}

// Handle form submission for profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $suffix = mysqli_real_escape_string($conn, $_POST['suffix']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $educational_attainment = mysqli_real_escape_string($conn, $_POST['educational_attainment']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email is already taken by another user
    $check_email = $conn->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
    $check_email->bind_param("si", $email, $student_id);
    $check_email->execute();
    $email_result = $check_email->get_result();

    if ($email_result->num_rows > 0) {
        $message = "Email already exists.";
    } else {
        // Update the student information
        $update_stmt = $conn->prepare("UPDATE students SET first_name = ?, middle_name = ?, last_name = ?, suffix = ?, date_of_birth = ?, address = ?, educational_attainment = ?, email = ? WHERE id = ?");
        $update_stmt->bind_param(
            "ssssssssi",
            $first_name,
            $middle_name,
            $last_name,
            $suffix,
            $date_of_birth,
            $address,
            $educational_attainment,
            $email,
            $student_id
        );

        if ($update_stmt->execute()) {
            $message = "Profile updated successfully!";
            // Refresh student data
            $stmt->execute();
            $student = $stmt->get_result()->fetch_assoc();
        } else {
            $message = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Arkheion - Student Profile</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="x-icon" href="LOGO.png">
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

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
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

                <div class="w3-white w3-text-grey w3-card-4">
                    <div class="w3-container">
                        <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Student Profile</h2>
                        <p><i class="fa fa-user fa-fw w3-margin-right w3-large w3-text-red"></i>
                            <?php
                            echo htmlspecialchars($student['first_name']) . ' ' .
                                ($student['middle_name'] ? htmlspecialchars($student['middle_name']) . ' ' : '') .
                                htmlspecialchars($student['last_name']) .
                                ($student['suffix'] ? ' ' . htmlspecialchars($student['suffix']) : '');
                            ?>
                        </p>
                        <p><i class="fa fa-id-badge fa-fw w3-margin-right w3-large w3-text-red"></i><?php echo htmlspecialchars($student['username']); ?></p>
                        <p><i class="fa fa-envelope fa-fw w3-margin-right w3-large w3-text-red"></i><?php echo htmlspecialchars($student['email']); ?></p>
                        <p><i class="fa fa-calendar fa-fw w3-margin-right w3-large w3-text-red"></i><?php echo date('F j, Y', strtotime($student['date_of_birth'])); ?></p>
                        <p><i class="fa fa-home fa-fw w3-margin-right w3-large w3-text-red"></i><?php echo htmlspecialchars($student['address']); ?></p>
                        <p><i class="fa fa-graduation-cap fa-fw w3-margin-right w3-large w3-text-red"></i><?php echo htmlspecialchars($student['educational_attainment']); ?></p>
                        <hr>

                        <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-red"></i>Options</b></p>
                        <a href="student_dashboard.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-home fa-fw"></i> Dashboard</a>
                        <a href="student_papers.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-book fa-fw"></i> My Papers</a>
                        <a href="student_profile.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-user fa-fw"></i> Profile</a>
                        <a href="student_settings.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-cog fa-fw"></i> Settings</a>
                        <a href="logout.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        <br>
                    </div>
                </div>

                <!-- End Left Column -->
            </div>

            <!-- Right Column -->
            <div class="w3-twothird">

                <div class="w3-container w3-card w3-white w3-margin-bottom">
                    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-edit fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Edit Profile</h2>

                    <?php if (isset($message)): ?>
                        <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="w3-container">
                        <form method="post" action="">
                            <div class="w3-row-padding">
                                <div class="w3-third">
                                    <label for="first_name"><i class="fa fa-user"></i> First Name</label>
                                    <input type="text" class="w3-input w3-border" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                                </div>
                                <div class="w3-third">
                                    <label for="middle_name"><i class="fa fa-user"></i> Middle Name</label>
                                    <input type="text" class="w3-input w3-border" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($student['middle_name']); ?>">
                                </div>
                                <div class="w3-third">
                                    <label for="last_name"><i class="fa fa-user"></i> Last Name</label>
                                    <input type="text" class="w3-input w3-border" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                                </div>
                            </div>

                            <div class="w3-row-padding w3-margin-top">
                                <div class="w3-quarter">
                                    <label for="suffix"><i class="fa fa-user"></i> Suffix</label>
                                    <input type="text" class="w3-input w3-border" id="suffix" name="suffix" value="<?php echo htmlspecialchars($student['suffix']); ?>">
                                </div>
                                <div class="w3-quarter">
                                    <label for="date_of_birth"><i class="fa fa-calendar"></i> Date of Birth</label>
                                    <input type="date" class="w3-input w3-border" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>" required>
                                </div>
                                <div class="w3-half">
                                    <label for="email"><i class="fa fa-envelope"></i> Email</label>
                                    <input type="email" class="w3-input w3-border" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                </div>
                            </div>

                            <div class="w3-row-padding w3-margin-top">
                                <div class="w3-full">
                                    <label for="address"><i class="fa fa-home"></i> Address</label>
                                    <textarea class="w3-input w3-border" id="address" name="address" rows="3" required><?php echo htmlspecialchars($student['address']); ?></textarea>
                                </div>
                            </div>

                            <div class="w3-row-padding w3-margin-top">
                                <div class="w3-half">
                                    <label for="educational_attainment"><i class="fa fa-graduation-cap"></i> Educational Attainment</label>
                                    <select class="w3-input w3-border" id="educational_attainment" name="educational_attainment" required>
                                        <option value="Elementary" <?php echo $student['educational_attainment'] == 'Elementary' ? 'selected' : ''; ?>>Elementary</option>
                                        <option value="High School" <?php echo $student['educational_attainment'] == 'High School' ? 'selected' : ''; ?>>High School</option>
                                        <option value="Senior High School" <?php echo $student['educational_attainment'] == 'Senior High School' ? 'selected' : ''; ?>>Senior High School</option>
                                        <option value="College" <?php echo $student['educational_attainment'] == 'College' ? 'selected' : ''; ?>>College</option>
                                        <option value="Masters" <?php echo $student['educational_attainment'] == 'Masters' ? 'selected' : ''; ?>>Masters</option>
                                        <option value="Doctorate" <?php echo $student['educational_attainment'] == 'Doctorate' ? 'selected' : ''; ?>>Doctorate</option>
                                    </select>
                                </div>
                                <div class="w3-half">
                                    <label for="username"><i class="fa fa-id-badge"></i> Username</label>
                                    <input type="text" class="w3-input w3-border" value="<?php echo htmlspecialchars($student['username']); ?>" disabled>
                                </div>
                            </div>

                            <div class="w3-row-padding w3-margin-top w3-margin-bottom">
                                <div class="w3-col">
                                    <button type="submit" class="w3-button w3-red w3-block"><i class="fa fa-save"></i> Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- End Right Column -->
            </div>

            <!-- End Grid -->
        </div>

        <!-- End Page Container -->
    </div>

</body>

</html>