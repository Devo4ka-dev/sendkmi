# English

## KMI - Secure Message Handling Service

A robust PHP-based service for secure message sharing with features like encryption, rate limiting, syntax highlighting and Redis caching.

[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)

## Core Features

- **Secure Message Storage**: AES-256-CBC encryption for sensitive data
- **Rate Limiting**: Prevents abuse through IP-based request limiting
- **Caching**: Redis/File-based caching for improved performance  
- **Syntax Highlighting**: Code highlighting with Ace editor
- **Logging**: Detailed request and error logging
- **CLI Support**: Works with curl, wget, PowerShell etc.

## Requirements

- PHP 7.4+ with extensions:
  - openssl
  - mysqli 
  - redis (optional)
- Web server: Nginx (recommended) or Apache
- MySQL 5.7+ or MariaDB
- Redis server (optional, for caching)

## Installation

1. Clone repository:
```bash
git clone https://github.com/Devo4ka-dev/sendkmi.git
cd sendkmi
```

2. Configure Nginx:
```nginx
server {
    listen 80;
    server_name kmi.devo4ka.top;
    root /var/www/kmi/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        include fastcgi_params;
    }
}
```

3. Create and configure config.php:
```php
// Database configuration
const DATABASE = [
    'host' => 'localhost',
    'port'     => 3306,
    'username' => 'db_user',
    'password' => 'db_password', 
    'database' => 'kmi'
];

// Redis configuration (optional)
const CACHE = [
    'enable_cache' => true,
    'cache_driver' => 'redis',
    'redis_host' => '127.0.0.1',
    'redis_port' => 6379
];
```

