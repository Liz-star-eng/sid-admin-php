<?php
include "../../header.php";
include "../../modules/eventFunction.php";
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() === JSON_ERROR_NONE) {
        if (is_array($input) && count($input) > 0) {
            $responses = [];
            foreach ($input as $event) {
                if (isset($event['id']) && !empty($event['event_name'])) {
                    $id = $event['id'];
                    $data = [
                        'event_name' => $event['event_name'],
                        'venue' => $event['venue'],
                        'description' => $event['description'],
                        'scheduled_start_date' => $event['scheduled_start_date'],
                        'scheduled_end_date' => $event['scheduled_end_date'],
                        'scheduled_start_time' => $event['scheduled_start_time']
                    ];

                    $response = updateEvent($id, $data);
                    $responses[] = json_decode($response, true);
                } else {
                    $responses[] = ['status' => 400, 'message' => 'Invalid input'];
                }
            }
            echo json_encode($responses);
        } else {
            echo json_encode(['status' => 400, 'message' => 'Invalid input']);
        }
    } else {
        echo json_encode(['status' => 400, 'message' => 'Invalid JSON']);
    }
} else {
    echo json_encode(['status' => 405, 'message' => 'Method Not Allowed']);
}
?>