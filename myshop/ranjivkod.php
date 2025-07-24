<?php
class Product 
{
    public $id;
    public $productname;
    public $price;
    public $comments;

    public function __construct($id, $productname, $price, $comments) 
    {
        $this->id = $id;
        $this->productname = $productname;
        $this->price = $price;
        $this->comments = $comments;
    }
}

class Cart
{
    public $items = [];
    public $total = 0;

    public function __construct($items = []) 
    {
        $this->items = $items;
        $this->calculateTotal();
    }

    public function addItem($product) 
    {
        $this->items[] = $product;
        $this->calculateTotal();
    }
    
    public function applyDiscount() 
    {
        if ($this->total > 250) {
            foreach ($this->items as $item) {
                $item->price *= 0.5;
            }
            $this->calculateTotal();
        }
    }

    public function calculateTotal() 
    {
        $this->total = 0;
        foreach ($this->items as $item) {
            $this->total += $item->price;
        }
    
        $this->applyDiscount();
    }



    public function __wakeup()
    {

        $this->applyDiscount(); 

    }
}



require('config/config.php');
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($connection->connect_error) 
{
    die("Connection failed: " . $connection->connect_error);
}

function displayProducts($connection)
{
    if (isset($_POST['submit_comment']))
    {
        $product_id = $connection->real_escape_string($_POST['product_id']);
        $comment_text = $connection->real_escape_string($_POST['comment_text']);

        $insert_query = "INSERT INTO comments(product_id, comment_text) VALUES ('$product_id', '$comment_text')";

        if($connection->query($insert_query) === TRUE)
        {
            echo "Comment added successfully.";
        }
        else
        {
            echo "Error: " . $insert_query . "<br>" . $connection->error;
        }
    }

    if (isset($_GET["searchitem"])) 
    {
        $item = $_GET["searchitem"];
        $q = "SELECT p.id, p.productname, p.price, c.comment_text 
              FROM products p 
              LEFT JOIN comments c ON p.id = c.product_id 
              WHERE p.productname = '$item'";

        $result = mysqli_query($connection,$q);

        if ($result === false) 
        {
            die("Failed to execute the query: " . $connection->error);
        }

        while($row = mysqli_fetch_array($result))
        {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["productname"] . "</td>";
            echo "<td>" . $row["price"] . "</td>";
            echo "<td>" . $row["comment_text"] . "</td>";
            echo "<td><a href='?add_to_cart=" . $row["id"] . "'>Add to Cart</a></td>";
            echo "</tr>";
        }
    }
}

if (isset($_GET['add_to_cart'])) 
{
    $productId = intval($_GET['add_to_cart']);
    
    $productQuery = "SELECT id, productname, price FROM products WHERE id = $productId";
    $productResult = $connection->query($productQuery);
    $product = $productResult->fetch_assoc();
    
    if ($product) 
    {
        $productObj = new Product($product['id'], $product['productname'], $product['price'], []);

        if (!isset($_COOKIE['cart'])) 
        {
            $cart = new Cart();
        } 
        else 
        {
            $cart = unserialize(base64_decode($_COOKIE['cart']));
            $cart->applyDiscount();
        }

        $cart->addItem($productObj);

        setcookie('cart', base64_encode(serialize($cart)), time() + (86400), "/");
        
        echo "Product added to cart!";
    } 
    else 
    {
        echo "Product not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products</title>
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
        table.table-color
        {
            color:#e3a6d5;
        }
        h3.h3-color
        {
            color:#e3a6d5;
        }
        label.label-color
        {
            color:#e3a6d5;
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="register-form">
    <div>
            <form method="GET" autocomplete="off">
                <table class="table-color">
                    <tr>
                        <td>Search for a product:</td>
                        <td><input type="text" id="searchitem" name="searchitem" value="<?php echo htmlspecialchars(isset($_GET["searchitem"]) ? $_GET["searchitem"] : ''); ?>">&nbsp;&nbsp;</td>
                        <td><input type="submit" value="Search!"/></td>
                    </tr>
                </table>
            </form>
        </div>

        <br />
        <div>
            <form method="POST" action="">
                <h3 class="h3-color">Add a comment</h3>
                <label for="product_id" class="label-color">Product ID:</label>
                <input type="number" id="product_id" name="product_id" required>
                <br>
                <label for="comment_text" class="label-color">Comment:</label>
                <textarea id="comment_text" name="comment_text" rows="4" required></textarea>
                <br>
                <input type="submit" name="submit_comment" value="Submit Comment">
            </form>
        </div>

        <br />

        <div>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Comments</th>
                        <th>Add to cart</th>
                    </tr>
                </thead>
                <tbody>
                <?php displayProducts($connection);  ?>  
                </tbody>
            </table>
        </div>
        <div class="cart-summary">
    <h3 class="h3-color">Cart summary</h3>
    <?php
    if (!isset($_COOKIE['cart'])) {
        echo "Your cart is empty.";
    } else {
        $cart = unserialize(base64_decode($_COOKIE['cart']));
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>Name of product</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($cart->items as $item) {
            echo "<tr>
                    <td>" . htmlspecialchars($item->productname) . "</td>
                    <td>$" . number_format($item->price, 2) . "</td>
                  </tr>";
        }

        echo "</tbody>
              </table>";
        echo "<h3 class='h3-color'>Total price: $" . number_format($cart->total, 2) . "</h3>";
    }
    ?>
    </div>
</div>


        <br />
        <div>
            <p><h4><a href="index.php">Home</a></h4></p>
        </div>
    </div>
</body>
</html>
