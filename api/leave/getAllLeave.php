<?php

require "../../header.php";
require "../../modules/leaveFunction.php";

header("Access-Control-Allow-Methods: GET");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET"){
    $getAllLeave = getAllLeave();
    echo json_encode($getAllLeave);
}