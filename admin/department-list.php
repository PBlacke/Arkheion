<?php
session_start();
require '../config/connection.php';
require_once '../config/auth.php';
require '../includes/database.php';

$departments = $db->getDepartments();

requireRole(['admin']);
?>

<!DOCTYPE html>
<html data-theme="ark">

<head>
    <title>Department List - Arkheion</title>
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <main class="flex justify-center min-h-screen px-8 bg-base-200">
        <div class="grid grid-cols-dashboard gap-8 w-full max-w-[1440px]">
            <?php include './nav.php'; ?>

            <!-- first_name	middle_name	last_name	suffix	birthdate	address	department -->

            <div class="flex flex-col gap-4 bg-base-100">
                <button class="btn btn-primary">Add Department</button>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Head Faculty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($departments as $department) {
                                echo "<tr>";
                                echo "<td></td>"; // Placeholder for checkbox or action buttons
                                echo "<td>{$department['department_code']}</td>";
                                echo "<td>{$department['department_name']}</td>";
                                echo "<td>{$department['description']}</td>";
                                echo "<td>{$department['head_faculty_id']}</td>";
                                echo "<td>{$department['status']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>