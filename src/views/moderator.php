<?php
session_start();
if($_SESSION["role"] != "moderator"){
    header("Location: login.php");
    die();
}
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/connection.php');
include(__ROOT__.'/components/navbar.php');


function fetchAccounts(){
    $conn = new DbConnection();
    $db = $conn->openConnection();
    $sql = "SELECT * FROM users where role='member'";
    $stmt = $db->prepare($sql);
    $stmt->execute([]);
    $users = $stmt->fetchAll();
    $conn->closeConnection();
    return $users;
}

function handleDisableAccount($userId, $isDisabled){
    $conn = new DbConnection();
    $db = $conn->openConnection();
    $sql = "UPDATE users set is_disabled=? WHERE user_id=?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$isDisabled, $userId]);
}

function fetchFeedbacks(){
    $conn = new DbConnection();
    $db = $conn->openConnection();
    $sql = "SELECT * FROM feedbacks join users on feedbacks.user_id=users.user_id";
    $stmt = $db->prepare($sql);
    $stmt->execute([]);
    $feedbacks = $stmt->fetchAll();
    $conn->closeConnection();
    return $feedbacks;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try {
        $isDisabled = $_POST['is_disabled'];
        $isDisabled = isset($isDisabled) ? 1 : 0;
        $userId = $_POST['user_id'];
        handleDisableAccount($userId, $isDisabled);
    } catch (Throwable $th) {
        echo $th->getMessage();
        echo "<div class='error'>couldn't disable user !</div>";
    }
}



?>


<html>
<head>
    <link rel="stylesheet" href="../../public/styles/style.css"/>
</head>
<body>
    <div>
        <h3>members account</h3>
        <?php try { $users = fetchAccounts();} catch(Exception $e) { echo "<p class='error'>couldn't fetch users</p>"; } ?>
        <?php if(isset($users) && count($users) == 0) :?>
            <div>empty users list</div>
        <?php endif ?>
        <?php if(count($users) > 0): ?>
            <table border="1">
            <tr>
                <td>username</td>
                <td>disabled</td>
            </tr>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $user["username"] ?></td>
                        <td>
                            <form action='<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>' method='POST'>
                                <input type='hidden' name='user_id' value=<?php echo $user['user_id'] ?> />
                                <input onChange='this.form.submit()' type='checkbox' name='is_disabled' value=<?php echo $user['is_disabled']?>  <?php echo($user["is_disabled"] ? "checked" : "") ?>  />
                            </form>
                        </td>
                    </tr>
                
                <?php endforeach ?>
            </table>
        <?php endif ?>
        <h3>feedbacks</h3>
        <?php try{ $feedbacks = fetchFeedbacks(); } catch(Exception $e) { echo "<p class='error'>couldn't fetch feedbacks</p>"; } ?>
        <?php if(isset($feedbacks) && count($feedbacks) == 0) :?>
            <div>no feedback yet</div>
        <?php endif ?>
        <?php if(count($feedbacks) > 0): ?>
            <table border="1">
            <tr>
                <td>username</td>
                <td>email</td>
                <td>comments</td>
                <td>attachement</td>
            </tr>
                <?php foreach($feedbacks as $feedback): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($feedback["username"]); ?></td>
                        <td><?php echo htmlspecialchars($feedback["email"]); ?></td>
                        <td><?php echo htmlspecialchars($feedback["comment"]); ?></td>
                        <td><?php echo htmlspecialchars($feedback["file_name"]); ?></td>
                    </tr>
                
                <?php endforeach ?>
            </table>
        <?php endif ?>
    </div>
</body>
</html>