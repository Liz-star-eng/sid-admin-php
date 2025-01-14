<?php
include "../../header.php";
include "../../modules/eventFunction.php";
header('Access-Control-Allow-Methods: GET');

$requestMethod = $_SERVER["REQUEST_METHOD"];


header("Access-Control-Allow-Methods: GET");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    $getAllEvents = getAllEvent();
    echo json_encode($getAllEvents);
}