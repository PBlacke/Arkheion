<?php
$host = "localhost"; // e.g., "localhost" or "127.0.0.1"
$username = "root";
$password = "admin520";
$database = "arkheion";

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally, set the character set to utf8
$conn->set_charset("utf8");
