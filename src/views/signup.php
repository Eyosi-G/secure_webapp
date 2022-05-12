<?php 
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');


function handleRegistration($username, $password){
    try{
        $conn = new DbConnection();
        $db = $conn->openConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, [
            'const'=> 12
        ]);
        $sql = "INSERT INTO users(username, password) values(?,?);";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username, $hashedPassword]);
        echo '<div> account created successfully ! </div>';
    }catch(Exception $e){
        echo '<div>registration failed</div>';
    }

}

$requestType = $_SERVER['REQUEST_METHOD'];
if($requestType == "POST"){
    $username = $_POST["username"];
    $password = $_POST['password'];
    handleRegistration($username, $password);
}

?>
<html>
    <h3>Registration Form</h3>
    <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
        <label for="username">username</label><br/>
        <input type="text" name="username"><br/><br/>
        <label for="password">password</label><br/>
        <input type="text" name="password"><br/>
        <button type="submit">register</button>
    </form>
    <div>already have an account ? <a href="login.php">login</a></div>
</html>