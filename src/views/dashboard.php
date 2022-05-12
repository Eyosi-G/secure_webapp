<?php
session_start();
if($_SESSION["role"] != "member"){
    header("Location: login.php");
}
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');
include(__ROOT__.'/components/navbar.php');

function fetchPreviousFeedbacks(){
    $userId = $_SESSION["id"];
    $conn = new DbConnection();
    $db = $conn->openConnection();
    $sql = 'SELECT * FROM feedbacks WHERE user_id=?;';
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $feedbacks = $stmt->fetchAll();
    return $feedbacks;
}
?>

<html>
    <?php try {$feedbacks = fetchPreviousFeedbacks(); } catch(Exception $e) { echo "couldn't fetch feedbacks"; } ?>
    <?php if(isset($feedbacks) && count($feedbacks) == 0): ?>
        <div>no feedbacks yet</div>
    <?php endif ?>
    <?php if(isset($feedbacks) && count($feedbacks) > 0): ?>
        <?php foreach($feedbacks as $feedback): ?>
            <fieldset>
                <div>name: <?php echo $feedback["name"] ?></div>
                <div>email: <?php echo $feedback["email"] ?></div>
                <div>comment: <?php echo $feedback["comment"] ?></div>
                <div>attachement</div>
            </fieldset>
        <?php endforeach ?>
    <?php endif ?>
    <br/>
</html>
