<?php
session_start();
$db = new PDO('mysql:host=localhost;dbname=blog', 'navruz', 'Mqq17Kd5qbW5aWKl');
$stmt = $db->prepare('INSERT INTO users(username, email, password, fullname, dob) VALUES (?,?,?,?, CURRENT_TIMESTAMP())');
$get_user_info = $db->prepare('SELECT email, password, fullname FROM users WHERE username=?');

$username = '';
$email = '';
$pwd = '';
$fullname = '';
$confirm_pwd = '';

if(isset($_SESSION['user']) && $get_user_info->execute(array($_SESSION['user']))) {
    $row = $get_user_info->fetch();

    $username = $_SESSION['user'];
    $email = $row['email'];
    $pwd = $row['password'];
    $confirm_pwd = $row['password'];
    $fullname = $row['fullname'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_REQUEST['username'];
    $email = $_REQUEST['email'];
    $pwd = $_REQUEST['pwd'];
    $fullname = $_REQUEST['fullname'];

    $confirm_pwd = $_REQUEST['confirm_pwd'];

    if ($pwd == $confirm_pwd) {
        $stmt->execute(array($username, $email, $pwd, $fullname));
        header('Location: index.php');
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>My Blog - Registration Form</title>
    <link href="style.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php include('header.php'); ?>

<h2>User Details Form</h2>
<h4>Please, fill below fields correctly</h4>
<form action="register.php" method="post">
    <ul class="form">
        <li>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?=$username?>" required/>
        </li>
        <li>
            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" id="fullname" value="<?=$fullname?>" required/>
        </li>
        <li>
            <label for="email">Email</label>
            <input type="email" name="email" value="<?=$email?>" id="email" />
        </li>
        <li>
            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd" value="<?=$pwd?>" required/>
        </li>
        <li>
            <label for="confirm_pwd">Confirm Password</label>
            <input type="password" name="confirm_pwd" id="confirm_pwd" value="<?=$confirm_pwd?>" required />
        </li>
        <li>
            <input type="submit" value="Submit" /> &nbsp; Already registered? <a href="index.php">Login</a>
        </li>
    </ul>
</form>
</body>
</html>