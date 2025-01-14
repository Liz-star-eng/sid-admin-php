<?php
header("Access-Control-Allow-Methods: GET");

include "../../header.php";
include "../../modules/leaveFunction.php";

// getting leave days
$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {

    if (isset($_GET['id'])) {
        $totalLeaveDays = getAnnualLeaveDays($_GET['id']);
        $totalOutstandingDays = oustandingLeaveDays($_GET['id']);
        $leaveDaysTaken = leaveDaysTaken($_GET['id']);

        $response = array(
            "totalLeaveDays" => $totalLeaveDays["annualLeaveDays"],
            "totalOutstandingDays" => $totalOutstandingDays["outstanding_days"],
            "leaveDaysTaken" => $leaveDaysTaken["total_leave_taken"]
        );
        echo json_encode($response);

    } else {
        $data = [
            'status' => 422,
            'message' => 'user ID required',
        ];
        header("HTTP/1.0 422 Info Required");
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
