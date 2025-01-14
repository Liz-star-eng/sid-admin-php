<?php
require '../../header.php';
require '../../dbcon.php';
require '../../responseStatuses.php';
require '../../utils.php';

$database = new Operations();
$conn = $database->dbconnection();
session_start();

// post personal details request
function personalDetailChange($requestParams)
{
    global $conn;

    $staff_id = $requestParams['staff_id'];
    $request_category = 'Personal Detail Change';
    $reason = $requestParams['reason'];
    $status = "Pending";
    $request_body = json_encode($requestParams['request_body']);

    if (!$staff_id || !$reason || !$request_body) {
        echo json_encode(['status' => 400, 'message' => 'All fields are required']);
        exit();
    }

    // Check if there's already a pending request in this category for the same staff member
    $query = "SELECT * FROM requests WHERE staff_id = ? AND request_category = ? AND `status` = 'Pending'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$staff_id, $request_category]);
    $existingRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRequest) {
        // If a pending request exists, reject the new request
        echo json_encode(['status' => 400, 'message' => 'A pending request already exists in this category for this staff member']);
        exit();
    }

    // No pending request, proceed with inserting the new request
    try {
        $query = "INSERT INTO requests (request_id, staff_id, request_category, date_of_request, `status`, reason, request_body) 
                  VALUES (NULL, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $request_category,
            $status,
            $reason,
            $request_body
        ]);
        echo json_encode(['status' => 200, 'message' => 'Request submitted successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 500, 'message' => 'Failed to submit request: ' . $e->getMessage()]);
    }
    exit();
}
// emergency contact
function emergencyDetailChange($requestParams)
{
    global $conn;

    $staff_id = $requestParams['staff_id'];
    $request_category = 'Emergency Detail Change';
    $reason = $requestParams['reason'];
    $status = "Pending";
    $request_body = json_encode($requestParams['request_body']);

    if (!$staff_id || !$reason || !$request_body) {
        echo json_encode(['status' => 400, 'message' => 'All fields are required']);
        exit();
    }


    // Check if there's already a pending request in this category for the same staff member
    $query = "SELECT * FROM requests WHERE staff_id = ? AND request_category = ? AND `status` = 'Pending'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$staff_id, $request_category]);
    $existingRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRequest) {
        // If a pending request exists, reject the new request
        echo json_encode(['status' => 400, 'message' => 'A pending request already exists in this category for this staff member']);
        exit();
    }

    try {
        $query = "INSERT INTO requests (request_id, staff_id, request_category, date_of_request, `status`, reason, request_body) 
                  VALUES (NULL, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $request_category,
            $status,
            $reason,
            $request_body
        ]);
        echo json_encode(['status' => 200, 'message' => 'Request submitted successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 500, 'message' => 'Failed to submit request: ' . $e->getMessage()]);
    }
    exit();
}
// end

function bankDetailChange($requestParams)
{
    global $conn;

    $staff_id = $requestParams['staff_id'];
    $request_category = 'Bank Detail Change';
    $reason = $requestParams['reason'];
    $status = "Pending";
    $request_body = json_encode($requestParams['request_body']);

    if (!$staff_id || !$reason || !$request_body) {
        echo json_encode(['status' => 400, 'message' => 'All fields are required']);
        exit();
    }


    // Check if there's already a pending request in this category for the same staff member
    $query = "SELECT * FROM requests WHERE staff_id = ? AND request_category = ? AND `status` = 'Pending'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$staff_id, $request_category]);
    $existingRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRequest) {
        // If a pending request exists, reject the new request
        echo json_encode(['status' => 400, 'message' => 'A pending request already exists in this category for this staff member']);
        exit();
    }

    try {
        $query = "INSERT INTO requests (request_id, staff_id, request_category, date_of_request, `status`, reason, request_body) 
                  VALUES (NULL, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $request_category,
            $status,
            $reason,
            $request_body
        ]);
        echo json_encode(['status' => 200, 'message' => 'Request submitted successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 500, 'message' => 'Failed to submit request: ' . $e->getMessage()]);
    }
    exit();
}

function salaryAdvance($requestParams)
{
    global $conn;

    $staff_id = $requestParams['staff_id'];
    $request_category = 'Salary Advance';
    $reason = $requestParams['reason'];
    $status = "Pending";
    $request_body = json_encode($requestParams['request_body']);

    if (!$staff_id || !$reason || !$request_body) {
        echo json_encode(['status' => 400, 'message' => 'All fields are required']);
        exit();
    }

    // Check if there's already a pending request in this category for the same staff member
    $query = "SELECT * FROM requests WHERE staff_id = ? AND request_category = ? AND `status` = 'Pending'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$staff_id, $request_category]);
    $existingRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRequest) {
        // If a pending request exists, reject the new request
        echo json_encode(['status' => 400, 'message' => 'A pending request already exists in this category for this staff member']);
        exit();
    }

    try {
        $query = "INSERT INTO requests (request_id, staff_id, request_category, date_of_request, `status`, reason, request_body) 
                  VALUES (NULL, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $request_category,
            $status,
            $reason,
            $request_body
        ]);
        echo json_encode(['status' => 200, 'message' => 'Request submitted successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 500, 'message' => 'Failed to submit request: ' . $e->getMessage()]);
    }
    exit();
}

