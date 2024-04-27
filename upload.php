<?php
$db = mysqli_connect("localhost", "root", "", "uploadjnrdev");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = "";

    // Get file details
    $filename = $_FILES["uploadfile"]["name"];
    $tempname = $_FILES["uploadfile"]["tmp_name"];
    $folder = "./image/" . $filename;
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); // Get file type

    // Check if the file type is allowed
    if (!in_array($filetype, array('jpg', 'jpeg', 'png', 'gif'))) {
        $msg = "Invalid file format! Please upload JPG, PNG, or GIF files only.";
    } else {
        // Insert file details into the database
        $sql = "INSERT INTO image (filename, file_type) VALUES (?, ?)";
        $stmt = mysqli_prepare($db, $sql);
       ```php
        mysqli_stmt_bind_param($stmt, "ss", $filename, $filetype);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Move uploaded file to destination folder
        move_uploaded_file($tempname, $folder);

        // Create thumbnail
        $thumbnailPath = "./image/thumbnails/" . $filename;
        $thumbnailSize = 150;
        createThumbnail($folder, $thumbnailPath, $thumbnailSize, $thumbnailSize);

        $msg = "Image uploaded successfully!";
    }

    echo json_encode(['msg' => $msg]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filter = isset($_GET['filetype']) ? $_GET['filetype'] : '';

    $query = "SELECT * FROM image";
    if (!empty($filter)) {
        $query .= " WHERE file_type = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $filter);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($db, $query);
    }

    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }

    echo json_encode($images);
}

// Function to create a thumbnail using GD
function createThumbnail($sourcePath, $destinationPath, $thumbnailWidth, $thumbnailHeight)
{
    $sourceImage = imagecreatefromstring(file_get_contents($sourcePath));
    $sourceWidth = imagesx($sourceImage);
    $sourceHeight = imagesy($sourceImage);
    $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
    imagecopyresampled($thumbnailImage, $sourceImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $sourceWidth, $sourceHeight);
    imagejpeg($thumbnailImage, $destinationPath, 90);
    imagedestroy($thumbnailImage);
    imagedestroy($sourceImage);
}