<?php
// Include the connection file
require 'config/connection.php';

// Get admin info
$sql = "SELECT username, email FROM admin WHERE id = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $email = $row['email'];
} else {
    $username = "Default Username";
    $email = "Default Email";
}

$conn->close();
?>

<!-- Left Column -->
<div class="w3-third">
    <div class="w3-white w3-text-grey w3-card-4">
        <div class="w3-display-container">
            <div class="w3-display-bottomleft w3-container w3-text-black">
            </div>
        </div>
        <div class="w3-container">
            <p><i class="fa fa-briefcase fa-fw w3-margin-right w3-large w3-text-blue"></i><?php echo $username; ?></p>
            <p><i class="fa fa-home fa-fw w3-margin-right w3-large w3-text-blue"></i>Arkheion</p>
            <p><i class="fa fa-envelope fa-fw w3-margin-right w3-large w3-text-blue"></i><?php echo $email; ?></p>
            <hr>

            <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-blue"></i>Menu</b></p>

            <style>
                .list {
                    display: flex;
                    align-items: center;
                    text-decoration: none;
                    color: black;
                    padding: 5px;
                    transition: all 0.3s ease;
                }

                .list i {
                    margin-right: 8px;
                }

                /* Change hover style to target the anchor tag */
                a.list:hover {
                    background-color: #6b7b9e;
                    color: black;
                    padding: 5px;
                    border-radius: 10px;
                    text-decoration: none;
                }

                /* Add style for the paragraph inside the link */
                .list p {
                    margin: 0;
                    padding: 0;
                }
            </style>

            <style>
                .loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                }

                .loading-spinner {
                    border: 8px solid #f3f3f3;
                    border-top: 8px solid #3498db;
                    border-radius: 50%;
                    width: 50px;
                    height: 50px;
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    0% {
                        transform: rotate(0deg);
                    }

                    100% {
                        transform: rotate(360deg);
                    }
                }
            </style>

            <a href="javascript:void(0);" class="list" onclick="showLoading('dashboard.php')">
                <i class="fa fa-dashboard fa-fw"></i>
                <p class="list">Dashboard</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <a href="javascript:void(0);" class="list" onclick="showLoading('facultylist2.php')">
                <i class="fa fa-users fa-fw"></i>
                <p class="list">Staff List</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <a href="javascript:void(0);" class="list" onclick="showLoading('archivelist.php')">
                <i class="fa fa-archive fa-fw"></i>
                <p class="list">Archive List</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <br>

            <p class="w3-large"><b><i class="fa fa-asterisk fa-fw w3-margin-right w3-text-blue"></i>Settings</b></p>

            <a href="javascript:void(0);" class="list" onclick="showLoading('departmentlist.php')">
                <i class="fa fa-building fa-fw"></i>
                <p class="list">Department List</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <a href="javascript:void(0);" class="list" onclick="showLoading('curriculumlist.php')">
                <i class="fa fa-book fa-fw"></i>
                <p class="list">Program List</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <a href="javascript:void(0);" class="list" onclick="showLoading('setting.php')">
                <i class="fa fa-cogs fa-fw"></i>
                <p class="list">Settings</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <a href="javascript:void(0);" class="list" onclick="showLoading('records.php')">
                <i class="fa fa-archive fa-fw"></i>
                <p class="list">Records</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <a href="javascript:void(0);" class="list" onclick="showLoading('logout.php')">
                <i class="fa fa-sign-out fa-fw"></i>
                <p class="list">Logout</p>
            </a>
            <div class="w3-light-grey w3-round-xlarge w3-small"></div>

            <br>
        </div>
    </div><br>
</div>
<!-- End Left Column -->

<script>
    function showLoading(url) {
        // Create loading overlay
        var overlay = document.createElement('div');
        overlay.className = 'loading-overlay';

        var spinner = document.createElement('div');
        spinner.className = 'loading-spinner';

        overlay.appendChild(spinner);
        document.body.appendChild(overlay);

        // Redirect after a short delay
        setTimeout(function() {
            window.location.href = url;
        }, 500);
    }
</script>