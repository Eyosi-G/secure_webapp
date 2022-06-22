<?php
session_start();
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');
$role = $_SESSION['role'];
if($role != "member"){
    header("Location: login.php");
    die();
    return;
}
include(__ROOT__.'/utils/validate.php');

include(__ROOT__.'/components/navbar.php');

include(__ROOT__.'/utils/helper.php');


function submitFeedback($comment, $attachement){
    $userId = $_SESSION["id"];
    $conn = new DbConnection();
    try{
        $db = $conn->openConnection();
        if(!$attachement){
            $sql= "INSERT INTO feedbacks (comment, user_id) values (?,?);";
            $stmt = $db->prepare($sql);
            $stmt->execute([$comment, $userId]);
        }else{
            $sql= "INSERT INTO feedbacks (comment, file_name , user_id) values (?,?,?);";
            $stmt = $db->prepare($sql);
            $stmt->execute([$comment, $attachement,  $userId]);
        }
        echo "<div class='success'>feedback successfully submitted !</div>";
    }catch(Exception $e){
        echo $e->getMessage();
        echo "<div class='error'>feedback submission failed !</div>";
    }finally{
        $conn->closeConnection();
    }

}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try{
        
        if(!isset($_POST["token"]) || validate($_POST["token"]) != $_SESSION["token"]){
            throw Exception("unauthorized access");
        }

        if($_POST["user_id"] != "12"){
            $file = fopen(__DIR__."/../../public/uploads/log.txt", "w");
            $userId = $_SESSION["id"];
            $line = "user id - $userId \n";
            fwrite($file, $line);
            fclose($file);
        }
        $comment = validate($_POST['comment']);
        $attachement = $_FILES['attachement']['name'];
        $ext = explode(".", $attachement)[1];
        $attachement = uniqid();
        $attachement = "$attachement.$ext";
        $file_tmp = $_FILES['attachement']['tmp_name'];
        $fileValidationError = validateFile($file_tmp);
        if(!$fileValidationError){
            $result = move_uploaded_file($file_tmp, __DIR__."/../../public/uploads/".$attachement);
            submitFeedback($comment, $result ? $attachement : $result);
        }
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
<html>
<head>
    <link rel="stylesheet" href="../../public/styles/style.css"/>
</head>
<body>
    <h3>Complaint Submiting Form !! </h3>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
        <label>comment<label><br/>
        <textarea type="text" cols="21px" rows="5px" name="comment"></textarea> <br/><br/>
        <label>attachement<label><br/>
        <input type="file"  name="attachement"/> <br/>
        <?php echo "<p class='error'>$fileValidationError</p>";?>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
        <input type="hidden" name="user_id" value="12">
        <button type="submit">submit</button>
    </form>
</body>