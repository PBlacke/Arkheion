<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: studentlogin.php");
    exit();
}

// Include the connection file
require 'connection.php';

// Get student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT username, email, department, curriculum, first_name, last_name, suffix, date_of_birth, phone_number FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    // Handle error - student not found
    header("Location: logout.php");
    exit();
}

// Get papers from student's department and curriculum
$papers_query = $conn->prepare("SELECT * FROM files WHERE department = ? AND curriculum = ? AND status = 'Published' ORDER BY created_at DESC");
$papers_query->bind_param("ss", $student['department'], $student['curriculum']);
$papers_query->execute();
$papers_result = $papers_query->get_result();

$papers = [];
while ($row = $papers_result->fetch_assoc()) {
    $papers[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Arkheion - Student Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="x-icon" href="LOGO.png">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
        .paper-card {
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .paper-card:hover {
            transform: translateY(-5px);
        }
        .search-box {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body class="w3-light-grey">

<!-- Page Container -->
<div class="w3-content w3-margin-top" style="max-width:1400px;">

  <!-- The Grid -->
  <div class="w3-row-padding">
  
    <!-- Left Column -->
    <div class="w3-third">
    
      <div class="w3-white w3-text-grey w3-card-4">
        <div class="w3-container">
          <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user fa-fw w3-margin-right w3-xxlarge w3-text-#0c1776"></i>Student Profile</h2>
          <p><i class="fa fa-user fa-fw w3-margin-right w3-large w3-text-#0c1776"></i><?php echo htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) . ($student['suffix'] ? ' ' . htmlspecialchars($student['suffix']) : ''); ?></p>
          <p><i class="fa fa-id-badge fa-fw w3-margin-right w3-large w3-text-#0c1776"></i><?php echo htmlspecialchars($student['username']); ?></p>
          <p><i class="fa fa-envelope fa-fw w3-margin-right w3-large w3-text-#0c1776"></i><?php echo htmlspecialchars($student['email']); ?></p>
          <p><i class="fa fa-phone fa-fw w3-margin-right w3-large w3-text-#0c1776"></i><?php echo htmlspecialchars($student['phone_number']); ?></p>
          <p><i class="fa fa-calendar fa-fw w3-margin-right w3-large w3-text-#0c1776"></i><?php echo date('F j, Y', strtotime($student['date_of_birth'])); ?></p>
          <p><i class="fa fa-building fa-fw w3-margin-right w3-large w3-text-#0c1776"></i><?php echo htmlspecialchars($student['department']); ?></p>
          <p><i class="fa fa-book fa-fw w3-margin-right w3-large w3-text-#0c1776"></i><?php echo htmlspecialchars($student['curriculum']); ?></p>
          <hr>

          <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-#0c1776"></i>Options</b></p>
          
          <a href="student_dashboard.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-home fa-fw"></i> Dashboard</a>
          <a href="student_papers.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-book fa-fw"></i> My Papers</a>
          <a href="student_profile.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-user fa-fw"></i> Profile</a>
          <a href="student_settings.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-cog fa-fw"></i> Settings</a>
          <a href="logout.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
          <br>
        </div>
      </div><br>

    <!-- End Left Column -->
    </div>

    <!-- Right Column -->
    <div class="w3-twothird">
    
      <div class="w3-container w3-card w3-white w3-margin-bottom">
        <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-book fa-fw w3-margin-right w3-xxlarge w3-text-#0c1776"></i>Available Papers</h2>
        
        <div class="w3-container">
            <input type="text" id="searchInput" class="search-box" placeholder="Search papers...">
            
            <div id="papersList">
                <?php foreach ($papers as $paper): ?>
                    <div class="w3-container w3-card w3-white w3-margin-bottom paper-card">
                        <h3><?php echo htmlspecialchars($paper['title']); ?></h3>
                        <p class="w3-opacity"><b>Author:</b> <?php echo htmlspecialchars($paper['uploader']); ?></p>
                        <p><b>Description:</b> <?php echo htmlspecialchars($paper['description']); ?></p>
                        <p><b>Year:</b> <?php echo htmlspecialchars($paper['year']); ?></p>
                        <div class="w3-row">
                            <div class="w3-col m6">
                                <a href="view_file.php?id=<?php echo $paper['id']; ?>" class="w3-button w3-red" target="_blank">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            </div>
                            <div class="w3-col m6">
                                <a href="download.php?id=<?php echo $paper['id']; ?>" class="w3-button w3-green">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
      </div>

    <!-- End Right Column -->
    </div>
    
  <!-- End Grid -->
  </div>
  
  <!-- End Page Container -->
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = this.value.toLowerCase();
    var papers = document.getElementById('papersList').getElementsByClassName('paper-card');
    
    for (var i = 0; i < papers.length; i++) {
        var title = papers[i].getElementsByTagName('h3')[0].textContent.toLowerCase();
        var description = papers[i].getElementsByTagName('p')[1].textContent.toLowerCase();
        
        if (title.includes(input) || description.includes(input)) {
            papers[i].style.display = "";
        } else {
            papers[i].style.display = "none";
        }
    }
});
</script>

</body>
</html> 