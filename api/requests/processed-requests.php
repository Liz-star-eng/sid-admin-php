<?php
require "../../header.php";
require "../../modules/requestFunction.php";
header("Access-Control-Allow-Methods: GET");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    if (isset($_GET)) {
        $request = getProcessedRequests();
        echo json_encode($request);
    } else {
        $data = [
            'status' => 422,
            'message' => 'ID required',
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
