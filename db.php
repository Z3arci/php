<?php

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "registeratt";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die("Соединение прервано". mysqli_connect_error());
}
?>