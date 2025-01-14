<?php
include "../../header.php";
include "../../modules/employeeFunction.php";
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['type'])) {
        $staff_id = $input['staff_id'];
        $requestParams = $input;
        $type = $input['type'];

        switch ($type) {
            case 'staff':
                $result = updateStaffDetails($requestParams);
                break;
            case 'bank':
                $result = updateBankDetails($requestParams);
                break;
            case 'job':
                $result = updateJobDetails($requestParams);
                break;
            default:
                $result = ['status' => 'error', 'message' => 'Invalid type'];
                break;
        }

        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
