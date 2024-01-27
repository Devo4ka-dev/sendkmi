<?php

require_once('config.php');

header('Content-Type: text/html; charset=utf-8');
$mysqli = new mysqli($database_config['host'], $database_config['username'], $database_config['password'], $database_config['database'], $database_config['port']);

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$message = file_get_contents("php://input");

if (!empty($message)) {
    $random_id = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 7);

    $sql = "INSERT INTO messages (message, random_id) VALUES (?, ?)";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ss", $message, $random_id);
        $stmt->execute();
        $stmt->close();
        echo "Сообщение успешно записано\n$example_link/$random_id\n";
    } else {
        echo "Ошибка подготовки запроса\n";
    }
} else {
    echo "Пустые данные\n";
}

$mysqli->close();
?>
