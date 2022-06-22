<?php 
session_start();
if($_SESSION["role"] != "member"){
    header("Location: login.php");
    die();
}
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');
require_once(__ROOT__.'/utils/validate.php');
include(__ROOT__.'/components/navbar.php');



function updateFeedback($feedbackId, $comment, $attachement){
    $conn = new DbConnection();
    $db = $conn->openConnection();
    if($attachement == ""){
        $sql = "UPDATE feedbacks SET comment=? WHERE feedback_id=?;";
        $stmt = $db->prepare($sql);
        $stmt->execute([$comment, $feedbackId]);
        $row = $stmt->fetch();
        $conn->closeConnection();
        return $row;
    }else{
        $sql = "UPDATE feedbacks SET comment=?, file_name=? WHERE feedback_id=?;";
        $stmt = $db->prepare($sql);
        $stmt->execute([$comment, $attachement, $feedbackId]);
        $row = $stmt->fetch();
        $conn->closeConnection();
        return $row;
    }
}

function getFeedback(int $feedbackId, int $userId){
    $conn = new DbConnection();
    $db = $conn->openConnection();
    $sql = "SELECT * FROM feedbacks where feedback_id=? and user_id=?;";
    $stmt = $db->prepare($sql);
    $stmt->execute([$feedbackId, $userId]);
    $row = $stmt->fetch();
    $conn->closeConnection();
    return $row;
}

if($_SERVER["REQUEST_METHOD"] == "GET"){
    try{
        $feedbackId = ($_GET["id"]);
        echo validate($feedbackId);
        $userId = $_SESSION["id"];
        $feedback = getFeedback($feedbackId, $userId);
        if($feedback == null){
            throw new Exception("unauthorized access denied");
        }
    }catch(Exception $e){
        echo $e->getMessage();
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try {
        $userId = $_SESSION["id"];
        $feedbackId = ($_POST["feedback_id"]);
        $name = validate($_POST["name"]);
        $email = validate($_POST['email']);
        $comment = validate($_POST['comment']);
        $attachement = $_FILES['attachement']['name'];
        $feedback = getFeedback($feedbackId, $userId);
        if($feedback==null) throw new Exception("unauthorized access denied!");
        if(isset($attachement)){
            $ext = explode(".", $attachement)[1];
            $file_tmp = $_FILES['attachement']['tmp_name'];

            if(!isset($feedback["file_name"])){
                $ext = explode(".", $attachement)[1];
                $attachement = uniqid();
                $attachement = "$attachement.$ext";
            }else{
                $attachement = $feedback["file_name"];
            }
            $fileValidationError = validateFile($file_tmp);
            if(!$fileValidationError){
                $result = move_uploaded_file($file_tmp, __DIR__."/../../public/uploads/".$attachement);
                updateFeedback($feedbackId, $comment, $attachement );
            }

        }else{
            updateFeedback($feedbackId, $comment, "");
        }
        $feedbackId = $_GET["id"];
        $userId = $_SESSION["id"];
        $feedback = getFeedback($feedbackId, $userId);
        echo "<p class='success'>review edited succesfully !</p>";

    } catch (Exception $e) {
        echo $e->getMessage();
    }

}

?>

<h3>Edit Complaint Submiting Form !! </h3>
<form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
    <label>comment<label><br/>
    <textarea type="text" cols="21px" rows="5px" name="comment"><?php echo isset($feedback["comment"]) ? $feedback["comment"] : ""; ?></textarea> <br/><br/>
    <label>attachement<label><br/>
    <input type="file"  name="attachement"/> <br/>
    <?php echo "<p>$fileValidationError</p>"?>
    <input type="hidden" name="feedback_id" value=<?php echo isset($feedbackId) ? $feedbackId : "" ?> />
    <button type="submit">submit</button>
</form>