<?php
error_reporting(0);

$msg = "";

// If upload button is clicked ...
if (isset($_FILES['uploadfile'])) {
    // Get file details
    $filename = $_FILES["uploadfile"]["name"];
    $tempname = $_FILES["uploadfile"]["tmp_name"];
    $folder = "./image/" . $filename;
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); // Get file type

    // Check if the file type is allowed
    if (!in_array($filetype, array('jpg', 'jpeg', 'png', 'gif'))) {
        $msg = "Invalid file format! Please upload JPG, PNG, or GIF files only.";
    } else {
        // Database connection
        $db = mysqli_connect("localhost", "root", "", "uploadjnrdev");

        // Insert file details into the database
        $sql = "INSERT INTO image (filename, file_type) VALUES ('$filename', '$filetype')";
        mysqli_query($db, $sql);

        // Move the uploaded image into the folder: image
        if (move_uploaded_file($tempname, $folder)) {
            $msg = "Image uploaded successfully!";
        } else {
            $msg = "Failed to upload image!";
        }
    }
}

// Retrieve images from the database
$db = mysqli_connect("localhost", "root", "", "uploadjnrdev");
$filter = isset($_GET['filetype']) ? mysqli_real_escape_string($db, $_GET['filetype']) : '';
$query = "SELECT * FROM image";
if (!empty($filter)) {
    $query .= " WHERE file_type = '$filter'";
}
$result = mysqli_query($db, $query);

$images = array();
while ($data = mysqli_fetch_assoc($result)) {
    $images[] = $data;
}

$response = array(
    'msg' => $msg,
    'images' => $images
);

header('Content-Type: application/json');
echo json_encode($response);
?>