<?php
include "../../header.php";
include "../../modules/leaveFunction.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "PUT") {
    $body = file_get_contents("php://input");
    if ($body) {
        $body = json_decode($body, true);
        $leave_id = $body['leave_id'];
        $reliever_id = $body['reliever_id'];
        $approver_id = $body['approver_id'];
        $rejecter_id = $body['approver_id'];
        $status = $body['status'];

        if ($status == 'Authorize'
        ) {
            $leaveData = authorizeLeaveStatus($reliever_id, $leave_id);
            echo json_encode('Success');
        } else if ($status == 'Approve') {
            $leaveData = approveLeaveStatus($leave_id, $approver_id);
        } else if ($status == 'Reject') {
            $leaveData = rejectLeaveStatus($reliever_id, $leave_id, $rejecter_id);
        } else {
            $leaveData = declineLeaveStatus($reliever_id, $leave_id);
        }
    } else {

        $data = [
            'status' => 400,
            'message' => 'Request body required',
        ];
        header("HTTP/1.0 400 Bad request");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
