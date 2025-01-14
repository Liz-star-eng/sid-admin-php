<?php
include "../../header.php";
include "../../modules/employeeFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];


if ($requestMethod === 'POST') {
    if (!empty($_FILES) && !empty($_POST)) {
        $uploadDirectory = '../../uploads/';
        $uploadedFileName = $_FILES['image']['name'];
        $tempFilePath = $_FILES['image']['tmp_name'];
        $destination = $uploadDirectory . $uploadedFileName;
        $relativeImagePath = 'uploads/' . $uploadedFileName;

        if (move_uploaded_file($tempFilePath, $destination)) {
            $imagePath = $relativeImagePath;
             
            // Parse JSON data from form fields
            $personalInfo = json_decode($_POST['personalInfo'], true);
            $contactInfo = json_decode($_POST['contactInfo'], true);
            $jobInfo = json_decode($_POST['jobInfo'], true);
            $bankDetails = json_decode($_POST['bankDetails'], true);
            $emergencyContact = json_decode($_POST['emergencyContact'], true);
            if (
                json_last_error() === JSON_ERROR_NONE &&
                !is_null($personalInfo) &&
                !is_null($contactInfo) &&
                !is_null($jobInfo) &&
                !is_null($bankDetails) &&
                !is_null($emergencyContact)
            ) {
                $requestParams = [
                    'personalInfo' => $personalInfo,
                    'contactInfo' => $contactInfo,
                    'jobInfo' => $jobInfo,
                    'bankDetails' => $bankDetails,
                    'emergencyContact' => $emergencyContact,
                    'image' => $imagePath
                ];
                $addEmployee = addEmployee($requestParams);
                echo json_encode(['status' => 200, 'message' => 'Employee added successfully']);
            } else {
                echo json_encode(['status' => 400, 'message' => 'Invalid JSON data']);
            }
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to move uploaded image.']);
        }
    } else {
        echo json_encode(['status' => 400, 'message' => 'No files uploaded or form data missing']);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}