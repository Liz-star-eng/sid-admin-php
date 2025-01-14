<?php

require '../../header.php';
require '../../dbcon.php';
require '../../responseStatuses.php';
require '../../utils.php';

$database = new Operations();
$conn = $database->dbconnection();
session_start();



// Add Employee
function addEmployee($requestParams)
{
    global $conn;
    // generating staff ID 
    $firstName = $requestParams['personalInfo']['firstName'];
    $lastName = $requestParams['personalInfo']['lastName'];

    $firstLetterFirstName = substr($firstName, 0, 1);
    $firstLetterLastName = substr($lastName, 0, 1);

    // latest staff id
    $query = "SELECT MAX(RIGHT(staff_id, 3)) AS max_suffix FROM staff WHERE LEFT(staff_id, 2) = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$firstLetterFirstName . $firstLetterLastName]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $suffix = str_pad($row['max_suffix'] + 1, 3, '0', STR_PAD_LEFT);
    $staff_id = $firstLetterFirstName . $firstLetterLastName . $suffix;
    $imagePath = $requestParams['image'];

    try {
        // start transaction
        $conn->beginTransaction();
        $query = "INSERT INTO contact (staff_id, email, phone_number) 
              VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $requestParams['contactInfo']['email'],
            $requestParams['contactInfo']['phone'],
            // $requestParams['contactInfo']['alt_phone']
        ]);
        $contact_id = $conn->lastInsertId();

        echo "Contact inserted for staff ID: $staff_id\n";
        echo "$contact_id\n";

        $query = "INSERT INTO emergency_contact (staff_id, emergency_first_name, emergency_last_name, relationship, emergency_address, emergency_phone) 
              VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $requestParams['emergencyContact']['emergency_first_name'],
            $requestParams['emergencyContact']['emergency_last_name'],
            $requestParams['emergencyContact']['relationship'],
            $requestParams['emergencyContact']['emergency_address'],
            $requestParams['emergencyContact']['emergency_phone'],
        ]);
        $emergency_contact_id = $conn->lastInsertId();
        echo "Emergency contact inserted for staff ID: $imagePath";

        $query = "INSERT INTO staff (staff_id, `image`, firstname, surname, dob, gender, marital_status, `address`, ssnit_number, gh_card_number, job_title, job_email, departmentName, contact_id, emergency_contact_id, job_start_date, bank_name, bank_branch, bank_account_number, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $imagePath,
            $requestParams['personalInfo']['firstName'],
            $requestParams['personalInfo']['lastName'],
            $requestParams['personalInfo']['dob'],
            $requestParams['personalInfo']['gender'],
            $requestParams['personalInfo']['marital_status'],
            $requestParams['personalInfo']['address'],
            $requestParams['personalInfo']['ssnit'],
            $requestParams['personalInfo']['ghanaCardNumber'],
            $requestParams['jobInfo']['job_title'],
            $requestParams['jobInfo']['job_email'],
            $requestParams['jobInfo']['department'],
            $contact_id,
            $emergency_contact_id,
            $requestParams['jobInfo']['start_date'],
            $requestParams['bankDetails']['bank_name'],
            $requestParams['bankDetails']['branch'],
            $requestParams['bankDetails']['acc_no'],
        ]);

        // insert into 

        $username = $requestParams['jobInfo']['job_email'];
        $password = password_hash('changeme', PASSWORD_DEFAULT);
        $user_role = 'User';
        $createdBy = 'HR';

        $query = "INSERT INTO users (id, staff_id, username, `password`, user_role, dateOfCreation, createdBy, lastUpdatedOn) 
                  VALUES (NULL, ?, ?, ?, ?, NOW(), ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $staff_id,
            $username,
            $password,
            $user_role,
            $createdBy,
        ]);

        $conn->commit();
        return 'Employee added successfully.';
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo 'Failed to add employee: ' . $e->getMessage();
    }
   
}

