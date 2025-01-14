<?php
require "../../header.php";
require "../../modules/usersFunction.php";

header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (isset($input['staff_id']) && isset($input['user_role'])) {
        $user_id = $input['staff_id'];
        $new_role = $input['user_role'];

        // Update the user role
        echo updateUserRole($user_id, $new_role);
    } else {
        echo json_encode([
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }
} else {
    echo json_encode([
        'status' => 405,
        'message' => 'Method Not Allowed'
    ]);
}