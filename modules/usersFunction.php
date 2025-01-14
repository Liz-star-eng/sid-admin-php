<?php
require '../../header.php';
require '../../dbcon.php';
require '../../responseStatuses.php';
require '../../utils.php';

$database = new Operations();
$conn = $database->dbconnection();
session_start();


// get user function

function getUser($staff_id)
{

    $query = "SELECT U.*, 
       (
        SELECT JSON_ARRAYAGG(JSON_OBJECT(
                    'id', C.contact_id,
                    'email', C.email,
                    'phone', C.phone_number
                ))
            FROM contact C
            WHERE U.staff_id = C.staff_id
        ) AS contact_info,
       (
        SELECT JSON_OBJECT(
                    'id', E.id,
                    'phone', E.emergency_phone,
                    'address', E.emergency_address,
                    'relationship', relationship,
                    'fullname', CONCAT(E.emergency_first_name, ' ', E.emergency_last_name)
                )
            FROM emergency_contact E
            WHERE U.staff_id = E.staff_id
        ) AS emergency_contact_info,
       JSON_OBJECT(
            'firstname', S.firstname,
            'surname', S.surname,
            'department', S.departmentName,
            'profilePhoto', S.image, 
            'job_title', S.job_title,
            'job_start_date', S.job_start_date,
            'job_email', S.job_email,
            'address', S.address,
            'dob', S.dob,
            'gender', S.gender,
            'marital_status', S.marital_status,
            'bank_name', S.bank_name,
            'bank_branch', S.bank_branch,
            'bank_account_number', S.bank_account_number,
            'ssnit', S.ssnit_number,
            'gh_card_number', S.gh_card_number
        ) AS staff_info
FROM users U
LEFT JOIN staff S ON U.staff_id = S.staff_id
WHERE U.staff_id = ?";

    global $conn;
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$staff_id])) {
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            $res = Utils::convertStringToJson($res);
            return $res;
        } else {
            // return status404('User Not Found');
            return [];
        }
    } else {
        return status500();
    }
}

// end

// getting user by department

function getUserByDepartment($department)
{
    global $conn;
    $query = "SELECT CONCAT(S.firstname, ' ', S.surname) AS fullname, S.staff_id FROM staff AS S WHERE S.departmentName = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$department])) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            $res = Utils::convertStringToJson($res);
            return $res;
        } else {
            // return 'No User Found in the department';
            return [];
        }
    } else {
        return status500();
    }
}

// end

// get all users
function getAllUsers()
{
    global $conn;
    $query = "SELECT * FROM users";
    $stmt = $conn->prepare($query);
    if ($stmt->execute()) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            return $total;
        } else {
            // return status404('No Users Found');
            return [];
        }
    } else {
        return status500();
    }
}

// change user role
function updateUserRole($staff_id, $new_role)
{
    global $conn;

    $query = "UPDATE users SET user_role = ? WHERE staff_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$new_role, $staff_id])) {
        return json_encode([
            'status' => 200,
            'message' => 'User role updated successfully'
        ]);
    } else {
        return json_encode([
            'status' => 500,
            'message' => 'Internal Server Error'
        ]);
    }
}

// end