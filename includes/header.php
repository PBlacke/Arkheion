<header class="navbar bg-base-100 shadow-sm h-20">
    <a href="./index.php" class="navbar-start h-full">
        <img src="image/LOGO.png" class="h-full">
    </a>
    <form action="search4.php" method="GET" class="search-form navbar-center">
        <input type="search" name="searchbar" placeholder="Search by title..." id="search-box">
        <button type="submit" class="fas fa-search" aria-label="Search"></button>
    </form>
    <div class="navbar-end">
        <div id="search-btn" style="color: #0c1776;" class="fas fa-search"></div>
        <div id="login-btn" style="color: #0c1776;"><a href='./auth/login.php'>Login</a></div>
    </div>
</header>