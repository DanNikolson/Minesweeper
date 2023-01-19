<?php 
session_start();
if ($_SESSION['user']) {
    header('Location: profile.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <form action="vendor/signup.php" method="post" enctype="multipart/form-data">
        <label for="name">Имя:</label>
        <input name="name" placeholder="Введите своё полное имя">
        <label for="surname">Фамилия:</label>
        <input name="surname" placeholder="Введите свою фамилию">
        <label for="age">Возраст:</label>
        <input type="date" name="DateOfBirth" min="1923-01-01" max="2018-12-31" placeholder="Введите свою дату рождения">
        <label for="login">Логин:</label>
        <input name="login" placeholder="Введите логин">
        <label for="picture">Изображение профиля</label>
        <input type="file" name="avatar">
        <label for="password">Пароль:</label>
        <input type="password" name="password" placeholder="Введите пароль">
        <label for="password">Повторение пароля:</label>
        <input type="password" name="password_confirm" placeholder="Подтвердите пароль">
        <input type="submit">
        <p>Уже зарегистрированы? <a href="/index.php"> Авторизируйтесь!</a></p>
        <?php

        if(isset($_SESSION['message'])){
            echo '<p class="message">'.$_SESSION['message'].'</p>';
        }
        unset($_SESSION['message']);
        ?>
    </form>
</body>

</html>