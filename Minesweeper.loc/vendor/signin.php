<?php
session_start();
require_once 'connect.php';

$login = $_POST['login'];
$password = md5($_POST['password']);

$CheckUser = mysqli_query($connect, "SELECT * FROM users WHERE login = '$login' AND password = '$password'");
if (mysqli_num_rows($CheckUser) > 0) {

    $user = mysqli_fetch_assoc($CheckUser);

    $_SESSION['user'] = [
        "id" => $user['id'],
        "name" => $user['name'],
        "avatar" => $user['avatar'],
        "login" => $user['login']
    ];

    header('Location: ../profile.php');
} else {
    $_SESSION['message'] = 'Не верный логин или пароль';
    header('Location: ../index.php');
};?>
<pre>
    <?php
    print_r($CheckUser);
    print_r($user);
    ?>
</pre>