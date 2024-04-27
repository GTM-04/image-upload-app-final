<?php
error_reporting(0);

$msg = "";

// If upload button is clicked ...
if (isset($_POST['upload'])) {

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
?>
<!DOCTYPE html>
<html>

<head>
    <title>Image Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
    <div id="content">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <input class="form-control" type="file" name="uploadfile" value="" />
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="upload">UPLOAD</button>
            </div>
        </form>
        <?php if (!empty($msg)) { ?>
            <div class="alert alert-info" role="alert">
                <?php echo $msg; ?>
            </div>
        <?php } ?>
        <!-- Filter images by type -->
        <form method="GET" action="">
            <div class="form-group">
                <label for="filetype">Filter by File Type:</label>
                <select class="form-control" name="filetype" id="filetype">
                    <option value="">All</option>
                    <option value="jpg">JPG</option>
                    <option value="jpeg">JPEG</option>
                    <option value="png">PNG</option>
                    <option value="gif">GIF</option>
                </select>
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </form>
    </div>
    <div id="display-image">
        <?php
        $db = mysqli_connect("localhost", "root", "", "uploadjnrdev");
        $filter = isset($_GET['filetype']) ? mysqli_real_escape_string($db, $_GET['filetype']) : '';
        $query = "SELECT * FROM image";
        if (!empty($filter)) {
            $query .= " WHERE file_type = '$filter'";
        }
        $result = mysqli_query($db, $query);

        while ($data = mysqli_fetch_assoc($result)) {
            ?>
            <!-- Display thumbnails with a link to view larger image -->
            <a href="./image/<?php echo $data['filename']; ?>" target="_blank">
                <img src="./image/<?php echo $data['filename']; ?>" width="150" height="150">
            </a>
        <?php
        }
        ?>
    </div>
</body>

</html>
