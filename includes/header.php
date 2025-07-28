<header class="flex w-full h-24 bg-base-100">
    <a href="./" class="logo">
        <img src="image/LOGO.png" class="h-full">
    </a>
    <form action="search4.php" method="GET" class="search-form">
        <input type="search" name="searchbar" placeholder="Search by title..." id="search-box">
        <button type="submit" class="fas fa-search" aria-label="Search"></button>
    </form>
    <div class="icons">
        <div id="search-btn" class="fas fa-search"></div>
        <button class="btn" id="login-btn">
            <a href='adminlogin.php'>Login</a>
        </button>
    </div>
    <div class="icons">
        <div id="login-btn" style="color: #0c1776;"><a href='facultylogin.php'>staff login</a></div>
    </div>
    <div class="icons">
        <div id="login-btn" style="color: #0c1776;"><a href='studentlogin.php'>student login</a></div>
    </div>
</header>
