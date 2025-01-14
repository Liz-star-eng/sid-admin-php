<?php

include '../header.php';
include 'authFunction.php';
include 'authConfig.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Ensure the token is in the URL
if (!isset($_GET['token'])) {
    echo json_encode(['error' => 'Missing token in the URL']);
    exit;
}

$token = $_GET['token'] ?? '';
error_log("Token from URL: " . $token);

// Read the request body
$body = file_get_contents('php://input');
$requestParams = json_decode($body, true);

// Check if the required parameters are present
if (!isset($requestParams['password']) || !isset($requestParams['confirm_password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Validate that the passwords match
if ($requestParams['password'] !== $requestParams['confirm_password']) {
    http_response_code(400);
    echo json_encode(['error' => 'Passwords do not match']);
    exit;
}

// Call the resetPassword function with the extracted token
$result = resetPassword($token, $requestParams['password']);

if ($result === 'Password reset successfully.') {
    echo json_encode(['message' => $result]);
} else {
    http_response_code(400);
    echo json_encode(['error' => $result]);
}