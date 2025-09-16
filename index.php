<!DOCTYPE html>
<html lang="en" data-theme="ark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <link rel="stylesheet" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="./css/output.css">
    <title>Arkheion</title>
</head>

<body>
    <?php include 'includes/header.php' ?>

    <div class="hero min-h-screen" style="background-image: url(/Arkheion/image/background/hero-background.jpg);">
        <div class="hero-overlay"></div>
        <div class="hero-content text-neutral-content text-center">
            <div class="max-w-md">
                <h1 class="text-5xl font-bold">Arkheion</h1>
                <p class="py-6">
                    A Comprehensive Research Repository with Intelligent Analytics.
                </p>
                <button class="btn btn-primary">Get Started</button>
            </div>
        </div>
    </div>

    <section class="flex bg-base-200">
        <div class="row">
            <div class="bg-primary">
                <h3>Arkheion</h3>
                <p>A Comprehensive Research Repository with Intelligent Analytics</p>
            </div>

            <div class="swiper books-slider">
                <?php require 'config/connection.php'; ?>
                <!-- Assuming you have established a database connection in your config/connection.php file -->
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
                <img src="image/stand.png" alt="" class="">
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

    <?php include 'includes/footer.php' ?>
</body>

</html>