<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');

function handleLogin($username, $password){
    try{
            $conn = new DbConnection();
            $db = $conn->openConnection();
            $sql = 'SELECT * FROM users WHERE username=?;';
            $stmt = $db->prepare($sql);
            $stmt->execute([$username]);
            $row = $stmt->fetch();

            if($row != null){
                $result = password_verify($password, $row['password']);
                if($result){
                    session_start();                
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['id'] = $row['user_id'];
                    $_SESSION['role'] = $row['role'];
                    $conn->closeConnection();
                    switch($row['role']){
                        case "member":
                           return header('location: dashboard.php');
                        case "moderator":
                           return header('location: moderator.php');
                        }
                }
            }
            throw new Exception('wrong credentials');
    }catch(Exception $e){
        $conn->closeConnection();
        header('location: '.$_SERVER['PHP_SELF']);
    }
}


if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST["username"];
    $password = $_POST['password'];
    handleLogin($username, $password);
}
?>

<html>
    <script src="load_captha.js"></script>
    <h3>Login Form</h3>
    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
        <label>username</label><br/>
        <input type="text" name="username" /><br/><br/>
        <label>password</label><br/>
        <input type="password" name="password" /><br/><br/>
        <button type="submit">submit</button>
    </form>
    <div>don't have account ? <a href="signup.php">register</a></div>
</html>
