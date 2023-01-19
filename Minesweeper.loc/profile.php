<?php 
session_start();
if (!$_SESSION['user']) {
    header('Location: index.php');
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

<body>
    <form>
        <img src="<?= $_SESSION['user']['avatar'] ?>" width="100" alt="">
        <h2>
            <?= $_SESSION['user']['name'] ?>
        </h2>
        <a href="#"><?= $_SESSION['user']['login'] ?></a>
        <a href="vendor/logout.php" class="logout">Выход</a>
    </form>

</body>

</html>