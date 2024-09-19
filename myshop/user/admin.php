<?php
require('C:/xampp/htdocs/myshop/vendor/autoload.php');
require '../config/config.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($connection->connect_error) 
{
    http_response_code(500);
    die("Connection failed: " . $connection->connect_error);
}

$secretKey = SECRET_KEY;

$id = "";
$name = "";
$phone = "";
$address = "";
$roleid = "";
$errorMessage = "";
$successMessage = "";

$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) 
{
    http_response_code(403);
    die("Unauthorized: Token is missing.");
}

try 
{
    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $userId = $decoded->id;
    $roleid = $decoded->roleid;
    $email = $decoded->email;

    if ($roleid != 0) 
    {
        http_response_code(403); 
        die("Unauthorized: Insufficient permissions.");
    }

    $stmt = $connection->prepare("SELECT * FROM clients WHERE email=?");
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) 
    {

        http_response_code(404);
        die("User not found.");
    }

    $id = $row["id"];
    $name = $row["name"];
    $email = $row["email"];
    $phone = $row["phone"];
    $address = $row["address"];
    $roleid = $row["roleid"];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
        if (empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["phone"]) || empty($_POST["address"])) 
        {
            http_response_code(400);
            $errorMessage = "All fields are required.";
        } 
        else 
        {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $address = $_POST["address"];
            $roleid = $_POST["roleid"];

            $stmt = $connection->prepare("UPDATE clients SET name=?, email=?, phone=?, address=?, roleid=? WHERE id=?");
            $stmt->bind_param("ssssii", $name, $email, $phone, $address, $roleid, $id);
            $result = $stmt->execute();
            $stmt->close();

            if (!$result) 
            {
                http_response_code(500);
                $errorMessage = "Invalid query: " . $connection->error;
            } 
            else 
            {
                http_response_code(200);
                $successMessage = "Client updated correctly.";
                header("Location: index.php");
                exit;
            }
        }
    }
}
catch (Exception $e) 
{
    http_response_code(403); 
    die("Invalid token: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Panel</title>
        <style>
            div.register-container
            {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background-color: #e9cce0;
            }
            div.register-form
            {
                margin-top: 80px;
                width: 400px;
                padding: 40px;
                background-color: #333333;
                border-radius: 5px;
                box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3);
                display: flex;
                flex-direction: column;
                align-items: center; 
                justify-content: center;
                text-align: center; 
            }
            a.cancel-button
            {
                color: #e3a6d5;
            }</style>
    </head>
    <body>
    <div class="register-container">
    <div class="register-form">
    <div>
        <a href="users.php" role="button" role="button" class="cancel-button">List of users</a>
    </div>
    </div>
    </div>
    </body>
</html>
