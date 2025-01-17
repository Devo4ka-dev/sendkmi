<?php
$database_config = array(
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'your_database'
);

$security_config = array(
    'enable_rate_limit' => true,    // Включение/выключение защиты от частых запросов
    'rate_limit' => 10,             // Количество запросов
    'rate_time' => 60,              // Временной интервал в секундах
    'enable_logging' => true,       // Включение/выключение логирования
    'enable_encryption' => true     // Включение/выключение шифрования сообщений
);

$example_link = 'https://kmi.devo4ka.top';