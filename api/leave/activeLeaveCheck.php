<?php

require "../../header.php";
require "../../modules/leaveFunction.php";

header("Access-Control-Allow-Methods: GET");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    $activeLeave = activeLeave();
    echo json_encode($activeLeave);
}
