<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "dbHenrichFoodCorps";
$port = 3306;

// create connection
$conn = new mysqli($hostname, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}
error_log("Connected to database successfully");

