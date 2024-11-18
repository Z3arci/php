<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/edit-style.css">
    <title>Профиль</title>
    <style>
         #formContent {
            -webkit-border-radius: 10px 10px 10px 10px;
            border-radius: 10px 10px 10px 10px;
            background-color: #EBECF0;
            padding: 30px;
            width: 90%;
            height: 550px;
            max-width: 450px;
            position: relative;
            padding: 0px;
            -webkit-box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
            box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container label {
            margin-top: 10px;
            color: #0d0d0d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <h2 class="active"> Редакирование </h2>
                <?php
                require_once('db.php');

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $user_id = $_POST['user_id'];
                    $login = $_POST['login'];
                    $surname = $_POST['surname'];
                    $pass = $_POST['pass'];
                    $groups = $_POST['groups'];

                    $sql = "UPDATE `users` SET login = ?, `surname` = ?, `pass` = ?, `groups` = ? WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param($stmt, "sssss", $login, $surname, $pass, $groups, $user_id); 

                    if (mysqli_stmt_execute($stmt)) {
                        echo "Пользователь успешно обновлен.";
                        header("Location: profile.php"); 
                        exit;
                    } else {
                        echo "Ошибка обновления пользователя: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    if (isset($_GET['user_id'])) {
                        $user_id = $_GET['user_id'];

                        $sql = "SELECT login, `surname`, `pass`, `groups` FROM `users` WHERE id = '$user_id'";
                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            ?>

                            <form method="POST" action="edit_user.php" class="form-container">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <label for="login">Логин:</label>
                                <input class="oval-lg" type="text" name="login" value="<?php echo $row['login']; ?>" required>

                                <label for="surname">Фамилия:</label>
                                <input class="oval-lg" type="text" name="surname" value="<?php echo $row['surname']; ?>" required>

                                <label for="pass">Пароль:</label>
                                <input class="oval-lg" type="text" name="pass" value="<?php echo $row['pass']; ?>" required>

                                <label for="groups">Группа:</label>
                                <input class="oval-lg" type="text" name="groups" value="<?php echo $row['groups']; ?>" required>

                                <button id="link-button" type="submit" class="oval-lg">Сохранить</button>
                            </form>

                            <?php
                        } else {
                            echo "Ошибка получения данных о пользователе.";
                        }
                    } else {
                        echo "Некорректный запрос.";
                    }
                }

                mysqli_close($conn);
                ?>
                <a class="reg" href="profile.php">Назад</a>
            </div>
        </div>
        <script src="http://telegram.org/js/telegram-web-app.js"></script>
    </div>
</body>
</html>