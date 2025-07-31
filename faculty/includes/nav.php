<?php
require_once '../config/connection.php';
require_once '../includes/database.php';

$current_user = getLoggedInUser($db);
?>

<section class="flex flex-col w-full bg-base-100 rounded-box shadow-lg">
    <div class="flex flex-col px-5 py-2">
        <p class="text-2xl text-center font-bold my-2">Arkheion</p>
        <div class="flex gap-4">
            <div class="avatar avatar-placeholder justify-center py-2">
                <div class="bg-neutral text-neutral-content w-16 rounded-full">
                    <span class="text-3xl"><?php echo strtoupper(substr($current_user['username'], 0, 1)); ?></span>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <p><?php echo $current_user['username']; ?></p>
                <p><?php echo $current_user['email']; ?></p>
            </div>
        </div>
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
                    <a href="./students-list.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Student List</p>
                    </a>
                </li>
                <li>
                    <a href="./pending-students-list.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Pending Student List</p>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <p class="list">Archive List</p>
                    </a>
                </li>
            </ul>
            <ul class="menu rounded-box w-full">
                <li class="menu menu-title">Settings</li>
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