4. Import database schema:
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
(1, '#include <stdio.h>\n\nint main() {\n    printf("Hello, World!\\n");\n    return 0;\n}\n', 'hello', '2025-01-17 10:24:54', 'web', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 0);
```

5. Set proper permissions:
```bash
chown -R www-data:www-data /var/www/kmi
chmod -R 755 /var/www/kmi
```

# Demo & Testing

## Usage Examples

## 1. Get your IP address

### Windows (7-11) PowerShell

To get your current IP address in PowerShell:

```powershell
Invoke-RestMethod -Uri https://kmi.devo4ka.top/ip
```

### Linux/macOS

To get your current IP address on Linux or macOS:

```bash
curl https://kmi.devo4ka.top/ip
```

Both commands will return your public IP address.

---

## 2. Measure ping from user to server

### Windows (7-11) PowerShell

To measure ping from user to server in PowerShell:

```powershell
Invoke-RestMethod -Uri https://kmi.devo4ka.top/ping
```

### Linux/macOS

To measure ping from user to server on Linux or macOS:

```bash
curl https://kmi.devo4ka.top/ping
```

Both commands will return the server's response time.

---

## 3. View example message

### Windows (7-11) PowerShell

To view an example message from the server in PowerShell:

```powershell
Invoke-RestMethod -Uri https://kmi.devo4ka.top/hello
```

### Linux/macOS

To view an example message from the server on Linux or macOS:

```bash
curl https://kmi.devo4ka.top/hello
```

Both commands will return an example message from the server.

---

## 4. Create a new message

### Windows (7-11) PowerShell

To create a new message in PowerShell:

```powershell
"Your message" | ForEach-Object { Invoke-RestMethod -Uri https://kmi.devo4ka.top -Method Post -Body $_ }
```

### Linux/macOS

To create a new message on Linux or macOS:

```bash
echo "Your message" | curl -X POST -d @- https://kmi.devo4ka.top
```

---

### Web Interface

- Main page:  
  [https://kmi.devo4ka.top](https://kmi.devo4ka.top)

- Measure ping:  
  [https://kmi.devo4ka.top/ping](https://kmi.devo4ka.top/ping)

- Find out your public IP:  
  [https://kmi.devo4ka.top/ip](https://kmi.devo4ka.top/ip)

- Read message:  
  [https://kmi.devo4ka.top/hello](https://kmi.devo4ka.top/hello)

- Read message (editor):  
  [https://kmi.devo4ka.top/hello?hl](https://kmi.devo4ka.top/hello?hl)

---

## Configuration Guide

### Security Options

```php
const SECURITY = [
    'enable_encryption' => true,     // Enable AES encryption
    'max_message_size' => 10240,     // Max 10KB messages
    'enable_rate_limit' => true,     // Enable rate limiting
    'rate_limit' => 20,             // Max 20 requests
    'rate_time' => 60,              // Per minute
    'enable_logging' => true        // Enable access logs
];
```

### Caching Configuration 

```php
const CACHE = [
    'enable_cache' => true,         // Enable caching
    'cache_driver' => 'redis',      // Redis or file
    'cache_time' => 300,           // 5 minute TTL
    'redis_host' => '127.0.0.1',   // Redis server
    'redis_port' => 6379          // Redis port
];
```

### Logging

Logs are stored in `/cache/logs/`:
- `access.log` - Request logs
- `php_errors.log` - Error logs 

## Performance Tuning

1. Enable Redis caching
2. Configure proper rate limiting
3. Enable Nginx caching
4. Optimize MySQL configuration
5. Enable PHP OPcache

## Security Considerations

1. Keep config.php secure
2. Use HTTPS only
3. Set proper file permissions
4. Enable rate limiting
5. Monitor access logs

## Troubleshooting

Common issues and solutions:

1. **Can't connect to Redis**
   - Check Redis service status
   - Verify connection settings
   
2. **Database errors**
   - Check MySQL credentials
   - Verify table permissions

3. **Permission errors**
   - Set proper ownership
   - Check directory permissions

## Contact & Support

For issues and support:
- GitHub: [project repository](https://github.com/Devo4ka-dev/sendkmi)
- Website: [devo4ka.top](https://devo4ka.top)

---

# Русский

## KMI - Сервис безопасной передачи сообщений

Надёжный PHP-сервис для безопасного обмена сообщениями с функциями шифрования, ограничения запросов, подсветки синтаксиса и Redis-кэширования.

## Основные возможности

- **Безопасное хранение**: Шифрование AES-256-CBC
- **Защита от перегрузки**: IP-based ограничение запросов
- **Кэширование**: Redis/файловое кэширование
- **Подсветка кода**: Редактор Ace
- **Логирование**: Детальные логи запросов
- **Поддержка CLI**: Работает с curl, wget, PowerShell

## Требования

- PHP 7.4+ с расширениями:
  - openssl
  - mysqli
  - redis (опционально)
- Веб-сервер: Nginx (рекомендуется) или Apache
- MySQL 5.7+ или MariaDB
- Redis сервер (опционально)

## Установка

1. Клонирование репозитория:
```bash
git clone https://github.com/Devo4ka-dev/sendkmi.git
cd sendkmi
```

2. Настройка Nginx:
```nginx
server {
    listen 80;
    server_name kmi.devo4ka.top;
    root /var/www/kmi/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        include fastcgi_params;
    }
}
```

3. Создание и настройка config.php:
```php
// Конфигурация базы данных
const DATABASE = [
    'host' => 'localhost',
    'username' => 'db_user',
    'port'     => 3306,
    'password' => 'db_password',
    'database' => 'kmi'
];

// Конфигурация Redis (опционально)
const CACHE = [
    'enable_cache' => true,
    'cache_driver' => 'redis',
    'redis_host' => '127.0.0.1',
    'redis_port' => 6379
];
```

4. Импорт схемы базы данных:
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
(1, '#include <stdio.h>\n\nint main() {\n    printf("Hello, World!\\n");\n    return 0;\n}\n', 'hello', '2025-01-17 10:24:54', 'web', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 0);
```

5. Установка прав доступа:
```bash
chown -R www-data:www-data /var/www/kmi
chmod -R 755 /var/www/kmi
```

## Демо и тестирование

### Демонстрационные страницы

Доступны следующие демо-страницы:

