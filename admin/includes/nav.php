<?php
// Include the connection file
require_once '../config/connection.php';
require_once '../includes/database.php';

$current_user = getLoggedInUser($db);
?>

<section class="flex flex-col w-full bg-base-100 rounded-box shadow-lg">
    <div class="flex flex-col px-5 py-2">
        <p class="text-2xl text-center font-bold my-2">Arkheion</p>
        <div class="avatar avatar-placeholder justify-center py-2">
            <div class="bg-neutral text-neutral-content w-20 rounded-full">
                <span class="text-3xl">D</span>
            </div>
        </div>
        <p><i class="fa fa-briefcase fa-fw w3-margin-right w3-large w3-text-blue"></i><?php echo $current_user['username']; ?></p>
        <p><i class="fa fa-envelope fa-fw w3-margin-right w3-large w3-text-blue"></i><?php echo $current_user['email']; ?></p>
    </div>
    <div class="flex flex-col justify-between grow">
        <div class="flex flex-col">
            <ul class="menu rounded-box w-full">
                <li class="menu menu-title">Menu</li>
                <li>
                    <a href="dashboard.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Dashboard</p>
                    </a>
                </li>
                <li>
                    <a href="./faculty-list.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Faculty List</p>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Student List</p>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Papers List</p>
                    </a>
                </li>
            </ul>
            <ul class="menu rounded-box w-full">
                <li class="menu menu-title">Settings</li>
                <li>
                    <a href="./department-list.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Department List</p>
                    </a>
                </li>
                <li>
                    <a href="./department-list.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Schools List</p>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Settings</p>
                    </a>
                </li>
            </ul>
        </div>
        <div class="m-4">
            <a href="../auth/logout.php" class="btn btn-primary w-full">
                <i class="fa fa-dashboard fa-fw"></i>
                <p class="list">Logout</p>
            </a>
        </div>
    </div>
</section>