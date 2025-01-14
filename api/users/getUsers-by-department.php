<?php

header("Access-Control-Allow-Methods: GET");

require "../../header.php";
require "../../modules/usersFunction.php";


$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    if (isset($_GET['departmentName'])){
        $relievers = getUserByDepartment($_GET['departmentName']);
        echo json_encode($relievers);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

