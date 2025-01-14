<?php
require "../../header.php";
require "../../modules/pdfUploadFunction.php";
header("Access-Control-Allow-Methods: GET");

    $requestMethod = $_SERVER["REQUEST_METHOD"];
    if ($requestMethod == "GET") {
        $policies = getPolicies();
        echo json_encode($policies);
    }
 else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
