<?php
header("Access-Control-Allow-Methods: GET");

include "../../header.php";
include "../../modules/leaveFunction.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    if (isset($_GET['departmentName']) && !empty($_GET['departmentName'])) {
    $getLeave = getLeaveByDepartment($_GET['departmentName']);
    echo json_encode($getLeave);
    }
} else {
    
}