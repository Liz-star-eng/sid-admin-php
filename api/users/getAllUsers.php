<?php

require "../../header.php";
require "../../modules/usersFunction.php";

header("Access-Control-Allow-Methods: GET");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    $getUsers = getAllUsers();
    echo json_encode($getUsers);
}