// fetch all employees information

function getAllEmployees()
{
    global $conn;
    $query = "
       SELECT S.*,
       (
           SELECT JSON_ARRAYAGG(JSON_OBJECT(
                      'id', C.contact_id,
                      'email', C.email,
                      'phone', C.phone_number,
                      'alt_phone', C.alt_phone_number
                  ))
           FROM contact C
           WHERE S.staff_id = C.staff_id
       ) AS contact_info,
       (
           SELECT JSON_OBJECT(
                      'id', E.id,
                      'first_name', E.emergency_first_name,
                      'last_name', E.emergency_last_name,
                      'relationship', E.relationship,
                      'address', E.emergency_address,
                      'phone', E.emergency_phone
                  )
           FROM emergency_contact E
           WHERE S.staff_id = E.staff_id
           LIMIT 1
         
       ) AS emergency_contact_info
FROM staff S";

    $stmt = $conn->prepare($query);

    if ($stmt->execute()) {
        $results =
            $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            foreach ($results as &$result) {
                if (isset($result['contact_info'])) {
                    $result['contact_info'] = json_decode($result['contact_info'], true);
                }
                if (isset($result['emergency_contact_info'])) {
                    $result['emergency_contact_info'] = json_decode($result['emergency_contact_info'], true);
                }
            }
            return  $results;
        } else {
            // return json_encode([
            //     'status' => 404,
            //     'message' => 'No employees found'
            // ]);

            return [];
        }
    } else {
        return json_encode([
            'status' => 500,
            'message' => 'Internal Server Error'
        ]);
    }
}

// get a staff

function getEmployee($staff_id)
{
    global $conn;
    $query = "SELECT S.*, U.*, 
       (
           SELECT JSON_ARRAYAGG(JSON_OBJECT(
                      'id', C.contact_id,
                      'email', C.email,
                      'phone', C.phone_number,
                      'alt_phone', C.alt_phone_number
                  ))
           FROM contact C
           WHERE S.staff_id = C.staff_id
       ) AS contact_info,
       (
           SELECT JSON_OBJECT(
                      'id', E.id,
                      'first_name', E.emergency_first_name,
                      'last_name', E.emergency_last_name,
                      'relationship', E.relationship,
                      'address', E.emergency_address,
                      'phone', E.emergency_phone
                  )
           FROM emergency_contact E
           WHERE S.staff_id = E.staff_id
           LIMIT 1
       ) AS emergency_contact_info
    FROM staff S
    JOIN users U ON S.staff_id = U.staff_id
    WHERE S.staff_id = ?;
       ";

    $stmt = $conn->prepare($query);

    if ($stmt->execute([$staff_id])) {
        $results =
            $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            foreach ($results as &$result) {
                if (isset($result['contact_info'])) {
                    $result['contact_info'] = json_decode($result['contact_info'], true);
                }
                if (isset($result['emergency_contact_info'])) {
                    $result['emergency_contact_info'] = json_decode($result['emergency_contact_info'], true);
                }
            }
            return  $results;
        } else {
            // return json_encode([
            //     'status' => 404,
            //     'message' => 'No employees found'
            // ]);

            return [];
        }
    } else {
        return json_encode([
            'status' => 500,
            'message' => 'Internal Server Error'
        ]);
    }
}

