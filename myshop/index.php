<!DOCTYPE html>
<html>
    <head>
        <title>My shop</title>
        <style>
            div.login-container
            {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #e9cce0;
            } 
            div.login-form
            {
                width: 400px;
                padding: 20px;
                background-color: #333333;
                border-radius: 5px;
                box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            }
            a.title
            {
                font-size: 24px;
                color: #ff0099;
            }
            h1.title
            {
                margin-top: 0;
                text-align: center;
                font-size: 24px;
                margin-bottom: 20px;
                color: #e3a6d5;
            }
            div.login-div
            {
                text-align: center;
            }
            button.button-center
            {
               margin-left: 170px;
               margin-top: 20px;
            }
            button.register-button
            {
                border: none;
                background-color: white;
                border-radius: 8px;
                text-align: center;
                padding: 20px;
                cursor: pointer;
                font-size: 20px;
                color : #e3a6d5;
            }
            div.button-container 
            {
                display: flex;
                gap: 10px;
            }
        </style>
    </head>
    <body>
    <div class="button-container">
    <form action="/redirect.php" method="get">
                <input type="hidden" name="url" value="http://localhost:8000/user/register.php">
                <button type="submit" class="register-button">Register</button>
            </form>
            <form action="/redirect.php" method="get">
                <input type="hidden" name="url" value="http://localhost:8000/user/login.php">
                <button type="submit" class="register-button">Login</button>
            </form>
        </div>
    </script>
    <div class="login-container">
    </div>
    <div class="login-container">
    </div>
    </body>
</html>