// getting requets
function getRequest($staff_id)
{
    global $conn;
    $query = 'SELECT * FROM requests where staff_id=? ORDER BY date_of_request DESC';
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$staff_id])) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            $res = Utils::convertStringToJson($res);
            return $res;
        } else {
            return [];
        }
    } else {
        return status500();
    }
}

// total request

function getTotalRequest()
{
    global $conn;
    $query = "SELECT * FROM requests";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = $stmt->rowCount();
    if ($total) {
        return $total;
    } else {
        return 0;
    }
}

// requets by status
function getPendingRequests()
{
    global $conn;
    try {
        $query = "SELECT R.*, CONCAT(S.firstname, ' ', S.surname) AS staffName FROM requests AS R LEFT JOIN staff AS S ON R.staff_id = S.staff_id WHERE R.`status` = 'Pending' ORDER BY date_of_request DESC";
        $stmt = $conn->prepare($query);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($res) > 0) {
                $res = Utils::convertStringToJson($res);
                return $res;
            } else {
                return [];
            }
        } else {
            return status500();
        }
    } catch (PDOException $e) {
        error_log("Database query error: " . $e->getMessage());
        return status500();
    }
}
// Processed requests

function getProcessedRequests()
{
    global $conn;
    try {
        $query = "SELECT R.*, CONCAT(S.firstname, ' ', S.surname) AS staffName FROM requests AS R LEFT JOIN staff AS S ON R.staff_id = S.staff_id WHERE R.`status` != 'Pending' ORDER BY date_of_request DESC";
        $stmt = $conn->prepare($query);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($res) > 0) {
                $res = Utils::convertStringToJson($res);
                return $res;
            } else {
                return [];
            }
        } else {
            return status500();
        }
    } catch (PDOException $e) {
        error_log("Database query error: " . $e->getMessage());
        return status500();
    }
}

// Processing request
function declineRequest($request_id, $requestParams)
{

    global $conn;

    $comment = $requestParams['comment'];

    $query = "UPDATE requests SET `status`='Rejected', `comment` = ?, declined_date = NOW(), declined_by = 'HR'  WHERE request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$comment, $request_id]);
}

// approve request
function approveRequest($request_id, $approveFormData, $comment)
{
    global $conn;

    try {
        // Begin a transaction
        $conn->beginTransaction();
        $query = "UPDATE requests SET `status`='Approved', `comment`=? , approved_date = NOW(), approved_by = 'HR' WHERE request_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$comment,$request_id]);

        // Fetch the request body
        $query = "SELECT request_id, request_body, request_category, staff_id FROM requests WHERE request_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$request_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res) {
            $requestBody = json_decode($res['request_body'], true);
            $requestCategory = $res['request_category'];
            $staff_id = $res['staff_id'];
            $request_id = $res['request_id'];
            // Update the staff table based on the request category
            switch ($requestCategory) {
                case 'Salary Advance':
                    postSalaryAdvance($staff_id, $requestBody, $approveFormData, $request_id, $comment);
                    break;
                case 'Personal Detail Change':
                    updatePersonalDetails($staff_id, $requestBody);
                    break;
                case 'Emergency Detail Change':
                    updateEmergencyDetails($staff_id, $requestBody);
                    break;
                case 'Bank Detail Change':
                    updateBankDetails($staff_id, $requestBody);
                    break;
                default:
                    throw new Exception("Unknown request category: $requestCategory");
            }

            // Commit the transaction
            $conn->commit();
            echo "Request approved and staff details updated successfully.";
            print_r($requestBody);
        } else {
            throw new Exception("Request not found");
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo 'Failed to approve request: ' . $e->getMessage();
    }
}