// update details
function updateStaffDetails($requestParams)
{
    global $conn;
    $staff_id = $requestParams['staff_id'];
    $firstname = $requestParams['firstname'];
    $surname = $requestParams['surname'];
    $dob = $requestParams['dob'];
    $address = $requestParams['address'];
    $gender = $requestParams['gender'];
    $marital_status = $requestParams['marital_status'];
    $ssnit_number = $requestParams['ssnit_number'];
    $gh_card_number = $requestParams['gh_card_number'];

    $query = "UPDATE staff SET 
                firstname = ?, 
                surname = ?, 
                dob = ?, 
                gender = ?,
                marital_status = ?,
                `address` = ?, 
                ssnit_number = ?, 
                gh_card_number = ?, 
                updated_at = NOW() 
              WHERE staff_id = ?";

    $stmt = $conn->prepare($query);

    if ($stmt->execute([
        $firstname,
        $surname,
        $dob,
        $gender,
        $marital_status,
        $address,
        $ssnit_number,
        $gh_card_number,
        $staff_id
    ])) {
        return ['status' => 'success', 'message' => 'Record updated successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error];
    }
}


function updateBankDetails($requestParams)
{
    global $conn;
    $staff_id = $requestParams['staff_id'];
    $bank_name = $requestParams['bank_name'];
    $bank_branch = $requestParams['branch'];
    $bank_account_number = $requestParams['acc_no'];
    

    $query = "UPDATE staff SET 
                bank_name = ?, 
                bank_branch = ?, 
                bank_account_number = ?, 
                updated_at = NOW() 
              WHERE staff_id = ?";

    $stmt = $conn->prepare($query);
    if ($stmt->execute([
        $bank_name,
        $bank_branch, 
        $bank_account_number, 
        $staff_id
    ])) {
        return ['status' => 'success', 'message' => 'Record updated successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error];
    }
}

function updateJobDetails($requestParams)
{
    global $conn;
    $staff_id = $requestParams['staff_id'];
    $job_title = $requestParams['job_title'];
    $job_email = $requestParams['job_email'];
    $departmentName = $requestParams['department'];
    $job_start_date = $requestParams['start_date'];
    

    $query = "UPDATE staff SET 
                job_title = ?, 
                job_email = ?, 
                departmentName = ?, 
                job_start_date = ?, 
                updated_at = NOW() 
              WHERE staff_id = ?";

    $stmt = $conn->prepare($query);
    if ($stmt->execute([
        $job_title, 
        $job_email, 
        $departmentName, 
        $job_start_date, 
        $staff_id
    ])) {
        return ['status' => 'success', 'message' => 'Record updated successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error];
    }
}

function updateContactDetails($requestParams)
{
    global $conn;
    
    $staff_id = $requestParams['staff_id'];
    $email = $requestParams['email'];
    $phone = $requestParams['phone'];
    $alt_phone = isset($requestParams['alt_phone']) ? $requestParams['alt_phone'] : '';

    $query = "UPDATE contact SET email=?, phone_number=?, alt_phone_number=? WHERE staff_id=?";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$email, $phone, $alt_phone, $staff_id])) {
        return ['status' => 'success', 'message' => 'Record updated successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error];
    }
    
}

function updateEmergencyContactDetails($requestParams)
{
    global $conn;

    $staff_id = $requestParams['staff_id'];
    $firstname = $requestParams['emergency_first_name'];
    $lastname = $requestParams['emergency_last_name'];
    $relationship = $requestParams['relationship'];
    $address = $requestParams['emergency_address'];
    $phone = $requestParams['emergency_phone'];


    $query = "UPDATE emergency_contact SET emergency_first_name=?, emergency_last_name=?, relationship=?, emergency_address=?, emergency_phone=?, updated_at=NOW()  WHERE staff_id=?";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([
        $firstname,
        $lastname,
        $relationship,
        $address,
        $phone,
        $staff_id
    ])) {
        return ['status' => 'success', 'message' => 'Record updated successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error];
    }
}

// update a picture

function updateProfilePicture($staff_id, $imagePath)
{

    global $conn;

    try {
        $query = "UPDATE staff SET image = ? WHERE staff_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$imagePath, $staff_id]);

        if ($stmt->rowCount() > 0) {
        return ['status' => 'success', 'message' => 'Record updated successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error];
    }
    } catch (Exception $e) {
        return ['status' => 500, 'message' => 'Failed to update profile picture: ' . $e->getMessage()];
    }

}
