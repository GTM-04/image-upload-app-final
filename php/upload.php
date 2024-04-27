<?php
error_reporting(0);

$msg = "";


if (isset($_FILES['uploadfile'])) {
   
    $filename = $_FILES["uploadfile"]["name"];
    $tempname = $_FILES["uploadfile"]["tmp_name"];
    $folder = "./image/" . $filename;
    $thumbnailFolder = "./image/thumbnails/" . $filename;
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); 

   
    if (!in_array($filetype, array('jpg', 'jpeg', 'png', 'gif'))) {
        $msg = "Invalid file format! Please upload JPG, PNG, or GIF files only.";
    } else {
       
        $db = mysqli_connect("localhost", "root", "", "uploadjnrdev");

        
        $sql = "INSERT INTO image (filename, file_type) VALUES ('$filename', '$filetype')";
        mysqli_query($db, $sql);

    
        if (move_uploaded_file($tempname, $folder)) {
            
            createThumbnail($folder, $thumbnailFolder, 150, 150);
            $msg = "Image uploaded successfully!";
        } else {
            $msg = "Failed to upload image!";
        }
    }
}


$db = mysqli_connect("localhost", "root", "", "uploadjnrdev");
$filter = isset($_GET['filetype']) ? mysqli_real_escape_string($db, $_GET['filetype']) : '';
$query = "SELECT * FROM image";
if (!empty($filter)) {
    $query .= " WHERE file_type = '$filter'";
}
$result = mysqli_query($db, $query);

$images = array();
while ($data = mysqli_fetch_assoc($result)) {
    $image = [
        'filename' => $data['filename'],
        'thumbnailUrl' => 'http://localhost/image/thumbnails/' . $data['filename'],
        'fullSizeUrl' => 'http://localhost/image/' . $data['filename']
    ];
    $images[] = $image;
}

$response = array(
    'msg' => $msg,
    'images' => $images
);

header('Content-Type: application/json');
echo json_encode($response);

/**
 * Function to create a thumbnail of an image
 *
 * @param string $sourcePath The path of the source image
 * @param string $destinationPath The path to save the thumbnail image
 * @param int $thumbWidth The width of the thumbnail
 * @param int $thumbHeight The height of the thumbnail
 */
function createThumbnail($sourcePath, $destinationPath, $thumbWidth, $thumbHeight)
{
    list($width, $height) = getimagesize($sourcePath);
    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    $source = imagecreatefromjpeg($sourcePath);

    imagecopyresized($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

    imagejpeg($thumb, $destinationPath);
}
?>