<?php

require '../../header.php';
require '../../dbcon.php';
require '../../responseStatuses.php';
require '../../utils.php';

$database = new Operations();
$conn = $database->dbconnection();

function postEvent($requestParams)
{

try{
    global $conn;
    $eventName = $requestParams['eventName'] ?? null;
    $eventLocation = $requestParams['eventLocation'] ?? null;
    $eventDescription = $requestParams['eventDescription'] ?? null;
    $eventStartDate = $requestParams['eventStartDate'] ?? null;
    $eventEndDate = $requestParams['eventEndDate'] ?? null;
    $eventTime = $requestParams['eventTime'] ?? null;
 

    // validate fields
    if (!$eventName || !$eventDescription || !$eventStartDate || !$eventEndDate || !$eventTime || !$eventLocation) {
        return json_encode(['status' => 'error', 'message' => 'All fields are mandatory']);
    }
    // end
    // get duration
    $startDateTime = new DateTime("$eventStartDate $eventTime");
    $endDateTime = new DateTime($eventEndDate);
    $duration = $startDateTime->diff($endDateTime)->format('%a days');
    // end

    $query = "INSERT INTO `events` (id, event_name, venue, `description`, scheduled_start_date, scheduled_end_date, scheduled_start_time, duration, created_at, updated_at )
     VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, NOW(), NULL)";
          $stmt = $conn->prepare($query);
        $stmt->execute([
        $eventName,
        $eventLocation,
        $eventDescription,
        $eventStartDate,
        $eventEndDate,
        $eventTime,
        $duration
        ]);

        return json_encode(['status' => 'success', 'message' => 'Event added successfully']);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        return json_encode(['status' => 'error', 'message' => 'Failed to add event: ' . $e->getMessage()]);
    }
}

// get all events
function getAllEvent()
{
    global $conn;

    $query = "SELECT * FROM `events`  ORDER BY `id` DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = $stmt->rowCount();
    if ($total) {
        return $res;
    } else {
        // return status500("No events found");
        return [];
    }
}

// get eveent by id

function getEventById($id)
{
    global $conn;

    $query = "SELECT * FROM `events` WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = $stmt->rowCount();
    if ($total) {
        return $res;
    } else {
        // return status500("No events found");
        return [];
    }
}

// delete Event

function deleteEvent($id)
{
 global $conn;

    $query = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$id])) {
        return json_encode(array('message' => 'Event deleted successfully'));
        echo 'success';
    } else {
        return json_encode(array('message' => 'Failed to delete event'));
    }
}

// update Event

function updateEvent($id, $fields)
{
    global $conn;

    // Construct the SQL query dynamically based on the fields to update
    $setClause = [];
    $values = [];
    foreach ($fields as $column => $value) {
        $setClause[] = "$column = ?";
        $values[] = $value;
    }
    $values[] = $id; // Add the ID to the end of the values array

    $setClauseString = implode(", ", $setClause);
    $query = "UPDATE events SET $setClauseString WHERE id = ?";

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    if ($stmt->execute($values)) {
        return json_encode(['status' => 200, 'message' => 'Event updated successfully']);
    } else {
        return json_encode(['status' => 500, 'message' => 'Failed to update event']);
    }
}