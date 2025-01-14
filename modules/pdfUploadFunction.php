<?php
require '../../header.php';
require '../../dbcon.php';
require '../../responseStatuses.php';
require '../../utils.php';

$database = new Operations();
$conn = $database->dbconnection();

function paySlipUpload($requestParams, $uploadedFile)
{
    global $conn;

    // Validate file upload and staff ID
    if (empty($uploadedFile) || empty($requestParams['staff_id'])) {
        throw new Exception("File is missing or staff ID is missing");
    }

    $uploadDirectory = '../../uploads/';
    $uploadedFileName = $uploadedFile['name'];
    $tempFilePath = $uploadedFile['tmp_name'];

    // Validate the file type
    $fileType = mime_content_type($tempFilePath);
    if ($fileType !== 'application/pdf') {
        throw new Exception("Invalid file type. Only PDF files are allowed.");
    }

    // Ensure the upload directory exists and is writable
    if (!is_dir($uploadDirectory) || !is_writable($uploadDirectory)) {
        throw new Exception("Upload directory is not writable.");
    }

    $uploadedFile = $_FILES['file'];
    $uploadedFileName = $uploadedFile['name'];
    $tempFilePath = $uploadedFile['tmp_name'];
    $fileType = mime_content_type($tempFilePath);

    // Validate the file type
    if ($fileType !== 'application/pdf') {
        throw new Exception("Invalid file type. Only PDF files are allowed.");
    }

    // Read the file content and encode it to Base64
    $fileContent = file_get_contents($tempFilePath);
    $base64Content = base64_encode($fileContent);



    // Prepare data for database insertion
    $staff_id = htmlspecialchars($requestParams['staff_id']);
    $currentDate = new DateTime();
    $monthDate = htmlspecialchars($requestParams['month']); // The month comes as a date string
    $dateObject = new DateTime($monthDate); // Create a DateTime object from the date string
    $year = $dateObject->format('Y'); // Extract the year
    $month = $dateObject->format('m');
    $createdAt = $currentDate->format('Y-m-d');

    $query = "INSERT INTO payslip (id, staff_id, file_path, `month`, `year`, created_at, last_updated) 
                  VALUES (null, ?, ?, ?, ?, NOW(), NOW())";

    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$staff_id, $base64Content, $month, $year]);
    } catch (PDOException $e) {
        // Log the detailed SQL error
        error_log('Database error: ' . $e->getMessage());
        throw new Exception("Failed to save payslip to the database. Error: " . $e->getMessage());
    }
}

function getPaySlipById($staff_id)
{
    global $conn;
    $query = "SELECT P.*, CONCAT(S.firstname, ' ', S.surname) AS staffName 
FROM payslip AS P 
LEFT JOIN staff AS S ON P.staff_id = S.staff_id WHERE P.staff_id=?";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$staff_id])) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            $res = Utils::convertStringToJson($res);
            return $res;
        } else {
            return Null;
        }
    } else {
        return status500();
    }
}

function getAllPaySlip()
{
    global $conn;
    global $conn;
    $query = "SELECT * FROM payslip";
    $stmt = $conn->prepare($query);

    if ($stmt->execute()) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();
        if ($total > 0) {
            $res = Utils::convertStringToJson($res);
            return $res;
        } else {
            return Null;
        }
    } else {
        return status500();
    }
}

// uploading policies

function uploadPolicies($requestParams, $uploadedFile)
{
    global $conn;

    // Check if file and title are present
    if (empty($_FILES['file']) || empty($requestParams['title'])) {
        throw new Exception("File is missing or title is missing");
    }

    $uploadedFile = $_FILES['file'];
    $uploadedFileName = $uploadedFile['name'];
    $tempFilePath = $uploadedFile['tmp_name'];
    $fileType = mime_content_type($tempFilePath);

    // Validate the file type
    if ($fileType !== 'application/pdf') {
        throw new Exception("Invalid file type. Only PDF files are allowed.");
    }

    // Read the file content and encode it to Base64
    $fileContent = file_get_contents($tempFilePath);
    $base64Content = base64_encode($fileContent);

    $title = htmlspecialchars($requestParams['title']);
    $tag = htmlspecialchars($requestParams['downloadTag']) || '';
    $lastUpdated = ($requestParams['last_updated']);



    $query = "INSERT INTO policies (id, title, `file`, created_at, tag, last_updated) 
              VALUES (null, ?, ?, NOW(), ?, ?)";
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$title, $base64Content, $tag, $lastUpdated]);
    } catch (PDOException $e) {
        // Log the detailed SQL error
        error_log('Database error: ' . $e->getMessage());
        throw new Exception("Failed to save policy to the database. Error: " . $e->getMessage());
    }
}

function getPolicies()
{
    global $conn;
    $query = "SELECT * FROM policies";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([])) {
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

function deletePolicy($id)
{
    global $conn;
    $query = "DELETE FROM policies WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$id])) {
        return json_encode(array('message' => 'policy deleted successfully'));
        echo 'success';
    } else {
        return json_encode(array('message' => 'Failed to delete ploicy'));
    }
}

function deletePayslip($id)
{
    global $conn;
    $query = "DELETE FROM payslip WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$id])) {
        return json_encode(array('message' => 'policy deleted successfully'));
        echo 'success';
    } else {
        return json_encode(array('message' => 'Failed to delete ploicy'));
    }
}