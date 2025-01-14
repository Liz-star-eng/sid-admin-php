<?php
header("Access-Control-Allow-Methods: GET");

require "../../header.php";
require "../../modules/employeeFunction.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    $getAllEmployees = getAllEmployees();
    echo json_encode($getAllEmployees);
}

