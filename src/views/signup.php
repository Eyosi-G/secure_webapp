<?php 
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');
require_once(__ROOT__.'/utils/validate.php');


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
        echo $e->getMessage();
        echo '<div>registration failed</div>';
    }

}

$requestType = $_SERVER['REQUEST_METHOD'];
if($requestType == "POST"){
    $username = validate($_POST["username"]);
    $password = validate($_POST['password']);
    $email = validate($_POST['email']);
    $emailErr = validateEmail($email);
    $passwordErr = validatePassword($password);
    if(!($emailErr || $passwordErr)){
        handleRegistration($username, $email, $password);
    }
}

?>
<html>
    <head>
        <link rel="stylesheet" href="../../public/styles/style.css"/>
    </head>
    <h3>Registration Form</h3>
    <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
        <label for="username">username</label><br/>
        <input type="text" id="username" name="username"><br/>
        <div class="error" id="username_error"></div>
        <label for="email">email</label><br/>
        <input type="text" name="email" id="email"><br/>
        <div class="error" id="email_error"></div>
        <?php echo "<p class='error'>$emailErr</p>"; ?>
        <label for="password">password</label><br/>
        <input type="password" name="password" id="password"><br/>
        <div class="error" id="password_error"></div>
        <?php echo "<p class='error'>$passwordErr</p>"; ?>
        <button type="submit" id="register_button">register</button>
    </form>
    <div>already have an account ? <a href="login.php">login</a></div>
    <script>
        let username = document.getElementById("username");
        let email = document.getElementById("email");
        let password = document.getElementById("password");
        let usernameError = document.getElementById("username_error");
        let emailError = document.getElementById("email_error");
        let passwordError = document.getElementById("password_error");
        let submitButton = document.getElementById("register_button");
        
        let changeButtonDisablity = ()=>{
            let isDisabled =  usernameError.textContent || emailError.textContent || passwordError.textContent;
            if(isDisabled){
                submitButton.disabled = true;
            }else{
                submitButton.disabled = false;
            }
        }
        username.addEventListener("input", (e)=>{
            let value = e.target.value;
            if(value == ""){
                usernameError.textContent = "username is required";
            }else{
                usernameError.textContent = "";
            }
            changeButtonDisablity()
        });

        email.addEventListener("input", (e)=>{
            let value = e.target.value;
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)){
                emailError.textContent = "";
            }else{
                emailError.textContent = "invalid email format";
            }
            changeButtonDisablity()
        });

        password.addEventListener("input", (e)=>{
            let value = e.target.value;
            if (/^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(value)){
                passwordError.textContent = "";
            }else{
                passwordError.textContent = "Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.";  
            }
            changeButtonDisablity()
        });

        changeButtonDisablity();

    </script>
</html>