function updatePersonalDetails($staff_id, $requestBody)
{
    global $conn;

    // Convert keys to match the expected format (lowercase)
    $requestBody = array_change_key_case($requestBody, CASE_LOWER);

    // Fields for the `staff` table
    $staffFields = [
        'firstname' => 'firstname',
        'surname' => 'lastname',
        'address' => 'address',
        'ssnit_number' => 'ssnitnumber',
        'gh_card_number' => 'ghanacardnumber'
    ];

    // Fields for the `contact` table
    $contactFields = [
        'email' => 'email',
        'phone_number' => 'phonenumber'
    ];

    // Check if $requestBody is set and is an array
    if (!isset($requestBody) || !is_array($requestBody)) {
        echo "Invalid request data.";
        return;
    }

    // Debug output: Show the contents of $requestBody
    echo "Request Body: " . print_r($requestBody, true) . "\n";

    // Update `staff` table
    $staffSet = [];
    $staffParams = [];
    foreach ($staffFields as $dbField => $requestField) {
        if (isset($requestBody[$requestField])) {
            $staffSet[] = "$dbField = ?";
            $staffParams[] = $requestBody[$requestField];
        }
    }

    if (!empty($staffSet)) {
        $staffSetStr = implode(', ', $staffSet);
        $query = "UPDATE staff SET $staffSetStr WHERE staff_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $staffParams[] = $staff_id;
            if ($stmt->execute($staffParams)) {
                echo "Personal details updated for staff ID: $staff_id in `staff` table.\n";
            } else {
                echo "Error updating `staff` table: " . implode(", ", $stmt->errorInfo()) . "\n";
            }
        } else {
            echo "Error preparing `staff` table query: " . implode(", ", $conn->errorInfo()) . "\n";
        }
    } else {
        echo "No fields to update in `staff` table.\n";
    }

    // Update `contact` table
    $contactSet = [];
    $contactParams = [];
    foreach ($contactFields as $dbField => $requestField) {
        if (isset($requestBody[$requestField])) {
            $contactSet[] = "$dbField = ?";
            $contactParams[] = $requestBody[$requestField];
        }
    }

    if (!empty($contactSet)) {
        $contactSetStr = implode(', ', $contactSet);
        $query = "UPDATE contact SET $contactSetStr WHERE staff_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $contactParams[] = $staff_id;
            if ($stmt->execute($contactParams)) {
                echo "Personal details updated for staff ID: $staff_id in `contact` table.\n";
            } else {
                echo "Error updating `contact` table: " . implode(", ", $stmt->errorInfo()) . "\n";
            }
        } else {
            echo "Error preparing `contact` table query: " . implode(", ", $conn->errorInfo()) . "\n";
        }
    } else {
        echo "No fields to update in `contact` table.\n";
    }
}

function updateBankDetails($staff_id, $requestBody)
{
    global $conn;

    $query = "UPDATE staff SET bank_name = ?, bank_branch = ?, bank_account_number = ? WHERE staff_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        $requestBody['bank_name'],
        $requestBody['branch'],
        $requestBody['acc_no'],
        $staff_id,
    ]);

    echo "Bank details updated for staff ID: $staff_id";
}

// emergency
function updateEmergencyDetails($staff_id, $requestBody)
{
    global $conn;

    $requestBody = array_change_key_case($requestBody, CASE_LOWER);

    // Fields for the `emergency_contact` table
    $emergencyFields = [
        'firstname' => 'emergency_first_name',
        'surname' => 'emergency_last_name',
        'relationship' => 'relationship',
        'address' => 'emergency_address',
        'phone' => 'emergency_phone'
    ];

    // Check if $requestBody is set and is an array
    if (!isset($requestBody) || !is_array($requestBody)) {
        echo json_encode(['status' => 400, 'message' => 'Invalid request data.']);
        return;
    }

    // Update `emergency_contact` table
    $staffSet = [];
    $staffParams = [];
    foreach ($emergencyFields as $dbField => $requestField) {
        if (isset($requestBody[$requestField])) {
            $staffSet[] = "$dbField = ?";
            $staffParams[] = $requestBody[$requestField];
        }
    }

    if (!empty($staffSet)) {
        $staffSetStr = implode(', ', $staffSet);
        $query = "UPDATE emergency_contact SET $staffSetStr WHERE staff_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $staffParams[] = $staff_id;
            if ($stmt->execute($staffParams)) {
                echo json_encode(['status' => 200, 'message' => 'Emergency details updated successfully.']);
            } else {
                echo json_encode(['status' => 500, 'message' => 'Error updating `emergency_contact` table: ' . implode(", ", $stmt->errorInfo())]);
            }
        } else {
            echo json_encode(['status' => 500, 'message' => 'Error preparing `emergency_contact` table query: ' . implode(", ", $conn->errorInfo())]);
        }
    } else {
        echo json_encode(['status' => 400, 'message' => 'No fields to update in `emergency_contact` table.']);
    }
}
    // end

function postSalaryAdvance($staff_id, $requestBody, $approveFormData, $request_id, $comment)
{
    global $conn;



    $query = "INSERT INTO salaryAdvance (id, staff_id, request_id, requested_amount, reason, amount_payable, no_installments, rate, start_month, comment, date_approved) 
              VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        $staff_id,
        $request_id,
        $requestBody['requested_amount'],
        $requestBody['reason'],
        $approveFormData['amount_payable'],
        $approveFormData['no_installments'],
        $approveFormData['rate'],
        $approveFormData['start_month'],
        $comment
    ]);

    echo "Salary Advance Approved for staff ID: $staff_id";
}

function getsalaryAdvance($request_id)
{
    global $conn;

    $query = " SELECT * FROM salaryadvance WHERE request_id =?";
    $stmt = $conn->prepare($query);


    if ($stmt->execute([$request_id])) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            $res = Utils::convertStringToJson($res);
            return $res;
        } else {
            return [];
        }
    } else {
        return status500();
    }
}
