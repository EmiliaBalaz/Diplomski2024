<?php
$url = $_GET['url'] ?? '';

$allowed_urls = [
    "http://localhost:8000/user/register.php",
    "http://localhost:8000/user/login.php"
];

function url_is_allowed($url) 
{
    global $allowed_urls;
    foreach ($allowed_urls as $allowed_url)
     {
        if (strpos($url, $allowed_url) === 0)
        {
            return true;
        }
    }
    return false;
}

if (url_is_allowed($url)) 
{
    header("Location: $url");
    exit;
} 
else
{
    echo "URL is not allowed!";
}
?>
