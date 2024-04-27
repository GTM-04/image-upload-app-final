<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uploadjnrdev";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve images from the database
$sql = "SELECT * FROM image";
$result = $conn->query($sql);

$images = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $image = array(
            'filename' => $row['filename'],
            'file_type' => $row['file_type']
        );
        array_push($images, $image);
    }
}

$conn->close();

echo json_encode($images);
?>