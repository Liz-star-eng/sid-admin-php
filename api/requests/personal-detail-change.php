<?php
require "../../header.php";
require "../../modules/requestFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === 'POST') {
    $body = file_get_contents("php://input");
    if ($body) {
        $postForm = personalDetailChange(json_decode($body, true));
        echo "Success";
    }else{
        echo json_encode(['status' => 400, 'message' => 'No data provided']);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}