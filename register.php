<?php
require_once('db.php'); 
require_once('phpqrcode/qrlib.php'); // Assuming you have this library installed

$login = $_POST['login'];
$surname = $_POST['surname'];
$groups = $_POST['group'];
$pass = $_POST['password'];

if (empty($login) || empty($surname) || empty($groups) || empty($pass)) {
    echo "Заполните все поля";
    header('Location: /index1.html'); 
    exit;
} else {
    // Проверяем, существует ли группа в базе данных
    $stmt = $conn->prepare("SELECT 1 FROM `groups` WHERE `groups` = ?");
    $stmt->bind_param("s", $groups);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { 
        // Получаем ID роли "user" из таблицы roles
        $role_sql = "SELECT id FROM roles WHERE name = 'user'";
        $role_result = mysqli_query($conn, $role_sql);
        $role_row = mysqli_fetch_assoc($role_result);
        $role_id = $role_row['id']; 

        // Генерируем QR-код
        $qr_code = generateQRCode($login);

        // Вставляем нового пользователя с ролью "user" и QR-кодом
        $sql = "INSERT INTO `users` (`login`, `surname`, `groups`, `pass`, `qr_code`, `role_id`) 
                VALUES ('$login', '$surname', '$groups', '$pass', '$qr_code', '$role_id')";
        if ($conn->query($sql) === TRUE) {
            header('Location: /index5.html'); 
            exit;
        } else {
            echo "Ошибка при регистрации: " . $conn->error;
            header('Location: /index1.html'); 
            exit;
        }
    } else {
        echo "Ошибка: Группа не найдена!";
        header('Location: /index1.html'); 
        exit;
    }
}

function generateQRCode($login) {
    require_once('phpqrcode/qrlib.php'); 

    $qr_code_data = "login=" . $login; 
    $tempDir = "qrcode/"; 
    $codeFile = $tempDir . "qrcode_" . $login . ".png"; 

    QRcode::png($qr_code_data, $codeFile, QR_ECLEVEL_L, 5, 2, false, false, 2);

    return base64_encode(file_get_contents($codeFile));
}

mysqli_close($conn);
?>