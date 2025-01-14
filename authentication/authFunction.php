<?php
define('BASE_URL', 'http://localhost/selfservice/');

require '../vendor/autoload.php';
require '../header.php';
require '../dbcon.php';
require '../responseStatuses.php';
require '../utils.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


$database = new Operations();
$conn = $database->dbconnection();


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Function to generate JWT token
function generateToken($userData)
{

    $secret_key = SECRET_KEY;
    $payload = [
        'userData' => $userData,
        'exp' => time() + 60
    ];

    return JWT::encode($payload, $secret_key, 'HS256');
    // global $token;
    // $token = JWT::encode($payload, $secret_key, 'HS256');
}




// login
function authenticateUser($username, $password)
{
    global $conn;
    $query = "SELECT U.*, S.departmentName, U.password as hashed_password 
              FROM users AS U 
              LEFT JOIN staff AS S ON U.staff_id = S.staff_id
              WHERE U.username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$username])) {
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            if (password_verify($password, $res['hashed_password'])) {
                unset($res['hashed_password']);
                return $res;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// validate token
function validateToken($token)
{
    $secret_key = SECRET_KEY;
    try {
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        return array(
            'user_id' => $decoded->userData->staff_id,
            'user_role' => $decoded->userData->user_role,
            'departmentName' => $decoded->userData->departmentName,
        );
    } catch (\Firebase\JWT\ExpiredException $e) {
        // Return a specific error response for expired tokens
        http_response_code(401); // Unauthorized
        echo json_encode(array('error' => 'Token expired'));
        exit();
    } catch (Exception $e) {
        // Return a specific error response for invalid tokens
        http_response_code(401); // Unauthorized
        echo json_encode(array('error' => 'Invalid token'));
        exit();
    }
}

// request for password request

function requestPasswordReset($email)
{
    global $conn;

    // Check if the email exists in the database
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 0) {
        return 'Email address not found in the system.';
    }

    $token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $updateQuery = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE username = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute([$token, $expiry, $email]);

    // Generate the password reset link
    $resetLink = "http://localhost:4200/reset-password?token=$token";
    $subject = 'Password Reset Request';
    $body = "Please click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";

    // Send the email
    sendEmail($email, $subject, $body);

    return 'Password reset email sent.';
}
// end



// send message
function sendEmail($to, $subject, $body)
{
    global $token;

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address";
        return;
    }
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true); 
 
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '7c75e92238bc65';
        $mail->Password = '66c3d0bedb091c';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525;
        $mail->setFrom('contact@example.com', 'sidrid-selfservice');
        $mail->addAddress($to);
        $mail->addReplyTo('contact@example.com');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();

        $success_message = 'An email is sent to your email address. Please check that and confirm password reset.';
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}
// end

// resetPssd
function resetPassword($token, $new_password)
{
    global $conn;
    

    // $hardcodedtoken = '6769409f94250f06367285eb1c4249d5';

    // Check if the token exists and has not expired
    $checkQuery = "SELECT password, reset_token_expiry FROM users WHERE reset_token = ?";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->execute([$token]);
    $user = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log('Token not found in the database: ' . $token);
        return 'Failed to reset password: User not found.';
    }

    // Check if token is expired
    if (strtotime($user['reset_token_expiry']) < time()) {
        return 'Failed to reset password: Token has expired.';
    }

    $existingPassword = $user['password'];

    // Check if the new password is different from the old one
    if (password_verify($new_password, $existingPassword)) {
        return 'New password cannot be the same as the old password.';
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Proceed with updating the password and clearing the reset token
    $query = "UPDATE users SET `password` = ?, reset_token = '' WHERE reset_token = ?";
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$hashed_password, $token]);

        if ($stmt->rowCount() > 0) {
            return 'Password reset successfully.';
        } else {
            return 'Failed to reset password: No rows affected.';
        }
    } catch (Exception $e) {
        return 'Failed to reset password: ' . $e->getMessage();
    }
}

