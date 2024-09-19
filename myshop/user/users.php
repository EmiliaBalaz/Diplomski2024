<?php
require('../config/config.php');
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($connection->connect_error) 
{
    die("Connection failed: " . $connection->connect_error);
}

function displayUsers($connection)
{

    $q = "SELECT id, name, email, phone, address FROM clients";

    $result = mysqli_query($connection, $q);

    
    if ($result === false) 
    {
        die("Failed to execute the query: " . $connection->error);
    }

        while($row = mysqli_fetch_array($result))
        {
            $id = $row["id"];
            echo "<tr>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["phone"] . "</td>";
            echo "<td>" . $row["address"] . "</td>";
            echo "<td>";
            echo "<form method='GET' action='delete.php'>";
            echo "<input type='hidden' name='id' value='" . $id . "'>";
            echo "<input type='submit' value='Delete'>";
            echo "</form>";
            echo "</td>"; 
            echo "</tr>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>Search Products</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div>
        <div>
            <table border="1">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                <?php displayUsers($connection);  ?>  
                </tbody>
            </table>
        </div>
        <div>
            <a href="../index.php">index</a>
        </div>
        <br />
    </div>
</body>
</html>
