<?php
$connect = mysqli_connect($host = 'localhost', $user = 'root', $password = '', $database = 'minesweeper');
if(!$connect){
    die('Error connect to DataBase');
}
