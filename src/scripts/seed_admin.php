<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');

function seedAdmin($username, $password){
    try{
        $conn = new DbConnection();
        $db = $conn->openConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, [
            'const'=> 12
        ]);
        $sql = "INSERT INTO users(username, password, role) values(?,?,?);";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username, $hashedPassword, "moderator"]);

    }catch(Exception $e){
        echo $e->getMessage();
    }
  
}

seedAdmin("admin", "adminadmin");

