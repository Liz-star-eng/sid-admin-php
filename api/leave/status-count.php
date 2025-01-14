<?php
header("Access-Control-Allow-Methods: GET");

include "../../header.php";
include "../../modules/leaveFunction.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "GET") {
    $authStatusCount = statusAuthCount();
    if (isset($_GET['relieve_id'])) {
        $pendingStatusCount = statusPendingCount($_GET['relieve_id']);
    }
    echo json_encode([$authStatusCount, $pendingStatusCount]);
}
