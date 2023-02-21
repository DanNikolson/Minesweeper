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

<body class="profile">
    <div class="main">
        <div class="profile-picture"><img src="<?= $_SESSION['user']['avatar'] ?>" alt="">
        <div class="leaderboard">
                <h2>Количество побед</h2>
                <table>
                    <tr>
                        <td>Ранг</td>
                        <td>Имя</td>
                        <td>Фамилия</td>
                        <td>Количество побед</td>
                    </tr>
                    <?php include 'vendor/leaderboard.php'?>
                </table>
            </div>
        </div>

        <div class="game">
            <div class="game-left">
            </div>
            <div class="game-center">
                <h2>
                    Доброго времени суток, <?= $_SESSION['user']['name'] ?> !
                </h2>
                <a href="#"><?= $_SESSION['user']['login'] ?></a>
                <a href="vendor/logout.php" class="logout">Выход</a>
                <h1>Сапёр</h1>
                <?php
                include('minesweeper.php');
                ?>
            </div>
            <div class="game-right">
            </div>
        </div>
    </div>
</body>

</html>