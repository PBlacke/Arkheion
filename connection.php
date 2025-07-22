<?php

// // Database configuration
// $host = "localhost"; // e.g., "localhost" or "127.0.0.1"
// $username = "tatsnfqv_carrental";
// $password = ".V*_tv=)2u_V";
// $database = "tatsnfqv_new_db(final)";

$host = "localhost"; // e.g., "localhost" or "127.0.0.1"
$username = "root";
$password = "admin520";
$database = "new_db";

//Database configuration
// $host = "sql108.infinityfree.com"; // e.g., "localhost" or "127.0.0.1"
// $username = "if0_35572412";
// $password = "IkfcE60JtD";
// $database = "if0_35572412_new_db";

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally, set the character set to utf8
$conn->set_charset("utf8");

?>
