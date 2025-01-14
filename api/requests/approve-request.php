<?php
require "../../header.php";
require "../../modules/requestFunction.php";

header('Content-Type: application/json');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($body['request_id']) && isset($body['approveFormData']) && isset($body['comment'])) {
            $request_id = $body['request_id'];
            $approveFormData = $body['approveFormData'];
            $comment = $body['comment'];

            try {
                approveRequest($request_id, $approveFormData, $comment);
                echo json_encode([
                    'status' => 200,
                    'message' => 'Request approved and staff details updated successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Failed to approve request: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'Invalid request format'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 400,
            'message' => 'Invalid JSON'
        ]);
    }
} else {
    echo json_encode([
        'status' => 405,
        'message' => 'Method Not Allowed'
    ]);
    exit();
}
