<?php
header("Access-Control-Allow-Methods: GET");

include "../../header.php";
include "../../modules/leaveFunction.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    if (isset($_GET['id'])) {
        $leaveActivities = leaveActivities($_GET['id']);
        echo json_encode($leaveActivities);
    }
}