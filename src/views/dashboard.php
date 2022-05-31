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
        <table border='1'>
            <tr>
                <th>name</th>
                <th>email</th>
                <th>comment</th>
                <th>attachement</th>
                <th>action</th>
            </tr>
            <?php foreach($feedbacks as $feedback): ?>
                <tr>
                        <td><?php echo $feedback["name"]; ?></td>
                        <td><?php echo $feedback["email"]; ?></td>
                        <td><?php echo $feedback["comment"]; ?></td>
                        <td><?php echo $feedback["file_name"]; ?></td>
                        <td><a href=<?php echo "review_feedback.php?id={$feedback['feedback_id']}"?>>edit</a></td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
</html>
