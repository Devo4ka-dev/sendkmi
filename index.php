<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css">
</head>
<body>

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
        echo '<pre><code class="language-php">' . highlight_string($message, true) . '</code></pre>';
    } else {
        echo "Сообщение не найдено\n";
    }
} else {
    echo "Ошибка подготовки запроса\n";
}

$mysqli->close();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/plugins/autoloader/prism-autoloader.min.js"></script>
</body>
</html>
