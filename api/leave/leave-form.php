<?php
include "../../header.php";
include "../../modules/leaveFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(isset($_POST)) {
    $body = file_get_contents("php://input");
    if($body){
        $createdForm = postLeaveForm(json_decode($body, true));
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}