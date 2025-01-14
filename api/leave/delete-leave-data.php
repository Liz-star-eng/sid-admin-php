<?php
include "../../header.php";
include "../../modules/leaveFunction.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "DELETE"){
    $body = file_get_contents("php://input");
    if($body){
        $body = json_decode($body, true);
        $leave_id = $body['leave_id'];
        if($leave_id !== ''){
            $deleteData = deleteLeaveFunction($leave_id);
        } else {
            echo 'cannot delete data';
        }

        
    }
}