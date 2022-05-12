<?php
session_start();
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<nav>
    <div><?php echo $username ?></div>
    <ul>
        <?php if($role == "member"): ?>
            <li><a href="dashboard.php">dashboard</a></li>
            <li><a href="feedback.php">feedback</a></li>
        <?php endif ?>
        <li><a href="logout.php">logout</a></li>
    </ul>
</nav>