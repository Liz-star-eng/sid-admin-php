<?php
require "../../header.php";
require "../../modules/requestFunction.php";
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get the request body
    $body = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($body['request_id']) || !isset($body['comment'])) {
        echo json_encode(['status' => 400, 'message' => 'Request ID is required']);
        exit();
    }

    // decline the request
    $request_id = $body['request_id'];
    $comment = $body['comment'];

    try {
        declineRequest($request_id, ['comment' => $comment]);
        echo json_encode(['status' => 200, 'message' => 'Request declined and staff details updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 500, 'message' => 'Failed to approve request: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 405, 'message' => 'Method Not Allowed']);
    exit();
}