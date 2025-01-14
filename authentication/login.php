<?php
include '../header.php';
include 'authFunction.php';
include 'authConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];
        $userData = authenticateUser($username, $password);

        if ($userData) {
            $token = generateToken($userData);
            $decoded_data = validateToken($token);
            echo json_encode(array('token' => $token, 'user' => $decoded_data));
        } else {
            http_response_code(401);
            echo json_encode(array('message' => 'No matching user found'));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Username or password not provided'));
    }
}