<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Arkheion</title>

</head>
<body>

  <header class="header">
   
  <div class="header-1">
      <a href="index.php" class="logo"><img src="image/LOGO.png" style="height: 70px;"></a>

        <div class="icons">
          <div id="search-btn" style="color: red;" class="fas fa-search"></div>
          <div id="login-btn" style="color: red;" ><a href='adminlogin.php'>Admin login</a></div>
        </div>
        <div class="icons">
          <div id="login-btn" style="color: red;" ><a href='facultylogin.php'>Staff Login</a></div>
        </div>
    </div>


    <div class="header-2">
      <!-- <nav class="navbar">
        <a href="index.php">HOME</a>
        <a href="facultylogin.php">FACULTY LOGIN</a>
        <a href="adminlogin.php">ADMIN LOGIN</a>
      </nav> -->
    </div>

  </header>

  <div class="login-form-container">

    <div id="close-login-btn" class="fas fa-times">

    </div>

  </div>

  <section class="home">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>

    <?php
        // Include the connection file
        require 'connection.php';

        // Fetch department options from the database
        $departmentQuery = "SELECT DISTINCT department FROM department";
        $departmentResult = $conn->query($departmentQuery);

        // Fetch curriculum options from the database
        $curriculumQuery = "SELECT DISTINCT curriculum FROM curriculum";
        $curriculumResult = $conn->query($curriculumQuery);
    ?>

    <div class="container">
        <div class="col-md-6 mx-auto">
            <form action="search5.php" method="post">
                <div class="form-group">
                    <label for="department">Department:</label>
                    <select class="form-control" id="department" name="department" required>
                        <?php
                        // Loop through department options and populate the dropdown
                        while ($departmentRow = $departmentResult->fetch_assoc()) {
                            echo '<option value="' . $departmentRow['department'] . '">' . $departmentRow['department'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="curriculum">Curriculum:</label>
                    <select class="form-control" id="curriculum" name="curriculum" required>
                        <?php
                        // Loop through curriculum options and populate the dropdown
                        while ($curriculumRow = $curriculumResult->fetch_assoc()) {
                            echo '<option value="' . $curriculumRow['curriculum'] . '">' . $curriculumRow['curriculum'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="text" class="form-control" id="year" name="year" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

  </section>

  <section class="footer">
      <div class="box-container">
          <div class="box">
            <h3 style="color: #25ae60; font-size: 22px;">WE'RE LOCATED AT</h3>
            <a href="#" style="cursor:text;"><i class="fas fa-location-dot"></i> A. Bonifacio St., <br><br>Ormoc City, Leyte 6541 </a>
          </div>

          <div class="box">
            <h3 style="color: #25ae60; font-size: 22px;">Extras</h3>
            <a href="terms.php" target="_blank"><i class="fas fa-arrow-right"> </i> Privacy Policy</a>
            <a href="terms.php" target="_blank"><i class="fas fa-arrow-right"></i> Terms & Conditions</a>
            <a href="archive.php" target="_blank"><i class="fas fa-arrow-right"></i> Archive</a>
          </div>

          <div class="box">
            <h3 style="color: #25ae60; font-size: 22px;">Contact Us</h3>
            <a href="#" style="cursor:text;"><i class="fas fa-phone" style="color:#25ae60; font-size: 15px;"></i> +63 9*** ** ****</a>
            <a href="#" target="_blank"><i class="fas fa-envelope"></i>arkheion@sample.com</a>
          </div>
      </div>

      <div class="share">
            <h3>Follow Us</h3>
            <a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a>
            <a href="https://www.google.com/" target="_blank"><i class="fab fa-google-plus"></i></a>
            <a href="https://www.twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a>
          </div>

      <div class="credit">Created by <span>Donaire & Ubay</span> | All rights reserved.</div>
  </section>

<div class="loader-container">
  <img src="image/loading.gif" alt="">
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="script.js"></script>

</body>
</html>