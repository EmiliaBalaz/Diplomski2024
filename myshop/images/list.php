<?php
function listFiles($dir) 
{
    if ($handle = opendir($dir)) 
    {
        echo "<ul>";

        while (false !== ($entry = readdir($handle))) 
        {
            if ($entry != "." && $entry != "..") 
            {
                $path = $dir . '/' . $entry;
                
                if (is_dir($path)) 
                {
                    echo "<li><strong>$entry</strong></li>";
                    listFiles($path);
                } 
                else 
                {

                    echo "<li><a href=\"?file=" . urlencode($path) . "\">$entry</a></li>";
                }
            }
        }
        
        echo "</ul>";
        closedir($handle);
    }
}

function displayFile($file) 
{
    if (file_exists($file) && is_file($file)) 
    {
        echo "<h2>Content of: " . htmlspecialchars($file) . "</h2>";
        echo "<pre>" . htmlspecialchars(file_get_contents($file)) . "</pre>";
    } 
    else 
    {
        echo "File does not exist or is not accessible.";
    }
}


$dir = 'C:/xampp/htdocs/myshop/';
listFiles($dir);


if (isset($_GET['file']))
{
    $file = $_GET['file'];
    if (strpos(realpath($file), realpath($dir)) === 0) 
    {
        displayFile($file);
    } 
    else
    {
        echo "Invalid file path.";
    }
}
?>