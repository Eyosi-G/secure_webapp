<?php
session_start();
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');
$role = $_SESSION['role'];
if($role != "member"){
     return header("Location: login.php");
}
include(__ROOT__.'/utils/validate.php');

include(__ROOT__.'/components/navbar.php');

function submitFeedback($name, $email, $comment, $attachement){
    $userId = $_SESSION["id"];
    $conn = new DbConnection();
    try{
        $db = $conn->openConnection();
        if(!$attachement){
            $sql= "INSERT INTO feedbacks (name, email, comment, user_id) values (?,?,?,?);";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $email, $comment, $userId]);
        }else{
            $sql= "INSERT INTO feedbacks (name, email, comment, file_name , user_id) values (?,?,?,?,?);";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $email, $comment, $attachement,  $userId]);
        }
        echo "<div>feedback successfully submitted !</div>";
    }catch(Exception $e){
        echo $e->getMessage();
        echo "<div>feedback submission failed !</div>";
    }finally{
        $conn->closeConnection();
    }

}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try{
        
        if(!isset($_POST["token"]) || validate($_POST["token"]) != $_SESSION["token"]){
            throw Exception("unauthorized access");
        }
        $name = validate($_POST["name"]);
        $email = validate($_POST['email']);
        $comment = validate($_POST['comment']);
        $attachement = $_FILES['attachement']['name'];
        $ext = explode(".", $attachement)[1];
        $attachement = uniqid();
        $attachement = "$attachement.$ext";
        $file_tmp = $_FILES['attachement']['tmp_name'];
        $result = move_uploaded_file($file_tmp, __DIR__."/../../public/uploads/".$attachement);
        submitFeedback($name, $email, $comment, $result ? $attachement : $result);
    }catch(Exception $e){
        echo $e->getMessage();
    }

}

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    }
}

?>

<h3>Complaint Submiting Form !! </h3>
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
    <label>name<label><br/>
    <input type="text"  name="name"/> <br/><br/>
    <label>email<label><br/>
    <input type="text"  name="email"/> <br/><br/>
    <label>comment<label><br/>
    <textarea type="text" cols="21px" rows="5px" name="comment"></textarea> <br/><br/>
    <label>attachement<label><br/>
    <input type="file"  name="attachement"/> <br/>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <button type="submit">submit</button>
</form>