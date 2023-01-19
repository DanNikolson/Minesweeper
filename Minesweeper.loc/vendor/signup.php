<?php
session_start();
require_once 'connect.php';

$name = $_POST['name'];
$surname = $_POST['surname'];
$DateOfBirth = $_POST['DateOfBirth'];
$login = $_POST['login'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];

if ($password === $password_confirm) {
    $path = 'uploads/' . time() . $_FILES['avatar']['name'];
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], '../' . $path)) {
        $_SESSION['message'] = 'Ошибка при загрузке файла';
        header('Location: ../registration.php');
    }

    $password = md5($password);

    if (mysqli_query($connect, "INSERT INTO `users` (`id`, `name`, `surname`, `DateOfBirth`, `login`, `password`, `avatar`) VALUES (NULL, '$name', '$surname', '$DateOfBirth', '$login', '$password', '$path')")) {
        $_SESSION['message'] = 'Регистрация прошла успешно!';
        header('Location: ../index.php');
    }
} else {
    $_SESSION['message'] = 'Пароли не совпадают';
    header('Location: ../registration.php');
}
