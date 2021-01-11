<?php
    require_once './DatabaseInterface.php';
    
    $databaseObj = new DatabaseInterface();

    /* Start session if none */
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    /* Check if the user already logged in */
    if(isset($_SESSION["user"]))
    {
        Header("Location: ./main.php");
    }

    $error_message = null;
    $success_message = null;

    if(isset($_POST['r_password']) && isset($_POST['r_username']))
    {
        $password   = $_POST['r_password'];
        $username   = $_POST['r_username'];
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $e_msg = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.<br>';
        }
        else{
            $g_msg = 'Strong password.';
        
            $return_array = $databaseObj->Register($username,$password);

            if ($return_array["success"] == false)
            {
                $error_message = $return_array["data"];
            }
            else
            {
                $success_message = $return_array["data"];
            }
        }

    }
    else if(isset($_POST['l_password']) && isset($_POST['l_username']))
    {
        $password   = $_POST['l_password'];
        $username   = $_POST['l_username'];

        $return_array = $databaseObj->Login($username, $password);

        if ($return_array["success"] == false)
        {
            $error_message = $return_array["data"];
        }
        else
        {
            /* set session */
            $_SESSION["user"]    = $return_array["data"]["username"];
            $_SESSION["userid"]  = $return_array["data"]["id"];

            /* set cookie */
            die(Header("Location: ./main.php"));
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="description" content="Login - Register Template">
    <meta name="author" content="Lorenzo Angelino aka MrLolok">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #ff0000;
        }
    </style>
    <title>Login/Regsiter</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./assets/css/main.css">
</head>
<body>

<div class="container">
    <div class="row">
            <div class="jumbotron">
            <center>
            <h1 style="color:black">Welcome To My Forum</h1>
            </center>
    </div>
</div>
    <br>
<br>
<br>


<div class="row">
    <div class="col-md-6">
    <div id="container-login">
        <div id="title">
            <i class="material-icons lock">lock</i> Login
        </div>

        <form  action="#" method="POST">
            <div class="input">
                <div class="input-addon">
                    <i class="material-icons">face</i>
                </div>
                <input id="l_email" placeholder="Username" type="text" name="l_username" required class="validate" autocomplete="off">
            </div>

            <div class="clearfix"></div>

            <div class="input">
                <div class="input-addon">
                    <i class="material-icons">vpn_key</i>
                </div>
                <input id="l_password" placeholder="Password" type="password" name="l_password" required class="validate" autocomplete="off">
            </div>

            <br>
            <br>
            <input type="submit" value="Login" />
            <br>
        </form>


        <div class="register">
            Don't have an account yet?
            <a href="#"><button id="register">Register here &#x2192;</button></a>
        </div>
    </div>


</div>

<div class="col-md-6">
    <div id="container-register">
        <div id="title">
            <i class="material-icons lock">lock</i> Register
        </div>

        <form action="#" method="POST">
            <div class="clearfix"></div>

            <div class="input">
                <div class="input-addon">
                    <i class="material-icons">face</i>
                </div>
                <input id="r_username" placeholder="Username" type="text" name="r_username" required class="validate" autocomplete="off">
            </div>

            <div class="clearfix"></div>

            <div class="input">
                <div class="input-addon">
                    <i class="material-icons">vpn_key</i>
                </div>
                <input id="r_password" placeholder="Password" type="password" name="r_password" required class="validate" autocomplete="off">
            </div>
<br>
<br>

            <input type="submit" value="Register" />
            <br>
              <?php
                if (isset($error_message))
                {
                    echo "<div class='alert alert-danger'><strong>Error:</strong>".$error_message."</div>";
                }
                else if (isset($success_message))
                {
                    echo "<div class='alert alert-success'><strong>Note:</strong>".$success_message."</div>";
                }
                else if(isset($g_msg)){
                    echo "<div class='alert alert-success'><strong>Note:</strong>".$g_msg."</div>";
                    }
                    else if(isset($e_msg)){
                    echo "<div class='alert alert-danger'><strong>Error:</strong>".$e_msg."</div>";
                    }
            ?>
        </form>

        <div class="register">
            Do you already have an account?
            <a href="#"><button id="register">&#x2190; Login here </button></a>
        </div>
    </div>
</div>
</div>
 
<script src="./assets/pages/index.js"></script>
</body>
</html>