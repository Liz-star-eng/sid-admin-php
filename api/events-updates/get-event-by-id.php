<?php
include "../../header.php";
include "../../modules/eventFunction.php";
header('Access-Control-Allow-Methods: GET');

$requestMethod = $_SERVER["REQUEST_METHOD"];


header("Access-Control-Allow-Methods: GET");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
if (isset($_GET['id'])) {
$event = getEventById($_GET['id']);
echo json_encode($event);
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