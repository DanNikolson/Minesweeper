<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: profile.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация и регистрация</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body class="verification main">
    <form action="vendor/signin.php" method="post">
        <label for="login">Логин:</label>
        <input name="login" placeholder="Введите логин">
        <label for="password">Пароль:</label>
        <input type="password" name="password" placeholder="Введите пароль">
        <input type="submit">
        <p>Нет аккаунта? <a href="/registration.php"> Зарегистрируйтесь!</a></p>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p class="message">' . $_SESSION['message'] . '</p>';
        }
        unset($_SESSION['message']);
        ?>
    </form>

</body>

</html>