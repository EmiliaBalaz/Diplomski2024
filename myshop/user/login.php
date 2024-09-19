<?php
require('C:/xampp/htdocs/myshop/vendor/autoload.php');
require '../config/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$email = "";
$password = "";
$errorMessage = "";

function log_event($event) 
{
    $file = 'login_logs.log';
    $current = file_get_contents($file);
    $current .= $event . "\n";
    file_put_contents($file, $current);
}

function sanitize_input($data) 
{
    return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
}

function hash_password($password) 
{
    return md5($password);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if (!empty($_POST["email"]) && !empty($_POST["password"])) 
    {
        $email = sanitize_input($_POST["email"]);
        $password = sanitize_input($_POST["password"]);
        $hashedPassword = hash_password($password);

        log_event("Login attempt: email: $email - " . date('Y-m-d H:i:s'));

        $sqlQuery = "SELECT * FROM clients WHERE email='".$email."' AND password = '".$hashedPassword."'";
        $result = $connection->query($sqlQuery);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $roleid = $row['roleid'];
            $userId = $row['id'];
            $phone = $row['phone'];
            $address = $row['address'];
            $name = $row['name'];

            $payload = array
            (
                "id" => $userId,
                "email" => $email,
                "phone" => $phone,
                "address" => $address,
                "roleid" => $roleid,
                "name" => $name,
                "exp" => time() + 3600
            );
            $token = JWT::encode($payload, SECRET_KEY, 'HS256');

            log_event("Successful login: email: $email - " . date('Y-m-d H:i:s'));

            if ($roleid == 0) 
            {
                header("Location: admin.php?token=" . urlencode($token)); //?token= . urlencode($token)
            } 
            else if ($roleid == 1)
            {
                header("Location: edit.php?token=" . urlencode($token)); //?email=" . urlencode($email). "&token=" . urlencode($token)
            }
            exit;

        } 
        else
        {
            $errorMessage = "Invalid email or password.";
            log_event("Unsuccessful login attempt: email: $email - " . date('Y-m-d H:i:s'));
        }
    } 
    else
    {
        $errorMessage = "Both fields are required.";
        log_event("Login attempt with empty fields. - " . date('Y-m-d H:i:s'));
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            div.login-container
            {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #e9cce0;
            }
            .login-form 
            {
                width: 400px;
                padding: 20px;
                background-color: #333333;
                border-radius: 5px;
                box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            }
            h1.title
            {
                margin-top: 0;
                text-align: center;
                font-size: 24px;
                margin-bottom: 20px;
                color: #e3a6d5;
            }
            label.label-color
            {
                color:#e3a6d5;
                margin-left: 180px;
            }
            div.centered
            {
                margin-left: 120px;
            }
            button.button-design
            {
                margin-left: 180px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-form">
                <form action="" method="post">
                    <h1 class="title">Login</h1>
                    <label class="label-color">Email</label>
                    <div class="centered">
                        <input type="text" name="email">
                    </div>
                    
                    <label class="label-color">Password</label>
                    <div class="centered">
                        <input type="password" name="password">
                    </div>
                    <br>
                    <div>
                        <button type="submit" class="button-design">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>