<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="./css/output.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Arkheion</title>
</head>

<body>

    <header class="header">
        <div class="header-1">
            <a href="" class="logo"><img src="image/LOGO.png" style="height: 100px;"></a>
            <form action="search4.php" method="GET" class="search-form">
                <input type="search" name="searchbar" placeholder="Search by title..." id="search-box">
                <button type="submit" class="fas fa-search" aria-label="Search"></button>
            </form>
            <div class="icons">
                <div id="search-btn" style="color: #0c1776;" class="fas fa-search"></div>
                <div id="login-btn" style="color: #0c1776;"><a href='adminlogin.php'>admin login</a></div>
            </div>
            <div class="icons">
                <div id="login-btn" style="color: #0c1776;"><a href='facultylogin.php'>staff login</a></div>
            </div>
            <div class="icons">
                <div id="login-btn" style="color: #0c1776;"><a href='studentlogin.php'>student login</a></div>
            </div>
        </div>
        <div class="header-2">
            <!-- <nav class="navbar">
        <a href="index.php">HOME</a>
      </nav> -->
        </div>
    </header>

    <div class="login-form-container">
        <div id="close-login-btn" class="fas fa-times">

        </div>
    </div>

    <section class="home" id="home">

        <div class="row">

            <div class="content bg-orange-400">
                <h3>Arkheion</h3>
                <p style="margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;">
                    A Comprehensive Research Repository with Intelligent Analytics

            </div>

            <!-- Assuming you have established a database connection in your config/connection.php file -->
            <?php require 'config/connection.php'; ?>

            <div class="swiper books-slider">
                <div class="swiper-wrapper">
                    <?php
                    // Fetch image paths from the database
                    $sql = "SELECT image FROM files";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $imagePath = $row['image'];
                            echo '<a href="#" class="swiper-slide"><img src="' . $imagePath . '" alt=""></a>';
                        }
                    } else {
                        echo '<p>No images found.</p>';
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </div>
                <img src="image/stand.png" alt="" class="stand">
            </div>

        </div>

    </section>

    <!-- featured section start -->

    <!-- Assuming you have established a database connection in your config/connection.php file -->
    <?php require 'config/connection.php'; ?>

    <section class="featured" id="featured">
        <h1 class="heading">
            <span>featured projects</span>
        </h1>

        <div class="swiper featured-slider">
            <div class="swiper-wrapper">
                <?php
                // Fetch project details from the database with status 'Published'
                $sql = "SELECT * FROM files WHERE status = 'Published'";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="swiper-slide box">';
                        echo '<div class="icons">';
                        echo '<a href="view_file.php?id=' . $row['id'] . '" class="fas fa-eye" target="_blank" id="convertBtn"></a>';
                        echo '<a href="download.php?id=' . $row['id'] . '" class="fas fa-download"></a>';
                        echo '</div>';
                        echo '<div class="image">';
                        echo '<img src="' . $row['image'] . '" alt="">';
                        echo '</div>';
                        echo '<div class="content">';
                        echo '<h3>' . $row['title'] . '</h3>';
                        echo '<p><strong>Author:</strong> ' . htmlspecialchars($row['uploader']) . '</p>';
                        echo '<p><strong>Department:</strong> ' . htmlspecialchars($row['department']) . '</p>';
                        echo '<p><strong>Year:</strong> ' . htmlspecialchars($row['year']) . '</p>';
                        echo '<a href="download.php?id=' . $row['id'] . '" class="btn">download</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No published projects found.</p>';
                }

                // Close the database connection
                $conn->close();
                ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- featured section end -->

    <?php include 'includes/footer.php' ?>

    <div class="loader-container">
        <img src="image/loading.gif" alt="">

    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script src="script.js"></script>

    <style>
        .featured .featured-slider .box .content p {
            font-size: 14px;
            color: #666;
            padding: 5px 0;
            line-height: 1.3;
        }

        .featured .featured-slider .box .content p strong {
            color: #0c1776;
        }
    </style>

    <script>
        var swiper = new Swiper(".featured-slider", {
            spaceBetween: 10,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 9500,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                0: {
                    slidesPerView: 1,
                },
                450: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 4,
                },
            },
        });
    </script>

</body>

</html>