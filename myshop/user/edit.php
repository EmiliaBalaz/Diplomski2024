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

    if ($roleid != 1) 
    {
        http_response_code(403); 
        die("Unauthorized: Insufficient permissions.");
    }

    $email = $decoded->email;

    $stmt = $connection->prepare("SELECT * FROM clients WHERE email=?");
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {

        http_response_code(404);
        die("User not found.");
    }

    $id = $row["id"];
    $name = $row["name"];
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
            //$roleid = $_POST["roleid"];

            $stmt = $connection->prepare("UPDATE clients SET name=?, email=?, phone=?, address=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);
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
        <title>My Shop</title>
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
            h2.title
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
            }
            input.photo-input-color
            {
                color:#e3a6d5;
                margin-left: 80px;
            }
            a.cancel-button
            {
                color: #e3a6d5;
            }
            strong.error-message
            {
                color: #e3a6d5;
            }
        </style>
    </head>
    <body>
    <div class="register-container">
    <div class="register-form">
        <h2 class="title">Edit your profile</h2>
            <?php
                if(!empty($errorMessage))
                {
                    echo "
                        <div>
                            <strong>$errorMessage</strong>
                        </div>
                    ";
                }
            ?>
            
            <form method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div>
                    <label class="label-color">Name</label>
                    <div>
                        <input type="text" name="name" value="<?php echo $name; ?>">
                    </div>
                </div>
                <div>
                    <label class="label-color">Email</label>
                    <div>
                        <input type="text" name="email" value="<?php echo $email; ?>">
                    </div>
                </div>
                <div>
                    <label class="label-color">Phone</label>
                    <div>
                    <input type="text" name="phone" value="<?php echo $phone; ?>">
                    </div>
                </div>
                <div>
                    <label class="label-color">Address</label>
                    <div>
                        <input type="text" name="address" value="<?php echo $address; ?>">
                    </div>
                </div>
                <?php
                    if(!empty($successMessage))
                    {
                        echo "
                            <div>
                                <strong>$successMessage</strong>
                            </div>
                        ";
                    }
                ?>
                <div>
                    <br>
                    <div>
                        <button type="submit">Submit</button>
                    </div>
                    <h4><a href="../products.php" role="button" class="cancel-button">Products</a></h4>
                    <div>
                        <a href="index.php" role="button" role="button" class="cancel-button">Cancel</a>
                    </div>
                </div>
            </form>
            </div>
            </div>
        </body>
</html>