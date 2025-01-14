<?php
include "../../header.php";
include "../../modules/employeeFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input
    $body = json_decode(file_get_contents('php://input'), true);

    if (isset($body['type'])) {
        $type = $body['type'];

        switch ($type) {
            case 'contact':
                updateContactDetails($body);
                break;
            case 'emergency_contact':
                updateEmergencyContactDetails($body);
                break;
            default:
                echo json_encode(["error" => "Invalid type"]);
                break;
        }
    } else {
        echo json_encode(["error" => "Invalid input"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
