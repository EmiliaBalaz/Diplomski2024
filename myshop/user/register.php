<?php
require '../config/config.php';

$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$name = "";
$password = "";
$email = "";
$phone = "";
$address = "";
$photo = "";
$errorMessage = "";
$successMessage = "";

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $name = $_POST["name"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $photo = $_FILES["photo"];
    $roleid = 1; 
    $hashedPassword = md5($password);


    do
    {
        if(empty($name) || empty($password) || empty($email) || empty($phone) || empty($address) || empty($photo['name']))
        {
            $errorMessage = "All the fields are required";
            break;
        }
        $targetDir = "../images/";
        $targetFile = $targetDir . basename($photo["name"]);

        if (!move_uploaded_file($photo["tmp_name"], $targetFile)) 
        {
            $errorMessage = "There was an error uploading your file.";
            echo $errorMessage;
            exit;
        }

        //bez parametrizovanih upita
        $sqlQuery = "INSERT INTO clients (name, password, email, phone, address, photo, roleid) VALUES ('$name', '$hashedPassword', '$email', '$phone', '$address', '$targetFile', '$roleid')";
        $result = $connection -> query($sqlQuery);

        $name = "";
        $password = "";
        $email = "";
        $phone = "";
        $address = "";

        $successMessage = "Client added correctly";

        header("location: ../index.php");
        exit;
    }
    while(false);
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
            <h2 class="title">Register</h2>
            <?php
                if(!empty($errorMessage))
                {
                    echo "
                        <div>
                            <strong class=\"error-message\">$errorMessage</strong>
                        </div>
                    ";
                }
            ?>
            <form method="post" enctype="multipart/form-data">
                <div>
                    <label class="label-color">Name</label>
                    <div>
                        <input type="text" name="name" value="<?php echo $name; ?>">
                    </div>
                </div>
                <div>
                    <label  class="label-color">Password</label>
                    <div>
                        <input type="password" name="password" value="<?php echo $password; ?>">
                    </div>
                </div>
                <div>
                    <label  class="label-color">Email</label>
                    <div>
                        <input type="text" name="email" value="<?php echo $email; ?>">
                    </div>
                </div>
                <div>
                    <label  class="label-color">Phone</label>
                    <div>
                        <input type="text" name="phone" value="<?php echo $phone; ?>">
                    </div>
                </div>
                <div>
                    <label  class="label-color">Address</label>
                    <div>
                        <input type="text" name="address" value="<?php echo $address; ?>">
                    </div>
                </div>
                <div>
                    <label  class="label-color">Photo</label>
                    <div>
                        <input type="file" name="photo" class="photo-input-color">
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
                    <div>
                        <a href="index.php" role="button" class="cancel-button">Cancel</a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </body>
</html>