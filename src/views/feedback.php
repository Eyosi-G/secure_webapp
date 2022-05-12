<?php
session_start();
$role = $_SESSION['role'];
if($role != "member"){
     return header("Location: login.php");
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $name = $_POST["name"];
    $email = $_POST['email'];
    $comment = $_POST['comment'];
    // $attachement = $_POST['attachement']
    $files = $_FILES["attachement"];
    print_r($files);
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
    <button type="submit">submit</button>
</form>