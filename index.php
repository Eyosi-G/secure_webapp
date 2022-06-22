<?php
session_start();
if(!isset($_SESSION["role"])){
    header('location: src/views/login.php');
    die();
}else{
    $role = $_SESSION["role"];
    if($role == "moderator"){
        header('location: src/views/moderator.php');
    }else{
        header('location: src/views/dashboard.php');
    }
}

?>