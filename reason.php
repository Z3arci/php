<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/reason-style.css">
    <title>Опрос</title>
</head>

<style>
    .news {
        height: 50vh;
        background-color: #EBECF0;
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

    .user-reason{
        list-style: none;
            padding: 0px;
            max-height: 150px; 
            overflow-y: auto;
            background-color: #EBECF0;
    }

    .user-reason{
        margin-bottom: 10px;
    }
</style>

<body>
    <div class="container">
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <div class="active-cotainer">
                    <h2 class="active"> Коментарии </h2>
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

                    $sql = "SELECT `reason` FROM `anser` WHERE `opros_id` = '1'";
                    $result = mysqli_query($conn, $sql);

                    if ($result) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<ul class='user-reason'>"; 
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<li>" . $row['reason'] . "</li>"; 
                            }
                            echo "</ul>";
                        } else {
                            echo "Комментариев пока нет.";
                        }
                    } else {
                        echo "Ошибка получения комментариев.";
                    }

                    mysqli_close($conn);
                    ?>
                    <a class="reg" href="opros.php">Назад</a>
                </div>
            </div>
        </div>
        <script src="http://telegram.org/js/telegram-web-app.js"></script>
    </div>
</body>
</html>