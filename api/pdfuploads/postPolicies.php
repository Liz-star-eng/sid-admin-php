<?php
include "../../header.php";
include "../../modules/pdfUploadFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];


if ($requestMethod === 'POST') {
    if (isset($_FILES['file']) && isset($_POST['title'])) {
        try {
            $policiesUpload = uploadPolicies($_POST, $_FILES['file']);
            echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request: missing file or other parameters']);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
