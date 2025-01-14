<?php

header("Access-Control-Allow-Methods: GET");

include "../../header.php";
include "../../modules/leaveFunction.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    if (isset($_GET['relieve_id'])) {
        $relieveActivities = relieverActivities($_GET['relieve_id']);
        echo json_encode($relieveActivities);
    }
}



