<!DOCTYPE html>
<html>
<head>
<title>Arkheion</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" type="x-icon" href="LOGO.png">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

<!-- Navbar (sit on top) -->
<div class="w3-top">
  <div class="w3-bar w3-white w3-wide w3-padding w3-card">
    <a href="index.php" class="w3-bar-item w3-button"><b>Arkheion</b> Online Archiving System</a>
    <!-- Float links to the right. Hide them on small screens -->
    <div class="w3-right w3-hide-small">
      <a href="facultylogin.php" class="w3-bar-item w3-button">Faculty Login</a>
      <a href="adminlogin.php" class="w3-bar-item w3-button">Admin Login</a>
    </div>
  </div>
  <div class="w3-bar w3-white w3-wide w3-padding w3-card">
    <form action="/search" method="GET">
        <input type="text" name="query" placeholder="Search..." class="w3-bar-item w3-input">
        <button type="submit" class="w3-bar-item w3-button"><i class="fa fa-search"></i>Search</button>
    </form>
  </div>
</div>

<!-- Header -->
<!-- <header class="w3-display-container w3-content w3-wide" style="max-width:1500px;" id="home">
  <img class="w3-image" src="/w3images/architect.jpg" alt="Architecture" width="1500" height="800">
  <div class="w3-display-middle w3-margin-top w3-center">
    <h1 class="w3-xxlarge w3-text-white"><span class="w3-padding w3-black w3-opacity-min"><b>BR</b></span> <span class="w3-hide-small w3-text-light-grey">Architects</span></h1>
  </div>
</header> -->

<!-- Page content -->
<div class="w3-content w3-padding" style="max-width:1564px">

  <!-- Project Section -->
  <div class="w3-container w3-padding-32" id="projects">
    <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16">Projects</h3>
    <!-- Page content -->
    <div class="w3-row-padding w3-padding-16">
        <style>
        body {
            text-align: center;
        }

        .center {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10vh;
        }

        .department {
            font-size: 100px; /* Set your desired font size */
        }
    </style>

<div class="center">
        <?php
            // Connect to the database (Replace these credentials with your actual database credentials)
            require 'connection.php';

            // Fetch departments from the database
            $sql = "SELECT * FROM department";
            $result = $conn->query($sql);

            // Display departments with styling and delay
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="w3-quarter department">';
                    echo '<h1>' . $row['department'] . '</h1>';
                    // You can add more details or customize the display as needed
                    echo '</div>';
                }
            } else {
                echo "No departments found.";
            }

            // Close the database connection
            $conn->close();
        ?>
    </div>
    <br>
    <div style="text-align: center;">
        <iframe src="https://heyzine.com/flip-book/0f580f131e.html" width="800" height="600" frameborder="0" scrolling="no"></iframe>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var departments = document.querySelectorAll('.department');
            var index = 0;

            function displayDepartment() {
                // Hide all departments
                departments.forEach(function(department) {
                    department.style.display = 'none';
                });

                // Display the current department
                departments[index].style.display = 'block';

                // Increment index or reset to 0
                index = (index + 1) % departments.length;

                setTimeout(displayDepartment, 1500); // 1.5-second delay
            }

            displayDepartment(); // Start displaying departments
        });
    </script>
    </div>
  </div>

  <!-- About Section -->
  <!-- <div class="w3-container w3-padding-32" id="about">
    <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16">About</h3>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint
      occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
      laboris nisi ut aliquip ex ea commodo consequat.
    </p>
  </div> -->

  <div class="w3-row-padding w3-grayscale">
    
  </div>

  <!-- Contact Section -->
  <div class="w3-container w3-padding-32" id="contact">
    
  </div>
  
<!-- End page content -->
</div>

<!-- Footer -->
<!-- <footer class="w3-center w3-black w3-padding-16">
  <p>Powered by <a href="https://www.w3schools.com/w3css/default.asp" title="W3.CSS" target="_blank" class="w3-hover-text-green">w3.css</a></p>
</footer> -->

</body>
</html>
