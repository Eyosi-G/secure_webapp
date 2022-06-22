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
    $sql = 'SELECT * FROM feedbacks join users on feedbacks.user_id=users.user_id WHERE feedbacks.user_id=?;';
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $feedbacks = $stmt->fetchAll();
    return $feedbacks;
}
?>

<html>
    <head>
        <link rel="stylesheet" href="../../public/styles/style.css"/>
    </head>
    <body>
        <?php try {$feedbacks = fetchPreviousFeedbacks(); } catch(Exception $e) { echo $e->getMessage();} ?>
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
                            <td><?php echo htmlspecialchars($feedback["username"]); ?></td>
                            <td><?php echo htmlspecialchars($feedback["email"]); ?></td>
                            <td><?php echo htmlspecialchars($feedback["comment"]); ?></td>
                            <td><?php echo ($feedback["file_name"] ? $feedback["file_name"] : "--") ; ?></td>
                            <td><a href=<?php echo "review_feedback.php?id={$feedback['feedback_id']}"?>>edit</a></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php endif ?>
    <body>
</html>
