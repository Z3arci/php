<?php

$servername = "";
$username = "";
$password = "";
$dbname = "registeratt";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die("Соединение прервано". mysqli_connect_error());
}
?>
