<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');

function seedAdmin($username, $email, $password){
    try{
        $conn = new DbConnection();
        $db = $conn->openConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, [
            'const'=> 12
        ]);
        $sql = "INSERT INTO users(username, email, password, role) values(?,?,?,?);";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword, "moderator"]);

    }catch(Exception $e){
        echo $e->getMessage();
    }
  
}

seedAdmin("admin", "admin@gmail.com","adminadmin");

