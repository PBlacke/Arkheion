<?php
session_start();
require '../config/connection.php';
require_once '../config/auth.php';
require '../includes/database.php';
require '../includes/validators.php'; // Include the validator file

requireRole(['student', 'admin', 'faculty']);

// Get messages from session and clear them
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html data-theme="ark">

<head>
    <title>Paper - Arkheion</title>
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen p-4 bg-base-200">
        <div class="grid grid-cols-dashboard gap-4 w-full">
            <?php include 'includes/nav.php'; ?>

            <div class="flex flex-col gap-4 p-8 bg-base-100 rounded-box shadow-lg">
                <div class="flex justify-between items-center w-full">
                    <h1 class="text-2xl font-bold">Submitted Paper</h1>
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

                <?php include '../papers/add_paper.php'; ?>
            </div>
        </div>
    </main>
</body>

</html>