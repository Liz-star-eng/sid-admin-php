<?php
include '../header.php';
include 'authFunction.php';
include 'authConfig.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['email'])) {
    $email = $data['email'];

    // Call your function to request the password reset
    $response = requestPasswordReset($email);

    // Send a success response
    echo json_encode(['message' => $response]);
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
}