<?php
session_start();
require '../config/connection.php';
require '../includes/database.php';
require '../config/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToDashboard($_SESSION['role']);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $auth_result = authenticateUser($db, $username, $password); // Pass $db, not $conn

        if ($auth_result['success']) {
            $profile = getUserProfile($db, $auth_result['user']['id'], $auth_result['user']['role']);
            createUserSession($auth_result['user'], $profile);
            redirectToDashboard($auth_result['user']['role']);
        } else {
            $error = $auth_result['message'];
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" data-theme="ark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="../css/output.css">
    <title>Arkheion Login</title>
</head>

<body>
    <main class="grid w-screen h-screen place-items-center bg-base-200">
        <section class="flex w-2/4 min-h-2/4 bg-base-100 shadow-lg rounded-box">
            <div class="w-2/6 overflow-auto rounded-l-box">
                <img src="../image/computer.jpg" class="h-full w-full object-cover" alt="computer">
            </div>
            <div class="w-4/6 px-16 py-4">
                <div class="flex flex-col gap-2 my-4">
                    <h2 class="text-4xl font-bold">Arkheion</h2>
                    <h4 class="text-2xl font-semibold">Login</h4>
                    <p class="text-base-content transpa">Access your account (Admin, Faculty, or Student)</p>
                </div>

                <?php if (!empty($error)): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <form action="./login.php" method="POST">
                    <div class="flex flex-col gap-3 max-w-2/3 my-4">
                        <input type="text" class="w-full input input-lg" name="username" placeholder="Username" required>
                        <input type="password" class="w-full input input-lg" name="password" placeholder="Password" required>
                        <button type="submit" class="w-full btn btn-primary text-primary-content">LOGIN</button>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-7">
                            <p>Student? <a href="student_registration.php" class="link link-primary ml-2">Register here</a></p>
                            <a href="index.php" class="link link-primary link-hover">← Back to Homepage</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>