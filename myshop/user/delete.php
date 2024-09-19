<?php
require('../config/config.php');


    if(isset($_GET["id"]))
    {
        $id = $_GET["id"];

        $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ($connection->connect_error) 
        {
            die("Connection failed: " . $connection->connect_error);
        }
        $sqlQuery = "DELETE FROM clients WHERE id=$id";
        $connection -> query($sqlQuery);
    }

    header("location: users.php");
    exit;
?>