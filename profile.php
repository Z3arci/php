<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/style4.css">
    <title>Профиль</title>
    <style>

        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            color: #0D0D0D;
            background-color: rgb(10, 13, 39);
        }

        .active-cotainer {
            border-radius: 10px;
            width: 300px;
            background: #f6f6f6;
            margin-left: 7px;
            margin-top: 10px;
            padding: 8px;
        }

        h2 {
            text-align: center;
            font-size: 19px;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
            margin: 10px 8px 10px 8px;
            color: #cccccc;
        }

        .news {
            height: 50vh;
            background-color: #EBECF0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        table {
            border-collapse: collapse;
            justify-content: space-between;
        }

        th {
            text-align: left;
            padding-top: 20px;
        }

        td {
            text-align: left;
            padding: 0 40px 10px 0;
        }

        .qr-contaier img {
            width: 200px; 
            height: 200px;
            height: auto;  
            margin-bottom: 10px; 
        }

        .user-list {
            list-style: none;
            padding: 0px;
            max-height: 150px; 
            overflow-y: auto;
            background-color: #EBECF0; 
            width: 250px;
        }

        .user-list li {
            margin-bottom: 10px;
        }

        .oval-lg {
            height: 52px;
            width: 240px;
            padding: 0 20px;
            border-radius: 40px;
        }

        button {
            font-family: "Montserrat", sans-serif;
            font-size: 19px;
            line-height: 18px;
            color: #6C7587;
            padding: 0 8px;
            position: relative;
            border: 3px solid rgba(255, 255, 255, 0);
            outline: none;
            text-align: center;
            background-color: #EBECF0;
            transition: all 250ms ease-in-out;
        }

        button {
            box-shadow: 8px 8px 12px -2px rgba(72, 79, 96, 0.4), -6px -6px 12px -1px white;
            cursor: pointer;
            color: #36D7E0;
        }

        button:active {
            box-shadow: inset -4px -4px 6px -1px white, inset 2px 2px 8px -1px rgba(72, 79, 96, 0.5);
            border-color: #36D7E0;
        }

        input:hover,
            button:hover {
            box-shadow: none;
            border-color: #36D7E0;
        }

        button>* {
            vertical-align: middle;
        }

        button>span:last-child {
            padding-left: 8px;
        }

        .icon-imge{
            height: 45px;
            width: 45px;
        }

        .qr-contaier {
            width: 100%; 
            display: flex;
            justify-content: center; 
        }

        .search-container {
            display: flex; 
            margin-top: 10px; 
            width: 250px; 
        }

        .search-input {
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            font-size: 16px; 
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <div class="active-cotainer">
                    <h2 class="active"> Профиль </h2>
                </div>
                <div class="news">
                    <?php
                    require_once('db.php');

                    session_start();

                    if (!isset($_SESSION['user_id'])) {
                        header("Location: index5.html"); 
                        exit;
                    }

                    $user_id = $_SESSION['user_id'];
                    $is_admin = is_admin($user_id, $conn);
                    $is_user = is_user($user_id, $conn);

                    if (!$conn) {
                        die("Ошибка подключения: " . mysqli_connect_error());
                    } else {
                        $sql = "SELECT qr_code FROM `users` WHERE id = '$user_id'";
                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $qr_code = $row['qr_code']; 
                        } else {
                            $qr_code = generateQRCode($user_id);

                            $sql = "UPDATE `users` SET qr_code = '$qr_code' WHERE id = '$user_id'";
                            mysqli_query($conn, $sql);
                        }

                        $sql = "SELECT login, `surname`, `groups` FROM `users` WHERE id = '$user_id'";
                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);

                            // Вывод данных в таблице с учетом роли пользователя
                            echo "<table>";
                            echo "<tr>"; 
                            echo "<th>ФИО:</th>";
                            echo "<th>Группа:</th>"; // Table header for Группа
                            
                            echo "</tr>";

                            echo "<tr>";
                            echo "<td>" . $row['login'] .  '<br>' . $row['surname'] . "</td>";  // Combine login and surname 
                            if (!$is_admin) {
                                echo "<td>" . $row['groups'] . "</td>"; 
                            }
                            echo "</tr>"; 

                            echo "</table>";
                        } else {
                            echo "Ошибка получения данных о пользователе. <br>";
                        }

                        if ($is_admin) {
                            echo "<div class='search-container'>"; 
                            echo "<input type='text' class='search-input' placeholder='Поиск...'>";
                            echo "</div>";

                            $sql = "SELECT id, login, `surname`, `pass`, `groups` FROM `users`";
                            $result = mysqli_query($conn, $sql);

                            if ($result && mysqli_num_rows($result) > 0) {
                                echo "<ul class='user-list'>";

                                while ($row = mysqli_fetch_assoc($result)) {
                                    if ($row['id'] != $user_id) { // Не показывать данные самого администратора
                                        echo "<li>
                                                ID: " . $row['id'] . "<br>
                                                Имя: " . $row['login'] . "<br>
                                                Фамилия: " . $row['surname'] . "<br>
                                                Пароль: " . $row['pass'] . "<br>
                                                Группа: " . $row['groups'] . "<br>
                                                <a href='edit_user.php?user_id=" . $row['id'] . "'>Редактировать</a> 
                                            </li>";
                                    }
                                }
                                echo "</ul>";
                            } else {
                                echo "Пользователи не найдены.";
                            }
                        }
                    }
                    mysqli_close($conn);

                    function is_admin($user_id, $conn) {
                        $sql = "SELECT role_id FROM `users` WHERE id = '$user_id'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        return ($row['role_id'] == 1);
                    }

                    function is_user($user_id, $conn) {
                        $sql = "SELECT role_id FROM `users` WHERE id = '$user_id'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        return ($row['role_id'] == 2); 
                    }
                    ?>

                    <?php if ($is_user): ?>
                        <div class="qr-contaier">
                            <img src="data:image/png;base64,<?php echo $qr_code; ?>" alt="QR-код">
                        </div>
                    <?php endif; ?>

                    <div class="qr-contaier">
                        <form action="logout.php" method="post">
                            <button  id="link-button" class="oval-lg" type="submit">Выйти</button>
                        </form>
                    </div>
                </div>
                <div id="formFooter">
                    <ul>
                        <li>
                            <label>
                                <input type="checkbox" name="">
                                <div class="icon-box" >
                                    <a href="chart.php"><img class="icon-imge" src="/icon-news.png" ></a>
                                </div>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" name="">
                                <div class="icon-box">
                                    <a href="schedule.php"><img class="icon-imge" src="/icon-schedule.png" ></a>
                                </div>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" name="">
                                <div class="icon-box">
                                    <i class="fa fa-snowflake-o" aria-hidden="true"></i>
                                    <a href="opros.php"><img class="icon-imge" src="/icon-chart.png" ></a>
                                </div>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" name="">
                                <div class="icon-box">
                                    <i class="fa fa-code" aria-hidden="true"></i>
                                    <a href="profile.php"><img class="icon-imge" src="/icon-profile.png" ></a>
                                </div>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <script src="http://telegram.org/js/telegram-web-app.js"></script>
        <script>
            const searchInput = document.querySelector('.search-input');
            const userList = document.querySelector('.user-list');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const listItems = userList.querySelectorAll('li');

                listItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        </script>
    </div>
</body>

</html>