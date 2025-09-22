<?php
require './config/connection.php';
require_once './config/auth.php';
require_once 'database.php';

session_start();

$loggedInUser = getLoggedInUser($db);

if (isset($_POST['dashboardButton']) && $loggedInUser) {
    redirectToDashboard($loggedInUser['role']);
}
?>

<header class="navbar bg-base-100 shadow-sm h-14">
    <a href="./index.php" class="navbar-start h-full">
        <img src="image/LOGO.png" class="h-full">
    </a>
    <form action="search4.php" method="GET" class="search-form navbar-center">
        <input type="search" name="searchbar" placeholder="Search by title..." id="search-box">
        <button type="submit" class="fas fa-search" aria-label="Search"></button>
    </form>
    <?php if ($loggedInUser): ?>
        <div class="navbar-end gap-2">
            <form method="post">
                <button type="submit" name="dashboardButton" class="btn btn-primary">Dashbaord</button>
            </form>
            <a href="./auth/logout.php" class="btn btn-primary">Logout</a>
        </div>
    <?php else: ?>
        <div class="navbar-end">
            <a href='./auth/login.php' class="btn btn-primary rounded-full">Login</a>
        </div>
    <?php endif; ?>
</header>