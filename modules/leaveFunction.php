<?php
require '../../header.php';
require '../../dbcon.php';
require '../../responseStatuses.php';
require '../../utils.php';

$database = new Operations();
$conn = $database->dbconnection();
session_start();


// getting Holidays
function getPublicHolidays($year, $countryCode, $apiKey)
{
    $url = "https://calendarific.com/api/v2/holidays?api_key=$apiKey&country=$countryCode&year=$year";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['meta']['code'] != 200) {
        return []; // Handle API errors or empty responses
    }

    $holidays = [];
    foreach ($data['response']['holidays'] as $holiday) {
        $holidays[] = $holiday['date']['datetime']['month'] . '-' . $holiday['date']['datetime']['day'];
    }

    return $holidays;
}
// end
// posting a leave application

function postLeaveForm($requestParams)
{
    global $conn;

    if (empty(trim($requestParams['staff_id']))) {
        return 'User not logged in';
    }

    if (empty(trim($requestParams['start_date']))) {
        return status422('Start date must be provided');
    }

    if (empty(trim($requestParams['end_date']))) {
        return status422('End date must be provided');
    }

    if (!empty(trim($requestParams['start_date'])) && !empty(trim($requestParams['end_date']))) {
        $startDate = new DateTime($requestParams['start_date']);
        $endDate = new DateTime($requestParams['end_date']);
        $year = $startDate->format('Y');
        $countryCode = 'gh';
        $apiKey = 'PTDSbwP8Szta0Yzu8Qh0vrtu52FDQ8cd';
        $holidays = getPublicHolidays($year, $countryCode, $apiKey);

        $number_of_days = getWeekdaysCount($startDate, $endDate, $holidays);
        if ($number_of_days === 0) {
            return status422('The number of leave days cannot be zero. Please check the dates.');
        }
    }

    if (empty(trim($requestParams['emergency_name']))) {
        return status422('Name must be provided');
    }

    if (empty(trim($requestParams['emergency_number']))) {
        return status422('Contact must be provided');
    }

    $reason = $requestParams['reason'];
    $reliever_id = $requestParams['reliever_id'];
    $dateOfRequest = date('Y-m-d');

    $query = 'INSERT INTO `leave` (staff_id, leave_type, number_of_days, 
    `start_date`, end_date, date_of_request, reliever_id, 
    emergency_name, emergency_number, reason) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = $conn->prepare($query);

    if ($stmt->execute([
        $requestParams['staff_id'],
        $requestParams['type_of_leave'],
        $number_of_days,
        $requestParams['start_date'],
        $requestParams['end_date'],
        $dateOfRequest,
        $reliever_id,
        $requestParams['emergency_name'],
        $requestParams['emergency_number'],
        $reason,
    ])) {
        return status200('Form successfully posted', null);
    } else {
        return status500();
    }
}

function getWeekdaysCount($startDate, $endDate, $holidays = [])
{
    $current = clone $startDate;
    $weekdays = 0;

    while ($current <= $endDate) {
        if ($current->format('N') < 6) { // Check if the day is a weekday
            $holidayDate = $current->format('m-d'); // Format date as MM-DD
            if (!in_array($holidayDate, $holidays)) { // Check if the date is not a holiday
                $weekdays++;
            }
        }
        $current->modify('+1 day'); // Move to the next day
    }

    return $weekdays;
}
// end

// get unavailable dates for a user
function getUnavailableLeaveDates($staff_id)
{
    global $conn;

    // Check if staff_id is provided
    if (empty(trim($staff_id))) {
        return status422('Staff ID must be provided.');
    }

    // Escape `leave` table name using backticks
    $query = 'SELECT start_date, end_date FROM `leave` WHERE staff_id = ?';
    $stmt = $conn->prepare($query);
    $stmt->execute([$staff_id]);

    $unavailableDates = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Create an array of unavailable date ranges
        $unavailableDates[] = [
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
        ];
    }

    return status200('Unavailable dates fetched successfully', $unavailableDates);
}

// end

// getting leave days

function getAnnualLeaveDays($id)
{

    global $conn;
    $query = 'SELECT annualLeaveDays FROM staff WHERE staff_id=?';
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$id])) {

        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();

        if ($total > 0) {
            return $res;
        } else {
            return status404('User Not Found');
        }
    } else {
        return status500();
    }
}

function oustandingLeaveDays($id)
{
    global $conn;

    $query = "SELECT 
              CASE 
                WHEN SUM(number_of_days) IS NULL THEN S.annualLeaveDays
                ELSE S.annualLeaveDays - SUM(number_of_days)
              END AS outstanding_days 
                FROM 
                    staff AS S
                LEFT JOIN 
                    `leave` AS L ON L.staff_id = S.staff_id AND L.`status` = 'Approved'
                WHERE 
                    S.staff_id = ?";

    $stmt = $conn->prepare($query);
    if ($stmt->execute([$id])) {
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            return $res;
        } else {
            return status404('User Not Found');
        }
    } else {
        return status500();
    }
}

function leaveDaysTaken($id)
{
    global $conn;
    $query = "SELECT COALESCE(SUM(number_of_days), 0) AS total_leave_taken
    FROM `leave`
    WHERE `status`= 'Approved' && staff_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$id])) {
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            return $res;
        } else {
            return [];
        }
    } else {
        return status500();
    }
}

// end

// getting leave activities

