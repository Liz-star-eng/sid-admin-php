<?php

require "../../header.php";
require "../../modules/requestFunction.php";

header("Access-Control-Allow-Methods: GET");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    $totalRequest = getTotalRequest();
    echo json_encode($totalRequest);
}
