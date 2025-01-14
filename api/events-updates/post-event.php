<?php
include "../../header.php";
include "../../modules/eventFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];
if (isset($_POST)) {
    $body = file_get_contents("php://input");
    if ($body) {
       $eventForm = postEvent(json_decode($body, true));
    }
    print_r($eventForm);
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}