<?php
$database_config = array(
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'your_database'
);

$security_config = array(
'enable_rate_limit' => true,    // Enable/Disable protection against frequent requests / Включение/выключение защиты от частых запросов
'rate_limit' => 10,             // Number of requests / Количество запросов
'rate_time' => 60,              // Time interval in seconds / Временной интервал в секундах
'enable_logging' => true,       // Enable/Disable logging / Включение/выключение логирования
'enable_encryption' => true     // Enable/Disable message encryption / Включение/выключение шифрования сообщений
);

$example_link = 'https://kmi.devo4ka.top';