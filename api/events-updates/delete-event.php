<?php
include "../../header.php";
include "../../modules/eventFunction.php";
header('Access-Control-Allow-Methods: DELETE');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];
        $response = deleteEvent($id);
        echo $response;
    } else {
        echo json_encode(array('message' => 'Event ID not provided'));
    }
} else {
    echo json_encode(array('message' => 'Invalid request method'));
}