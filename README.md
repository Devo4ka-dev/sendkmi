Here's the project description in both English and Russian versions:

### English Version

# Project Description

This project is a simple service for sending and reading messages via HTTP requests. Users can send messages to the server, which saves them in a database and returns a unique URL for accessing the message.

[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)

## Setup

To get started with the project, follow these steps:

1. Clone the repository to your local machine or server.
2. Install any necessary dependencies if there are any.
3. Configure the `config.php` file with your database parameters.
4. Start the server and ensure everything is working correctly.

## Database Setup

To create the necessary database structure, execute the following SQL query:

```sql
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    random_id VARCHAR(7) NOT NULL UNIQUE
);
```

## Requirements

1. PHP 7.4 or higher.
2. Web server like Nginx/Apache or equivalent.

## Usage Example

To send and record a message, execute the following command in your console:

```
echo "your message" | curl -X POST -d @- https://kmi.devo4ka.top/kmi
```

To read a message, follow the link provided after sending.

## Testing

Our bot for testing this script in Telegram:
[https://t.me/yugwfiuomdcpsobot](https://t.me/yugwfiuomdcpsobot)

## Contact

[https://devo4ka.top/](https://devo4ka.top/)


### Russian Version

# Описание проекта

Этот проект представляет собой простой сервис для отправки и чтения сообщений через HTTP запросы. Пользователи могут отправлять сообщения на сервер, который сохраняет их в базе данных и возвращает уникальный URL для доступа к сообщению.

[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)

## Настройка

Для начала работы с проектом выполните следующие шаги:

1. Клонируйте репозиторий на свой локальный компьютер или сервер.
2. Установите необходимые зависимости, если таковые имеются.
3. Настройте файл `config.php` с параметрами вашей базы данных.
4. Запустите сервер и убедитесь, что все работает корректно.

## Настройка базы данных

Для создания необходимой структуры базы данных, выполните следующий SQL запрос:

```sql
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    random_id VARCHAR(7) NOT NULL UNIQUE
);
```

## Требования

1. PHP 7.4 или выше.
2. Веб сервер Nginx/Apache или другой.

## Пример использования

Чтобы отправить и записать сообщение, выполните следующую команду в консоли:
```
echo "ваше сообщение" | curl -X POST -d @- https://kmi.devo4ka.top/kmi
```

Чтобы прочитать сообщение, перейдите по ссылке, которая будет выдана после отправки.

## Тест

Наш бот для теста этого скрипта в телеграм:
[https://t.me/yugwfiuomdcpsobot](https://t.me/yugwfiuomdcpsobot)

## Связь

[https://devo4ka.top/](https://devo4ka.top/)

