<?php

class DbConnection {
    public PDO $db;
    public function openConnection(){
        $this->$db = new PDO('mysql:host=localhost;dbname=secure_app',"root");
        return $this->$db;
    } 

    public function closeConnection(){
        $this->$db = null;
    }
}

?>