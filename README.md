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
CREATE DATABASE IF NOT EXISTS message_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE message_system;

-- Table for storing messages
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    random_id VARCHAR(10) NOT NULL UNIQUE,
    source ENUM('web', 'cli') NOT NULL DEFAULT 'web',
    user_agent VARCHAR(255) DEFAULT NULL,
    encrypted BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for logging requests
CREATE TABLE message_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id VARCHAR(10) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    request_type ENUM('read', 'write') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES messages(random_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for request rate limiting
CREATE TABLE request_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL UNIQUE,
    request_count INT NOT NULL DEFAULT 0,
    last_request TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
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
CREATE DATABASE IF NOT EXISTS message_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE message_system;

-- Таблица для хранения сообщений
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    random_id VARCHAR(10) NOT NULL UNIQUE,
    source ENUM('web', 'cli') NOT NULL DEFAULT 'web',
    user_agent VARCHAR(255) DEFAULT NULL,
    encrypted BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица для логирования запросов
CREATE TABLE message_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id VARCHAR(10) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    request_type ENUM('read', 'write') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES messages(random_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица для ограничения частоты запросов
CREATE TABLE request_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL UNIQUE,
    request_count INT NOT NULL DEFAULT 0,
    last_request TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
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