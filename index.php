<?php

require_once('config.php');

$mysqli = new mysqli($database_config['host'], $database_config['username'], $database_config['password'], $database_config['database'], $database_config['port']);

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$random_id = basename($_SERVER["REQUEST_URI"]);

$sql = "SELECT message FROM messages WHERE random_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $random_id);
    $stmt->execute();
    $stmt->bind_result($message);
    $stmt->fetch();
    $stmt->close();

    if ($message) {
        echo "\n" . $message;
    } else {
        echo "Сообщение не найдено\n";
    }
} else {
    echo "Ошибка подготовки запроса\n";
}

$mysqli->close();
?>
