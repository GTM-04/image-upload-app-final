<?php
$db = mysqli_connect("localhost", "root", "", "uploadjnrdev");
$filter = isset($_GET['filetype']) ? mysqli_real_escape_string($db, $_GET['filetype']) : '';
$query = "SELECT * FROM image";
if (!empty($filter)) {
    $query .= " WHERE file_type = '$filter'";
}
$result = mysqli_query($db, $query);

$images = [];
while ($data = mysqli_fetch_assoc($result)) {
    $image = [
        'filename' => $data['filename'],
        'thumbnailUrl' => 'http://localhost/image/thumbnails/' . $data['filename'],
        'fullSizeUrl' => 'http://localhost/image/' . $data['filename']
    ];
    $images[] = $image;
}

$response = [
    'images' => $images
];

header('Content-Type: application/json');
echo json_encode($response);