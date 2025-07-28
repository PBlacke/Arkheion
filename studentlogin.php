<?php
// Include the connection file
require 'config/connection.php';

// Initialize variables
$username = "";
$password = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // First check pending_students table
    $stmt = $conn->prepare("SELECT * FROM pending_students WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['status'] === 'Pending') {
            $error = "Your registration is still pending approval.";
        } else if ($row['status'] === 'Rejected') {
            $error = "Your registration has been rejected. Please contact your department for more information.";
        }
    } else {
        // Check active students table
        $stmt = $conn->prepare("SELECT id, username, password FROM students WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['last_activity'] = time();
                header("Location: student_dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
    $stmt->close();
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="shortcut icon" type="x-icon" href="image/favicon.png">
    <title>Arkheion Student Login</title>
</head>

<style>
    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
    }

    body {
        background-color: rgb(219, 226, 226);
    }

    .row {
        background-color: #fff;
        border-radius: 30px;
        box-shadow: 12px 12px 22px grey;
        height: 500px;
    }

    img {
        border-top-left-radius: 30px;
        border-bottom-left-radius: 30px;
    }

    .btn1 {
        background-color: maroon;
        border: none;
        outline: none;
        height: 50px;
        width: 100%;
        color: white;
        border-radius: 4px;
        font-weight: bolder;
    }

    .btn1:hover {
        background-color: white;
        border: 1px solid;
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
                    <h4>Student Login</h4>
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
                        <div class="form-row">
                            <div class="col-lg-7">
                                <p>Don't have an account? <a href="student_registration.php">Register here</a></p>
                                <a href="index.php"><-- Back to Homepage</a>
                            </div>
                        </div>
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