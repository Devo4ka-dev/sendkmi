# English

## Project Description

This project is a robust message-handling service that allows users to send and retrieve messages securely via HTTP requests. Each message is stored in a database and associated with a unique URL for easy access. Additional features include encryption, request rate limiting, and logging.

[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)

---

## Setup

Follow these steps to set up the project:

1. Clone this repository to your local machine or server.
2. Install necessary dependencies using Composer if applicable.
3. Configure the `config.php` file with your database and security parameters.
4. Import the provided database schema (see "Database Setup" below).
5. Start your PHP-enabled web server and test the setup.

---

## Database Setup

Run the following SQL script to create the required database schema:

```sql
-- Table `messages`
CREATE TABLE `messages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message` MEDIUMTEXT,
  `random_id` VARCHAR(7) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `source` VARCHAR(50) DEFAULT 'unknown',
  `user_agent` TEXT,
  `encrypted` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table `message_logs`
CREATE TABLE `message_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message_id` VARCHAR(7) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT,
  `request_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `request_type` ENUM('read', 'create') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  CONSTRAINT `message_logs_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`random_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table `request_limits`
CREATE TABLE `request_limits` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL UNIQUE,
  `request_count` INT DEFAULT 1,
  `last_request` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Sample data for `messages`
INSERT INTO `messages` (`id`, `message`, `random_id`, `created_at`, `source`, `user_agent`, `encrypted`) VALUES
(14, '#include <stdio.h>\n\nint main() {\n    printf(\"Hello, World!\\n\");\n    return 0;\n}\n', 'hello', '2025-01-17 10:24:54', 'web', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 0);
```

---

## Requirements

1. PHP 7.4 or higher with extensions: `openssl`, `mysqli`.
2. A web server such as Apache or Nginx.
3. MySQL 5.7 or higher (or equivalent).

---

## Features

- **Secure Message Handling:** Supports optional AES-256 encryption to protect sensitive messages.
- **Rate Limiting:** Prevents abuse by limiting the frequency of requests from a single IP.
- **Logging:** Logs every request for debugging and monitoring.
- **Syntax Highlighting:** Add `?hl` to a message URL for syntax highlighting in a web-based editor.

---

## Usage Examples

### Sending a Message
To send a message, use the following `curl` command:

```bash
echo "message" | curl -X POST -d @- https://kmi.devo4ka.top
```

### Retrieving a Message
When a message is successfully sent, a unique URL is returned. Visit this URL to view the message. For syntax highlighting, append `?hl`:

```
https://kmi.devo4ka.top/hello?hl
```

---

## Testing

For quick testing, use the online web form available at:

[https://kmi.devo4ka.top](https://kmi.devo4ka.top)

---

## Contact

For more information or support, visit:

[https://devo4ka.top/](https://devo4ka.top/)

---
# Русский
## Описание проекта
Этот проект представляет собой надежный сервис обработки сообщений, который позволяет пользователям безопасно отправлять и получать сообщения через HTTP-запросы. Каждое сообщение сохраняется в базе данных и связано с уникальным URL для удобного доступа. Дополнительные функции включают шифрование, ограничение частоты запросов и ведение логов.

[![Лицензия: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)

---

## Установка

Следуйте этим шагам для настройки проекта:

1. Клонируйте этот репозиторий на вашу локальную машину или сервер.
2. Установите необходимые зависимости с помощью Composer, если это применимо.
3. Настройте файл `config.php` с параметрами вашей базы данных и безопасности.
4. Импортируйте предоставленную схему базы данных (см. раздел "Настройка базы данных" ниже).
5. Запустите веб-сервер с поддержкой PHP и проверьте настройку.

---

## Настройка базы данных

Выполните следующий SQL-скрипт для создания требуемой схемы базы данных:

```sql
-- Table `messages`
CREATE TABLE `messages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message` MEDIUMTEXT,
  `random_id` VARCHAR(7) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `source` VARCHAR(50) DEFAULT 'unknown',
  `user_agent` TEXT,
  `encrypted` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table `message_logs`
CREATE TABLE `message_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message_id` VARCHAR(7) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT,
  `request_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `request_type` ENUM('read', 'create') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  CONSTRAINT `message_logs_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`random_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table `request_limits`
CREATE TABLE `request_limits` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL UNIQUE,
  `request_count` INT DEFAULT 1,
  `last_request` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Sample data for `messages`
INSERT INTO `messages` (`id`, `message`, `random_id`, `created_at`, `source`, `user_agent`, `encrypted`) VALUES
(14, '#include <stdio.h>\n\nint main() {\n    printf(\"Hello, World!\\n\");\n    return 0;\n}\n', 'hello', '2025-01-17 10:24:54', 'web', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 0);
```

---

## Требования

1. PHP 7.4 или выше с расширениями: `openssl`, `mysqli`.
2. Веб-сервер, такой как Apache или Nginx.
3. MySQL 5.7 или выше (или аналогичный).

---

## Особенности

- **Безопасная обработка сообщений:** Поддержка опционального шифрования AES-256 для защиты чувствительных сообщений.
- **Ограничение частоты запросов:** Предотвращает злоупотребления, ограничивая частоту запросов с одного IP.
- **Логирование:** Логирует каждый запрос для отладки и мониторинга.
- **Подсветка синтаксиса:** Добавьте `?hl` к URL сообщения для подсветки синтаксиса в веб-редакторе.

---

## Примеры использования

### Отправка сообщения
Для отправки сообщения используйте следующую команду `curl`:

```bash
echo "your message" | curl -X POST -d @- https://kmi.devo4ka.top
```

### Получение сообщения
Когда сообщение успешно отправлено, возвращается уникальный URL. Перейдите по этому URL, чтобы просмотреть сообщение. Для подсветки синтаксиса добавьте `?hl`:

```
https://kmi.devo4ka.top/hello?hl
```

---

## Тестирование

Для быстрого тестирования используйте онлайн-форму, доступную по адресу:

[https://kmi.devo4ka.top](https://kmi.devo4ka.top)

---

## Контакты

Для получения дополнительной информации или поддержки посетите:

[https://devo4ka.top/](https://devo4ka.top/)

---