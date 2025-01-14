<?php

require "../../header.php";
require "../../modules/leaveFunction.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['staff_id']) && !empty($_GET['staff_id'])) {
        $staff_id = $_GET['staff_id'];

        // Fetch unavailable leave dates
        try {
            $response = getUnavailableLeaveDates($staff_id);

            if ($response) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'Unavailable dates fetched successfully',
                    'data' => $response
                ]);
            } else {
                echo json_encode([
                    'status' => 404,
                    'message' => 'No unavailable leave dates found',
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 500,
                'message' => 'Error retrieving unavailable leave dates',
                'error' => $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'status' => 400,
            'message' => 'Invalid or missing staff_id parameter'
        ]);
    }
} else {
    echo json_encode([
        'status' => 405,
        'message' => 'Method not allowed'
    ]);
}