function leaveActivities($id)
{
    global $conn;
    $query = "SELECT 
    L.*, 
    CONCAT(S1.firstname, ' ', S1.surname) AS reliever_name,
    CONCAT(S2.firstname, ' ', S2.surname) AS approver_name
FROM 
    `leave` AS L
LEFT JOIN 
    staff AS S1 ON L.reliever_id = S1.staff_id  
LEFT JOIN 
    staff AS S2 ON L.approved_by = S2.staff_id 
WHERE 
    L.staff_id = ? 
ORDER BY 
    L.leave_id ASC";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$id])) {

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();

        if ($total > 0) {
            return $res;
        } else {
            return [];
        }
    } else {
        return status500();
    }
}
// end

// getting leave applications a user is relieving

function relieverActivities($reliever_id)
{
    global $conn;
    $query = "SELECT L.*, CONCAT(S.firstname, ' ', S.surname) AS fullname,    CONCAT(A.firstname, ' ', A.surname) AS approver_name
		FROM `leave` AS L
		LEFT JOIN staff AS S ON L.staff_id = S.staff_id
          LEFT JOIN staff AS A ON L.approved_by = A.staff_id
         WHERE reliever_id =? ORDER BY L.leave_id DESC";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$reliever_id])) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();

        if ($total > 0) {
            return $res;
        } else {
            return [];
        }
    } else {
        return status500();
    }
}



// process leave status

function authorizeLeaveStatus($reliever_id, $leave_id)
{
    global $conn;
    $query = "UPDATE `leave` SET `status`='Authorized', authorized_date=NOW() WHERE reliever_id=? AND leave_id=?";

    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$reliever_id, $leave_id]);

        if ($stmt->rowCount() > 0) {
            echo "Leave status authorized successfully.";
        } else {
            echo "No record updated. Check the reliever_id and leave_id.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function declineLeaveStatus($reliever_id, $leave_id)
{
    global $conn;
    $query = "UPDATE `leave` SET `status`='Declined', declined_date=NOW() WHERE reliever_id=? AND leave_id=?";

    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$reliever_id, $leave_id]);

        if ($stmt->rowCount() > 0) {
            echo "Leave status declined successfully.";
        } else {
            echo "No record updated. Check the reliever_id and leave_id.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function approveLeaveStatus($leave_id, $approver_id)
{
    global $conn;
    $query = "UPDATE `leave` SET `status`='Approved', approved_by=?, approved_date=NOW() WHERE leave_id=?";

    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$approver_id, $leave_id]);

        if ($stmt->rowCount() > 0) {
            echo "Leave status approved successfully.";
        } else {
            echo "No record updated. Check the leave_id.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function rejectLeaveStatus($reliever_id, $leave_id, $rejecter_id)
{
    global $conn;
    $query = "UPDATE `leave` SET `status`='Rejected', rejected_by=?, rejected_date=NOW() WHERE reliever_id=? AND leave_id=?";

    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$rejecter_id, $reliever_id, $leave_id]);

        if ($stmt->rowCount() > 0) {
            echo "Leave status rejected successfully.";
        } else {
            echo "No record updated. Check the reliever_id and leave_id.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//  get all leave

function getAllLeave()
{
    global $conn;

    $activeStaffIds = activeLeave();

    $query = "SELECT 
    L.*, 
    CONCAT(S.firstname, ' ', S.surname) AS fullname, 
    CONCAT(R.firstname, ' ', R.surname) AS reliever,
    CONCAT(A.firstname, ' ', A.surname) AS approver_name
    FROM `leave` AS L
    LEFT JOIN staff AS S ON L.staff_id = S.staff_id
    LEFT JOIN staff AS R ON L.reliever_id = R.staff_id
    LEFT JOIN staff AS A ON L.approved_by = A.staff_id
    WHERE L.`status` != 'Pending'
    ORDER BY L.leave_id DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = $stmt->rowCount();
    if ($total) {
        return $res;
    } else {
        return [];
    }
}

// delete a pending leave

function deleteLeaveFunction($leave_id)
{
    global $conn;
    $query = "DELETE FROM `leave` WHERE leave_id=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$leave_id]);
}
// end

// get leave by department
function getLeaveByDepartment($department)
{
    global $conn;
    $query = " SELECT L.*, CONCAT(S.firstname, ' ', S.surname) AS fullname, CONCAT(A.firstname, ' ', A.surname) AS approver_name
		FROM `leave` AS L
		LEFT JOIN staff AS S ON L.staff_id = S.staff_id 
        LEFT JOIN staff AS A ON L.approved_by = A.staff_id
        WHERE S.departmentName =? ORDER BY L.leave_id ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$department]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = $stmt->rowCount();
    if ($total) {
        return $res;
    } else {
        // return status500("No relievers found");
        return [];
    }
}

function statusPendingCount($reliever_id)
{
    global $conn;
    $query = "SELECT COUNT(*) AS pendingCount FROM `leave` WHERE `status` = 'Pending' AND reliever_id=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$reliever_id]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = $stmt->rowCount();
    if ($total) {
        return $res;
    } else {
        return [];
    }
}

function statusAuthCount()
{
    global $conn;
    $query = "SELECT COUNT(*) AS authorizedCount  FROM `leave` WHERE `status` = 'Authorized'";
    $stmt = $conn->prepare($query);
    $stmt->execute([]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = $stmt->rowCount();
    if ($total) {
        return $res;
    } else {
        return [];
    }
}

// active leave days

function activeLeave()
{
    global $conn;

    // Current date
    $currentDate = date('Y-m-d');

    $query = 'SELECT * FROM `leave` WHERE `start_date` <= ? AND end_date >= ? AND `status` = "Approved"';
    $stmt = $conn->prepare($query);
    $stmt->execute([$currentDate, $currentDate]);
    $total = $stmt->rowCount();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (
        $stmt->rowCount() > 0
    ) {
        //   return $total;
        return $results;
    } else {
        return 0;
    }
}
