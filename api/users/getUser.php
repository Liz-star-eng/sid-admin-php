<?php
header("Access-Control-Allow-Methods: GET");

require "../../header.php";
require "../../modules/usersFunction.php";


$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    if (isset($_GET['staff_id'])) {
        $user = getUser($_GET['staff_id']);
        echo json_encode($user);
    } else {
        $data = [
            'status' => 422,
            'message' => 'username required',
        ];
        header("HTTP/1.0 422 Info Required");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

