<?php

if(isset($_POST['title'])){
    require '../db_conn.php';
    session_start();
    $username = $_SESSION['user'];
    $title = htmlentities($_POST['title']);

    if(empty($title)){
        header("Location: ../index.php?mess=error");
    }else {
        $cursor = $conn->prepare("INSERT INTO todos1 (user,title) value (:user, :title)");
        $cursor->execute(array(":user"=>$username, ":title"=>$title));

        if($res){
            header("Location: ../index.php?mess=success"); 
        }else {
            header("Location: ../index.php");
        }
        $conn = null;
        exit();
    }
}else {
    header("Location: ../index.php?mess=error");
}