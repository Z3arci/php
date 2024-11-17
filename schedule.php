<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/style2.css">
    <title>Расписание</title>
</head>

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
        }

        .icon-imge{
            height: 45px;
            width: 45px;
        }
</style>

<body>
    <div class="container">
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <div class="active-cotainer">
                    <h2 class="active"> Расписание</h2>
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

                if ($is_admin) {
                    ?>
                    <h2>Загрузка расписания</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" name="schedule_file" accept=".pdf">
                        <select name="day" required>
                            <option value="">Выберите день недели</option>
                            <option value="1">Понедельник</option>
                            <option value="2">Вторник</option>
                            <option value="3">Среда</option>
                            <option value="4">Четверг</option>
                            <option value="5">Пятница</option>
                            <option value="6">Суббота</option>
                            <option value="7">Воскресенье</option>
                        </select>
                        <button type="submit" name="upload_schedule">Загрузить</button>
                    </form>
                    <?php

                    if (isset($_POST['upload_schedule'])) {
                        $target_dir = "schedule/";
                        $target_file = $target_dir . basename($_FILES["schedule_file"]["name"]);
                        $uploadOk = 1;
                        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                        if (isset($_POST["upload_schedule"])) {
                            $fileType = mime_content_type($_FILES["schedule_file"]["tmp_name"]);
                            if ($fileType === 'application/pdf') {
                                $uploadOk = 1;
                            } else {
                                echo "Извините, разрешены только PDF-файлы.";
                                $uploadOk = 0;
                            }
                        }

                        if (file_exists($target_file)) {
                            echo "Извините, файл с таким именем уже существует.";
                            $uploadOk = 0;
                        }

                        if ($_FILES["schedule_file"]["size"] > 500000) { 
                            echo "Извините, ваш файл слишком большой.";
                            $uploadOk = 0;
                        }

                        if($imageFileType != "pdf") {
                            echo "Извините, разрешены только PDF-файлы.";
                            $uploadOk = 0;
                        }

                        if ($uploadOk == 1) {
                            if (move_uploaded_file($_FILES["schedule_file"]["tmp_name"], $target_file)) {
                                echo "Файл " . basename($_FILES["schedule_file"]["name"]) . " загружен успешно.<br>";

                                $dayOfWeek = $_POST['day'];

                                $dayDir = "schedule/Pdf/day{$dayOfWeek}";
                                if (!is_dir($dayDir)) {
                                    mkdir($dayDir, 0777, true); 
                                }

                                $newTargetFile = "{$dayDir}/" . basename($_FILES["schedule_file"]["name"]);
                                rename($target_file, $newTargetFile);

                                $packDir = "schedule/Packegeofpdf/day{$dayOfWeek}";
                                if (!is_dir($packDir)) {
                                    mkdir($packDir, 0777, true); 
                                }

                                $pageCount = shell_exec("pdfinfo {$newTargetFile} | grep Pages | awk '{print $2}'");

                                $packDir = "schedule/Packegeofpdf/day{$dayOfWeek}";
                                if (!is_dir($packDir)) {
                                    mkdir($packDir, 0777, true);
                                }

                                for ($i = 0; $i <= $pageCount - 1; $i++) {
                                    $pageDir = "{$packDir}/page{$i}";
                                    if (!is_dir($pageDir)) {
                                        mkdir($pageDir);
                                    }

                                    $fileName = $pageDir . '/image_' . $i . '.png';
                                    shell_exec("convert -density 300 {$newTargetFile}[{$i}] {$fileName}");

                                    $sql = "INSERT INTO `schedule` (`file_name`, `day`, `page`) VALUES ('$fileName', '$dayOfWeek', '$i')";
                                    if (mysqli_query($conn, $sql)) {
                                        echo "Информация о расписании для страницы $i сохранена в базе данных.";

                                        $sql = "SELECT `file_name` FROM `schedule` WHERE `day` = '$dayOfWeek' AND `page` = '$i'";
                                        $result = mysqli_query($conn, $sql);
                                        if ($result && mysqli_num_rows($result) > 0) {
                                            $row = mysqli_fetch_assoc($result);
                                            $image_path = $row['file_name'];

                                            splitImageIntoSquares($image_path, $dayOfWeek, $i, $conn);
                                        }
                                    } else {
                                        echo "Ошибка сохранения информации о расписании для страницы $i: " . mysqli_error($conn);
                                    }
                                }
                            } else {
                                echo "Произошла ошибка при загрузке файла.";
                            }
                        }
                    }

                } else {
                    $sql = "SELECT `groups` FROM `users` WHERE `id` = '$user_id'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $user_group = $row['groups'];

                    $dayOfWeek = date('N');

                    $sql = "SELECT `file_name` FROM `schedule` WHERE `group` = '$user_group' AND `day` = '$dayOfWeek'";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $schedule_file = $row['file_name'];

                        ?>
                        <iframe src="<?php echo $schedule_file; ?>" width="100%" height="600px"></iframe>
                        <?php

                    } else {
                        echo "Расписание для вашей группы на сегодня пока недоступно.";
                    }
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

                function splitImageIntoSquares($image_path, $dayOfWeek, $page, $conn) {
                    $image = imagecreatefrompng($image_path);
                    $width = imagesx($image);
                    $height = imagesy($image);

                    $squareWidth = 560;
                    $squareHeight = 690;
                    
                    $startX = 145;
                    $startY = 245;
                    
                    $squaresDir = "schedule/Squares/day{$dayOfWeek}/page{$page}";
                    if (!is_dir($squaresDir)) {
                        mkdir($squaresDir, 0777, true);
                    }
                    
                    $squaresX = ceil($width / $squareWidth);
                    $squaresY = ceil($height / $squareHeight);
                    
                    for ($y = 0; $y < $squaresY; $y++) {
                        for ($x = 0; $x < $squaresX; $x++) {
                            $startXCoord = $startX + $x * $squareWidth;
                            $startYCoord = $startY + $y * $squareHeight;
                            $endX = min($startXCoord + $squareWidth, $width);
                            $endY = min($startYCoord + $squareHeight, $height);
                    
                            $square = imagecreatetruecolor($squareWidth, $squareHeight);
                            imagealphablending($square, false);
                            imagesavealpha($square, true);
                    
                            imagecopy($square, $image, 0, 0, $startXCoord, $startYCoord, $endX - $startXCoord, $endY - $startYCoord);
                    
                            $squareFileName = "{$squaresDir}/square_{$x}_{$y}.png";
                            imagepng($square, $squareFileName);
                    
                            $sql = "INSERT INTO `squares` (`file_name`, `day`, `page`, `x`, `y`) VALUES ('$squareFileName', '$dayOfWeek', '$page', '$x', '$y')";
                            if (mysqli_query($conn, $sql)) {
                                echo "Квадрат $x $y для страницы $page сохранен.";
                            } else {
                                echo "Ошибка сохранения квадрата: " . mysqli_error($conn);
                            }
                    
                            imagedestroy($square);
                        }
                    }
                    
                    imagedestroy($image);
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
    </div>
</body>

</html>