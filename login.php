<?php

require_once('db.php'); 

session_start();

$login = $_POST['login'];
$pass = $_POST['password'];

if (empty($login) || empty($pass)) {
    echo "Заполните все поля";
    header('Location: /index5.html');
    exit; 
} else {
    $sql = "SELECT id FROM `users` WHERE login = '$login' AND pass = '$pass'"; // Изменили запрос, чтобы получить role
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['id']; 
            //$_SESSION['user_role'] = $row['role']; // Сохраняем роль в сессию 
            echo "Добро пожаловать " . $row['login'];
            header('Location: /profile.php'); 
            exit; 
        }
    } else {
        echo "Нет такого пользователя";
        header('Location: /index5.html');
        exit;
    }
}


