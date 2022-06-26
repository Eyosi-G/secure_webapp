<?php
session_start();
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
                if($row["is_disabled"]){
                    echo "<div class='error'>account is locked, can't login</div>";
                    return ;
                }
                $result = password_verify($password, $row['password']);
                if($result){    
                    session_start();  
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['id'] = $row['user_id'];
                    $_SESSION['role'] = $row['role'];
                    $conn->closeConnection();
                    switch($row['role']){
                        case "member":
                           header('location: dashboard.php');
                           die();
                           return;
                        case "moderator":
                           header('location: moderator.php');
                           die();
                           return;
                        }
                }
            }
            throw new Exception('wrong credentials');
    }catch(Exception $e){
        $conn->closeConnection();
        $message = $e->getMessage();
        echo "<p class='error'> $message<p>";
    }
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST["username"];
    $password = $_POST['password'];
    if ( isset($_POST['captcha']) && ($_POST['captcha']!="") ){
        if(strcasecmp($_SESSION['captcha'], $_POST['captcha']) != 0){
        echo "<p class='error'>Entered captcha code does not match! 
        Kindly try again.</p>";
        }else{
            handleLogin($username, $password);
        }
    }else{
        
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="../../public/styles/style.css"/>
</head>
<body>
    <h3>Login Form</h3>
    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
        <label>username</label><br/>
        <input type="text" name="username" /><br/><br/>
        <label>password</label><br/>
        <input type="password" name="password" /><br/><br/>
        

        <img src="../utils/captcha.php?rand=<?php echo rand(); ?>" id='captcha_image'>
        </p>
        <label>Enter Captcha</label><br />
        <input type="text" name="captcha" />
        <p>Can't read the image?
        <a href='javascript: refreshCaptcha();'>click here</a>
        to refresh</p>




        <button type="submit">submit</button>
    </form>
    <div>don't have account ? <a href="signup.php">register</a></div>
    <script>
    //Refresh Captcha
    function refreshCaptcha(){
        var img = document.images['captcha_image'];
        img.src = img.src.substring(
            0,img.src.lastIndexOf("?")
            )+"?rand="+Math.random()*1000;
    }
    </script>
</body>
</html>
