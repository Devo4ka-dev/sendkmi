<?php
require_once('config.php');

header('Content-Type: text/html; charset=utf-8');

function processMessage($message) {
    global $database_config, $example_link;

    $mysqli = new mysqli($database_config['host'], $database_config['username'], $database_config['password'], $database_config['database'], $database_config['port']);

    if ($mysqli->connect_error) {
        die("Ошибка подключения: " . $mysqli->connect_error);
    }

    if (!empty($message)) {
        $message_kb = strlen($message) / 1024;
        $message_characters = strlen($message);

        if ($message_kb <= 512 && $message_characters <= 1000000) {
            $random_id = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 4);
            $sql = "INSERT INTO messages (message, random_id) VALUES (?, ?)";

            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("ss", $message, $random_id);
                $stmt->execute();
                $stmt->close();
                $example_link = rtrim($example_link, '/') . "/$random_id"; // Формируем полную ссылку

                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo "Сообщение успешно записано. <span data-link='$example_link'>$example_link</span>";
                } else {
                    echo "Сообщение успешно записано. $example_link";
                }
            } else {
                echo "Ошибка подготовки запроса";
            }
        } else {
            echo "Сообщение слишком большое. Максимальный размер: 512 КБ и 1 миллион символов";
        }
    } else {
        echo "Пустые данные";
    }

    $mysqli->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? $_POST['message'] : file_get_contents("php://input");
    processMessage($message);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no"
    />
    <title>Создать сообщение</title>
    <style>
      @font-face {
        font-family: monocraft;
        src: url(./style/Monocraft.ttf);
      }

      @font-face {
        font-family: SFP;
        src: url(./style/SFProText-Light.ttf);
      }

      /* Общие стили для темной и светлой темы */
      body {
        font-family: "monocraft";
        background-color: #272822;
        color: #f8f8f2;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        transition: background-color 0.2s, color 0.2s;
      }

      .container {
        max-width: 600px;
        width: 100%;
        background-color: #444;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
        justify-content: center;
        align-items: center;
        transition: background-color 0.2s;
      }

      h1 {
        font-size: 28px;
        text-align: center;
        margin-bottom: 20px;
      }

      form {
        margin-bottom: 20px;
      }

      textarea {
        width: 100%;
        font-size: 16px;
        font-weight: 700;
        border-radius: 4px;
        resize: vertical;
        background-color: #3c3c3a;
        color: #272822;
        border: 2px solid #ffffff;
        max-height: 250px;
        min-height: 250px;
        transition: background-color 0.2s, color 0.2s, border-color 0.2s;
      }

      button {
        font-family: "SFP";
        padding: 10px 20px;
        margin: 20px 10px 10px 0px;
        font-size: 16px;
        background-color: #0f33ff;
        color: #fff;
        border: none;
        cursor: pointer;
        border-radius: 0px;
        transition: background-color 0.2s;
      }

      button:hover {
        background-color: #0f28b4;
      }

      #response {
        margin-top: 20px;
        padding: 10px;
        background-color: #3c3c3a;
        border-radius: 4px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-all;
        display: none;
        color: #f8f8f2; /* Цвет текста для темной темы */
      }

      @media screen and (max-width: 600px) {
        textarea {
          font-size: 14px;
          max-height: 150px;
          min-height: 150px;
        }

        button {
          font-size: 14px;
          padding: 8px 16px;
        }
      }

      #link {
        color: #0f33ff;
        cursor: pointer;
      }

      #message {
        color: #f8f8f2 !important; /* Цвет текста для темной темы */
      }

      /* Стили для светлой темы */
      .light-theme body {
        background-color: #f8f8f2;
        color: #272822;
      }

      .light-theme .container {
        background-color: #ddd;
        color: #333;
      }

      .light-theme textarea {
        background-color: #eee;
        color: #333;
        border-color: #666;
      }

      .light-theme #response {
        background-color: #eee;
        color: #333;
      }

      .light-theme #message {
        color: #272822 !important; /* Цвет текста для светлой темы */
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Создать сообщение</h1>
      <form id="messageForm" method="post" onsubmit="return false;">
        <textarea
          id="message"
          name="message"
          rows="5"
          placeholder="Введите ваше сообщение..."
          required
        ></textarea>
        <button type="submit">Отправить</button>
      </form>
      <div id="response"></div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const messageForm = document.getElementById("messageForm");
        const responseDiv = document.getElementById("response");

        function attachLinkClickListener(linkElement) {
          linkElement.addEventListener("click", function () {
            const linkToCopy = this.dataset.link;
            navigator.clipboard
              .writeText(linkToCopy)
              .then(() => {
                alert("Ссылка скопирована: " + linkToCopy);
              })
              .catch((err) => {
                console.error("Не удалось скопировать ссылку: ", err);
              });
          });
        }

        messageForm.addEventListener("submit", function (event) {
          event.preventDefault();

          const formData = new FormData(messageForm);
          fetch(document.URL, {
            method: "POST",
            body: formData,
          })
            .then((response) => response.text())
            .then((data) => {
              const linkElement = document.createElement("span");
              linkElement.dataset.link = data.replace(
                "Сообщение успешно записано. ",
                ""
              );
              linkElement.textContent = linkElement.dataset.link;
              linkElement.style.color = "#0f33ff";
              linkElement.style.cursor = "pointer";
              attachLinkClickListener(linkElement);
              responseDiv.appendChild(linkElement);
              responseDiv.style.display = "block";
              messageForm.reset();
            })
            .catch((error) => console.error("Ошибка:", error));
        });

        function applyTheme(theme) {
          if (theme === "dark") {
            document.documentElement.classList.remove("light-theme");
          } else {
            document.documentElement.classList.add("light-theme");
          }
        }

        const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
        applyTheme(mediaQuery.matches ? "dark" : "light");

        if (mediaQuery.addEventListener) {
          mediaQuery.addEventListener("change", function (e) {
            applyTheme(e.matches ? "dark" : "light");
          });
        } else if (mediaQuery.addListener) {
          mediaQuery.addListener(function (e) {
            applyTheme(e.matches ? "dark" : "light");
          });
        }
      });
    </script>
  </body>
</html>
