<?php 
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');


function handleRegistration($username, $email, $password){
    try{
        $conn = new DbConnection();
        $db = $conn->openConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, [
            'const'=> 12
        ]);
        //check if email exist
        $sql = "select * from users where email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if($user){
            echo "<div>email is already associated with other account</div>";
            return;
        }
        //check if username exist
        $sql = "select * from users where username = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if($user){
            echo "<div>username is already associated with other account</div>";
            return;
        }
        $sql = "INSERT INTO users(username, email, password) values(?,?,?);";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword]);
        echo '<div> account created successfully ! </div>';
    }catch(Exception $e){
        echo '<div>registration failed</div>';
    }

}

$requestType = $_SERVER['REQUEST_METHOD'];
if($requestType == "POST"){
    $username = $_POST["username"];
    $password = $_POST['password'];
    $email = $_POST['email'];
    handleRegistration($username, $email, $password);
}

?>
<html>
    <h3>Registration Form</h3>
    <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
        <label for="username">username</label><br/>
        <input type="text" name="username"><br/><br/>
        <label for="email">email</label><br/>
        <input type="text" name="email"><br/><br/>
        <label for="password">password</label><br/>
        <input type="password" name="password"><br/><br/>
        <button type="submit">register</button>
    </form>
    <div>already have an account ? <a href="login.php">login</a></div>
</html>