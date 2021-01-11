<?php 

$sName = "localhost";
$uName = "USERNAME";//change here to your username
$pass = "PASSWORD";//change here to your password
$db_name = "forum";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", 
                    $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  echo "Connection failed : ". $e->getMessage();
}