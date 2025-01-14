<?php

function status404($message)
{
    $res = [
        'status' => 404,
        'message' => $message,
    ];
    header("HTTP/1.0 404 {$message}");
    return json_encode($res);
}

function status200($message, $data)
{
    $res = [
        'status' => 200,
        'message' => $message,
        'data' => $data
    ];
    header("HTTP/1.0 200 Successfully Fetched Data");
    return json_encode($res);
    exit();
}

function status500()
{
    $res = [
        'status' => 500,
        'message' => 'Internal Server Error',
    ];
    header("HTTP/1.0 500 Internal Server Error");
    return json_encode($res);
    exit();
}

function status422($message)
{
    $res = [
        'status' => 422,
        'message' => $message,
    ];
    // header("HTTP/1.0 422 Info required");
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(422);
    echo json_encode($res);
    die();
}
