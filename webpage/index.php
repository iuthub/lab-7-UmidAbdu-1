<?php
session_start();

$db = new PDO('mysql:host=localhost;dbname=blog', 'navruz', 'Mqq17Kd5qbW5aWKl');
$stmt = $db->prepare('SELECT password FROM users WHERE username=?');
$get_posts_stmt = $db->prepare('SELECT fullname, title, body, publishDate FROM users u JOIN posts p ON u.id=p.userId WHERE username=?');
$add_post = $db->prepare('INSERT INTO posts(title, body, publishDate, userId) VALUES (?,?,CURRENT_DATE(), (SELECT id FROM users WHERE username=?))');

$message = '';
$username = isset($_COOKIE['username'])?$_COOKIE['username']:'';
$pwd = isset($_COOKIE['pwd'])?$_COOKIE['pwd']:'';
$rows = 0;

// no validation; in-place authentication (without a function); repeating usernames in db (not required in lab);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['pwd'])) {
        if ($stmt->execute(array($_POST['username']))) {
            $password = $stmt->fetch()['password'];
            if ($password == $_POST['pwd']) {
                $_SESSION['user'] = $_POST['username'];

                if (isset($_POST['remember'])) {
                    setcookie('username', $_POST['username'], time()+60*60*24*365);
                    setcookie('pwd', $_POST['pwd'], time()+60*60*24*365);
                }
            } else {
                $message = 'Incorrect email or password';
            }
        } else {
            $message = 'Incorrect email or password';
        }
    }

    // can only be set when $_SESSION['user'] is set
    if(isset($_POST['title']) && isset($_POST['body'])) {
        $add_post->execute(array($_POST['title'], $_POST['body'], $_SESSION['user']));
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    session_destroy();
    header("Location: index.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>My Personal Page</title>
    <link href="style.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php
include('header.php');
if (!isset($_SESSION['user'])) {
    ?>
    <!-- Show this part if user is not signed in yet -->
    <div class="twocols">
        <form action="index.php" method="post" class="twocols_col">
            <ul class="form">
                <li>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?=$username?>" />
                </li>
                <li>
                    <label for="pwd">Password</label>
                    <input type="password" name="pwd" id="pwd" value="<?=$pwd?>" />
                </li>
                <li>
                    <label for="remember">Remember Me</label>
                    <input type="checkbox" name="remember" id="remember" checked />
                    <div class="invalid"><?=$message?></div>
                </li>
                <li>
                    <input type="submit" value="Submit" /> &nbsp; Not registered? <a href="register.php">Register</a>
                </li>
            </ul>
        </form>
        <div class="twocols_col">
            <h2>About Us</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero nostrum consequatur dolor. Nesciunt eos dolorem enim accusantium libero impedit ipsa perspiciatis vel dolore reiciendis ratione quam, non sequi sit! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio nobis vero ullam quae. Repellendus dolores quis tenetur enim distinctio, optio vero, cupiditate commodi eligendi similique laboriosam maxime corporis quasi labore!</p>
        </div>
    </div>
    <?php
} else {
    $get_posts_stmt->execute(array($_SESSION['user']));
    $rows = $get_posts_stmt->fetchAll();
    ?>
    <!-- Show this part after user signed in successfully -->
    <div class="logout_panel"><a href="register.php">My Profile</a>&nbsp;|&nbsp;<a href="index.php?logout=1">Log Out</a></div>
    <h2>New Post</h2>
    <form action="index.php" method="post">
        <ul class="form">
            <li>
                <label for="title">Title</label>
                <input type="text" name="title" id="title" />
            </li>
            <li>
                <label for="body">Body</label>
                <textarea name="body" id="body" cols="30" rows="10"></textarea>
            </li>
            <li>
                <input type="submit" value="Post" />
            </li>
        </ul>
    </form>
    <div class="onecol">
        <?php
        foreach ($rows as $row) {
            ?>
            <div class="card">
                <h2><?=$row['title']?></h2>
                <h5><?=$row['fullname']?> on <?=$row['publishDate']?></h5>
                <p><?=$row['body']?></p>
            </div>
            <?php
        }
        ?>
        <div class="card">
            <h2>TITLE HEADING</h2>
            <h5>Author, Sep 2, 2017</h5>
            <p>Some text..</p>
            <p>Sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</p>
        </div>
    </div>
    <?php
}
?>
</body>
</html>