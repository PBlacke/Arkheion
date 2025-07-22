<?php
// Start the session at the very beginning
session_start();

// Include the connection file
require 'connection.php';

// Initialize variables
$username = "";
$password = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input
    $username = trim($_POST["username"]); // Add trim to remove any whitespace
    $password = $_POST["password"];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password FROM faculty WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            // Authentication successful
            // Clear any existing session data
            session_unset();
            session_regenerate_id(true);

            // Store the employee_id in the session
            $_SESSION['employee_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['last_activity'] = time();

            // Redirect to fac_dash.php
            header("Location: fac_dash.php");
            exit();
        } else {
            // Authentication failed, display error message
            $error = "Wrong Password Or Username. Try Once Again.";
        }
    } else {
        // Authentication failed, display error message
        $error = "Wrong Password Or Username. Try Once Again.";
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">   
    <title>Arkheoin Staff Login</title>
  </head>

  <style>
    *{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    }
body{
    background-color: rgb(219, 226, 226);
}
.row{
    background-color: #fff;
    border-radius: 30px;
    box-shadow: 12px 12px 22px grey;
    height: 500px;
}
img{
    border-top-left-radius: 30px;
    border-bottom-left-radius: 30px;
}
.btn1{
    background-color: maroon;
    border: none;
    outline: none;
    height: 50px;
    width: 100%;
    color: white;
    border-radius: 4px;
    font-weight: bolder;
}
.btn1:hover{
    background-color: white;
    border:1px solid;
    color: black;
}


  </style>
  <body>

  <section class="Form my-4 mx-5">
    <div class="container">
        <div class="row no-gutters pb-5">
            <div class="col-lg-5">
                <img src="pc.jpg" class="img-fluid" alt="" style="width:1500px; height:500px;">
            </div>
            <div class="col-lg-7 px-5 pt-5">
                <h2 class="font-weight-bold py-3">Arkheion</h2>
                <h4>Staff Login</h4>
                <?php if (isset($error)) : ?>
                    <p class="error-msg"><?php echo $error; ?></p>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-row">
                        <div class="col-lg-7">
                            <input type="text" class="form-control my-3 p-4" name="username" placeholder="Username">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-7">
                            <input type="password" class="form-control my-3 p-4" name="password" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-7">
                            <button type="submit" class="btn1 mt-e mb-5">LOGIN</button>
                    </div>
                </div>
                    <a href="index.php"><-- Back to Homepage</a>
                </form>
            </div>
        </div>
    </div>
  </section>
    

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>