1. **Страница создания сообщений**
   - URL: [https://kmi.devo4ka.top/](https://kmi.devo4ka.top/)
   - Создание и обмен сообщениями через веб-интерфейс

2. **Пример просмотра сообщения**
   - Обычный вид: [https://kmi.devo4ka.top/hello](https://kmi.devo4ka.top/hello)
   - С подсветкой синтаксиса: [https://kmi.devo4ka.top/hello?hl](https://kmi.devo4ka.top/hello?hl)

3. **Служебные эндпоинты**
   - Узнать свой IP: [https://kmi.devo4ka.top/ip](https://kmi.devo4ka.top/ip)
   - Пинг от пользователя к серверу: [https://kmi.devo4ka.top/ping](https://kmi.devo4ka.top/ping)

## Использование примеры


## 1. Получить свой IP адрес

### Windows (7-11) PowerShell

Для получения вашего текущего IP адреса в PowerShell:

```powershell
Invoke-RestMethod -Uri https://kmi.devo4ka.top/ip
```

### Linux/macOS

Для получения вашего текущего IP адреса на Linux или macOS:

```bash
curl https://kmi.devo4ka.top/ip
```

Обе команды вернут ваш публичный IP адрес.

---

## 2. Измерить пинг от пользователя до сервера

### Windows (7-11) PowerShell

Для измерения пинга от пользователя до сервера в PowerShell:

```powershell
Invoke-RestMethod -Uri https://kmi.devo4ka.top/ping
```

### Linux/macOS

Для измерения пинга от пользователя до сервера на Linux или macOS:

```bash
curl https://kmi.devo4ka.top/ping
```

Обе команды вернут время отклика сервера.

---

## 3. Посмотреть пример сообщения

### Windows (7-11) PowerShell

Для просмотра примера сообщения от сервера в PowerShell:

```powershell
Invoke-RestMethod -Uri https://kmi.devo4ka.top/hello
```

### Linux/macOS

Для просмотра примера сообщения от сервера на Linux или macOS:

```bash
curl https://kmi.devo4ka.top/hello
```

Обе команды вернут пример сообщения от сервера.

---

## 4. Создать новое сообщение

### Windows (7-11) PowerShell

Для создания нового сообщения в PowerShell:

```powershell
"Ваше сообщение" | ForEach-Object { Invoke-RestMethod -Uri https://kmi.devo4ka.top -Method Post -Body $_ }
```

### Linux/macOS

Для создания нового сообщения на Linux или macOS:

```bash
echo "Ваше сообщение" | curl -X POST -d @- https://kmi.devo4ka.top
```

---

### Веб-интерфейс

- Главная страница:
[https://kmi.devo4ka.top](https://kmi.devo4ka.top)

- Измерить пинг
[https://kmi.devo4ka.top/ping](https://kmi.devo4ka.top/ping)

- Узнать свой публичный IP
[https://kmi.devo4ka.top/ip](https://kmi.devo4ka.top/ip)

- Прочитать сообщение:
[https://kmi.devo4ka.top/hello](https://kmi.devo4ka.top/hello)

- Прочитать сообщение (редактор):
[https://kmi.devo4ka.top/hello?hl](https://kmi.devo4ka.top/hello?hl)



## Руководство по настройке

### Параметры безопасности

```php
const SECURITY = [
    'enable_encryption' => true,     // Включить шифрование
    'max_message_size' => 10240,     // Макс. 10KB
    'enable_rate_limit' => true,     // Лимит запросов
    'rate_limit' => 20,             // Макс. 20 запросов
    'rate_time' => 60,              // В минуту
    'enable_logging' => true        // Включить логи
];
```

### Настройка кэширования

```php
const CACHE = [
    'enable_cache' => true,         // Включить кэш
    'cache_driver' => 'redis',      // Redis или файлы
    'cache_time' => 300,           // TTL 5 минут
    'redis_host' => '127.0.0.1',   // Redis сервер
    'redis_port' => 6379          // Redis порт
];
```

### Логирование

Логи хранятся в `/cache/logs/`:
- `access.log` - Логи запросов
- `php_errors.log` - Логи ошибок

## Оптимизация производительности

1. Включение Redis-кэширования
2. Настройка лимитов запросов
3. Настройка кэширования Nginx
4. Оптимизация MySQL
5. Включение PHP OPcache

## Безопасность
1. Защита config.php
2. Использование HTTPS
3. Правильные права доступа
4. Включение rate limiting
5. Мониторинг логов

## Устранение неполадок

Частые проблемы и решения:

1. **Нет подключения к Redis**
   - Проверьте статус службы
   - Проверьте настройки подключения
   
2. **Ошибки базы данных**
   - Проверьте учетные данные
   - Проверьте права на таблицы

3. **Ошибки прав доступа**
   - Установите правильного владельца
   - Проверьте права на директории

## Контакты и поддержка

По вопросам и поддержке:
- GitHub: [репозиторий проекта](https://github.com/Devo4ka-dev/sendkmi)
- Сайт: [devo4ka.top](https://devo4ka.top)