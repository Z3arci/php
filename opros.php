<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/opros.css">
    <title>Опрос</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        #formContent {
            -webkit-border-radius: 10px 10px 10px 10px;
            border-radius: 10px 10px 10px 10px;
            background-color: #EBECF0;
            padding: 30px;
            width: 90%;
            height: 520px;
            max-width: 450px;
            position: relative;
            padding: 0px;
            -webkit-box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
            box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
            text-align: center;
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

        a {
            color: #36D7E0;
            display: inline-block;
            text-decoration: none;
            padding-top: 5px;
        }

        .news {
            height: 50vh;
            background-color: #EBECF0;
            position: relative;
        }

        #myChart {
            width: 315px; /* Задайте ширину canvas */
            height: 200px; /* Задайте высоту canvas */
            position: absolute; /* Позиционирование canvas */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Центрирование canvas */
        }

        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 30%; 
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-text {
            margin-top: 10px;
        }

        #reason-field {
            display: none; 
            margin-top: 10px; 
        }

        .form{
            padding-top: 40px;
        }

        .icon-imge{
            height: 45px;
            width: 45px;
        }

        .char-button {
            position: absolute;
            bottom: 0px; 
            left: 50%;
            transform: translateX(-50%); 
        }

        .chart-results {
            position: absolute;
            bottom: 20px; /* Позиционирование под графиком */
            left: 25%;
            transform: translateX(-10%); 
            font-size: 18px;
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <div class="active-cotainer">
                    <h2 class="active"> Опрос </h2>
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

                    $showThanks = false;
                    $showChart = false;

                    $is_admin = is_admin($user_id, $conn);
                    $is_user = is_user($user_id, $conn);

                    $yes_votes = getYesVotes($conn);
                    $no_votes = getNoVotes($conn);

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $answer = $_POST["answer"];
                        $reason = isset($_POST["reason"]) ? $_POST["reason"] : "";

                        $sql = "SELECT COUNT(*) AS voted FROM `anser` WHERE user_id = '$user_id' AND opros_id = '1'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);

                        if ($row['voted'] == 0) {
                            $sql = "INSERT INTO `anser` (`answer`, `user_id`, `opros_id`, `reason`) VALUES ('$answer', '$user_id', '1', '$reason')"; // Добавлено reason в SQL
                            if (mysqli_query($conn, $sql) === TRUE) {
                                $showThanks = true;
                                $_SESSION['voted'] = true; 
                            } else {
                                echo "Ошибка: " . $sql . "<br>" . mysqli_error($conn);
                            }
                        } else {
                            echo "Вы уже голосовали!";
                            // $showChart = true;  // График всегда отображается
                        }
                    }

                    $sql = "SELECT COUNT(*) AS voted FROM `anser` WHERE user_id = '$user_id' AND opros_id = '1'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);

                    if ($is_user && !checkIfVoted($user_id, $conn)) { 
                        ?>
                        <form class="form" action="opros.php" method="post">
                            <h2 class="opros-text1">
                                Нравится ли вам приложение?
                            </h2>
                            <input type="radio" name="answer" value="yes" required> Да
                            <input type="radio" name="answer" value="no" id="noAnswer" required> Нет
                            <br><br>
                            <div id="reason-field">
                                <textarea id="reason" name="reason" placeholder="Введите причину..."></textarea>
                            </div>
                            <button id="link-button" type="submit" class="oval-lg">Отправить</button>
                        </form>
                        <script>
                            document.getElementById('noAnswer').addEventListener('click', function() {
                                document.getElementById('reason-field').style.display = 'block';
                            });
                            document.querySelector('input[name="answer"][value="yes"]').addEventListener('click', function() {
                                document.getElementById('reason-field').style.display = 'block';
                            });
                        </script>
                        <?php
                    } else if ($is_admin || checkIfVoted($user_id, $conn)) { 
                        ?>
                        <canvas id="myChart" data-yes-votes="<?php echo $yes_votes; ?>" data-no-votes="<?php echo $no_votes; ?>"></canvas>
                        <div class="chart-results">
                            <?php
                            $total_votes = $yes_votes + $no_votes;
                            if ($total_votes > 0) {
                                $yes_percentage = round(($yes_votes / $total_votes) * 100, 2);
                                $no_percentage = round(($no_votes / $total_votes) * 100, 2);

                                echo "Да: $yes_percentage%";
                                echo " Нет: $no_percentage%";
                            } else {
                                echo "Нет голосов.";
                            }
                            ?>
                        </div>
                        <?php if ($is_admin): ?>
                            <div class="char-button">
                                <form action="reason.php" method="post">
                                    <button  id="link-button" class="oval-lg" type="submit">Коментарии</button>
                                </form>
                            </div>
                        <?php endif; ?>
                        <?php
                    }

                    if ($showThanks) {
                        echo "<h3>Спасибо за ваш отзыв!</h3>";
                    }

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

                    function getYesVotes($conn) {
                        $sql = "SELECT COUNT(*) AS yes_votes FROM `anser` WHERE `answer` = 'yes' AND `opros_id` = '1'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        return $row['yes_votes'];
                    }
                    
                    function getNoVotes($conn) {
                        $sql = "SELECT COUNT(*) AS no_votes FROM `anser` WHERE `answer` = 'no' AND `opros_id` = '1'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        return $row['no_votes'];
                    }

                    function checkIfVoted($user_id, $conn) {
                        $sql = "SELECT COUNT(*) AS voted FROM `anser` WHERE user_id = '$user_id' AND opros_id = '1'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        return ($row['voted'] > 0);
                    }

                    mysqli_close($conn);
                    ?>
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
            const yesVotes = parseInt(document.getElementById('myChart').dataset.yesVotes);
            const noVotes = parseInt(document.getElementById('myChart').dataset.noVotes);

            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'pie', // Тип графика (круговая диаграмма)
                data: {
                    labels: ['Да', 'Нет'],
                    datasets: [{
                        data: [yesVotes, noVotes],
                        backgroundColor: [
                            'rgba(156, 228, 137, 1)', // Цвет для "Да"
                            'rgba(250, 89, 94, 1)' // Цвет для "Нет"
                        ],
                        borderColor: [
                            'rgba(156, 228, 137, 1)', 
                            'rgba(250, 89, 94, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    cutout: '60%',
                    responsive: false, // Отключите отзывчивость
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 40,
                            right: 40,
                            top: 0,
                            bottom: 50
                        }
                    }
                }
            });
        </script>
    </div>
</body>

