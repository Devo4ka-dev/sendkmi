<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @font-face {
            font-family: monocraft;
            src: url(./style/Monocraft.ttf);
        }

        @font-face {
            font-family: SFP;
            src: url(./style/SFProText-Light.ttf);
        }

        * {
            color: #f8f8f2 !important; 
        }

        body {
            font-family: "monocraft";
            font-size: 14px;
            line-height: 1.6;
            background-color: #272822; 
            margin: 0;
            padding: 0px 20px 20px 20px;
            word-wrap: break-word;
        }

        .light-theme * {
            color: #272822 !important;
        }

        .light-theme body {
            background-color: #f8f8f2; 
        }

        .code-container {
            overflow-x: auto;
        }

        code {
            white-space: pre-wrap;
        }
    </style>
    <script>
        function applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.remove('light-theme');
            } else {
                document.documentElement.classList.add('light-theme');
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            applyTheme(mediaQuery.matches ? 'dark' : 'light');
            if (mediaQuery.addEventListener) {
                mediaQuery.addEventListener('change', function(e) {
                    applyTheme(e.matches ? 'dark' : 'light');
                });
            } else if (mediaQuery.addListener) {
                mediaQuery.addListener(function(e) {
                    applyTheme(e.matches ? 'dark' : 'light');
                });
            }
        });
    </script>
</head>
<body>

    <div class="code-container">
        <span style="color: #000000">
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
        echo "<p>Сообщение не найдено</p>";
    }
} else {
    echo "<p>Ошибка подготовки запроса</п>";
}

$mysqli->close();
?>
        </div>
</body>
</html>
