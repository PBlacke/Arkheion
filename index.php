<!DOCTYPE html>
<html lang="en" data-theme="ark">

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
    <!-- header -->
    <?php include 'includes/header.php' ?>

    <section class="home bg-base-200" id="home">
        <div class="row">
            <div class="content bg-primary">
                <h3>Arkheion</h3>
                <p style="margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;">A Comprehensive Research Repository with Intelligent Analytics</p>
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

    <!-- footer -->
    <?php include 'includes/footer.php' ?>

</body>

</html>