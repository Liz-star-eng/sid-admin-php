<?php
include "../../header.php";
include "../../modules/employeeFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];


if ($requestMethod === 'POST') {
    if (!empty($_FILES) && !empty($_POST['staff_id'])) {
        $uploadDirectory = '../../uploads/';
        $uploadedFileName = $_FILES['image']['name'];
        $tempFilePath = $_FILES['image']['tmp_name'];
        $destination = $uploadDirectory . $uploadedFileName;
        $staff_id = $_POST['staff_id'];


        // Relative path to store in the database
        $relativeImagePath = 'uploads/' . $uploadedFileName;

        if (move_uploaded_file($tempFilePath, $destination)) {
            $imagePath = $destination;

            $updateResult = updateProfilePicture($staff_id, $relativeImagePath);
            echo json_encode($updateResult);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to move uploaded image.']);
        }
    } else {
        echo json_encode(['status' => 400, 'message' => 'No files uploaded or staff ID missing']);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
