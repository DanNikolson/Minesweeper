<?php
session_start();
require_once 'connect.php';
$result = mysqli_query($connect, "SELECT name, surname , Victory_Count FROM users ORDER BY Victory_Count DESC");
$ranking = 1;
if (mysqli_num_rows($result)) {
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>
        <td>{$ranking}</td>
        <td>{$row['name']}</td>
        <td>{$row['surname']}</td>
        <td>{$row['Victory_Count']}</td>
        </tr>";
        $ranking++;
    }
}
?>