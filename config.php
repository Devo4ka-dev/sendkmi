<?php
declare(strict_types=1);

/**
 * Файл конфигурации приложения.
 * Содержит настройки для базы данных, кэширования, безопасности и общие параметры приложения.
 */

class Config
{
    /**
     * Конфигурация базы данных MySQL.
     * Необходима для подключения к базе данных, где хранятся сообщения.
     */
    const DATABASE = [
        'host'     => 'localhost',    // Хост базы данных (обычно localhost)
        'port'     => 3306,           // Порт MySQL (обычно 3306)
        'username' => 'db_user',      // Имя пользователя базы данных
        'password' => 'db_password',  // Пароль пользователя базы данных
        'database' => 'kmi',          // Имя базы данных
    ];

    /**
     * Конфигурация кэширования.
     * Определяет параметры кэширования сообщений для ускорения доступа и снижения нагрузки на БД.
     */
    const CACHE = [
        'enable_cache' => true,         // Включить кэширование (true/false)
        'cache_driver' => 'redis',      // Драйвер кэша: 'redis' или 'file'
        'cache_time'   => 300,           // Время жизни кэша в секундах (например, 300 = 5 минут)
        'redis_host'   => '127.0.0.1',     // Хост Redis сервера
        'redis_port'   => 6379,          // Порт Redis сервера
        'redis_timeout' => 1,           // Тайм-аут подключения к Redis в секундах
    ];

    /**
     * Параметры безопасности приложения.
     * Включают настройки отладки, лимиты размеров, шифрования, защиты от DDoS и логирования.
     */
    const SECURITY = [
        'debug_mode'         => false,       // Режим отладки (true/false). В production должен быть false.
        'error_reporting'    => E_ALL,       // Уровень error_reporting для режима отладки (E_ALL для всех ошибок)
        'enable_encryption'  => false,       // Включить шифрование сообщений (true/false)
        'max_message_size'   => 51200,       // (512 КБ) Максимальный размер сообщения в байтах (например, 10240 = 10KB)
        'max_id_length'      => 10,          // Максимальная длина генерируемых ID сообщений
        'enable_rate_limit'  => true,        // Включить ограничение частоты запросов (true/false)
        'rate_limit'         => 20,          // Максимальное количество запросов с одного IP за период времени
        'rate_time'          => 60,          // Период времени для rate_limit в секундах (например, 60 = 1 минута)
        'enable_logging'     => false,        // Включить логирование запросов (true/false)
        'enable_error_logs'  => true,        // Параметр для управления регистрацией ошибок
        'enable_ping'        => true,        // Включить эндпоинт /ping (true/false)
        'enable_ip_check'    => true,        // Включить эндпоинт /ip (true/false)
        'ping_tests'         => 5,           // Количество тестов пинга при измерении
        'ping_timeout'       => 3,           // Тайм-аут для каждого теста пинга в секундах
    ];

    /**
     * Общие параметры приложения.
     * Содержат имя приложения, версию, URL репозитория, базовый URL и путь к favicon.
     */
    const APP = [
        // 'API_VERSION' => '1.0', // Версия API
        'name'       => 'KMI', // Название вашего приложения
        'version'    => 'v2025.03.4.19',
        'repository' => 'https://github.com/Devo4ka-dev/sendkmi', 
        'base_url'   => 'https://kmi.devo4ka.top', // Базовый URL вашего сайта (с https://)
        'favicon'    => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE4LjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiCgkgdmlld0JveD0iMCAwIDE2IDE2IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAxNiAxNiIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnIGlkPSJMYXllcl8xIiBkaXNwbGF5PSJub25lIj4KCTxnIGRpc3BsYXk9ImlubGluZSI+CgkJPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTEyLDE1Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTIsMTUgMTEsMTUgMTEsMTYgCQkiLz4KCQk8cmVjdCB4PSIxMCIgeT0iMTUiIGZpbGw9IiM1NDA5MEUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSIxNSIgZmlsbD0iIzU0MDkwRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxwb2x5bGluZSBmaWxsPSIjRkZGRkZGIiBwb2ludHM9IjksMTYgOSwxNSA4LDE1IAkJIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iOCwxNSA3LDE1IDcsMTYgCQkiLz4KCQk8cmVjdCB4PSI2IiB5PSIxNSIgZmlsbD0iIzU0MDkwRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjUiIHk9IjE1IiBmaWxsPSIjNTQwOTBFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNSwxNiA1LDE1IDQsMTUgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNNCwxNSIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xMywxNCIvPgoJCTxwb2x5bGluZSBmaWxsPSIjRkZGRkZGIiBwb2ludHM9IjEzLDE0IDEyLDE0IDEyLDE1IAkJIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjE0IiBmaWxsPSIjNTQwOTBFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjE0IiBmaWxsPSIjQjQxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iMTQiIGZpbGw9IiNCNDEzMUUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSIxNCIgZmlsbD0iIzM1MTcwNSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjE0IiBmaWxsPSIjMzUxNzA1IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iMTQiIGZpbGw9IiNCNDEzMUUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSIxNCIgZmlsbD0iI0I0MTMxRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjE0IiBmaWxsPSIjNTQwOTBFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjEzIiB5MT0iMTMiIHgyPSIxMyIgeTI9IjE0Ii8+CgkJPHJlY3QgeD0iMTIiIHk9IjEzIiBmaWxsPSIjNTQwOTBFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjEzIiBmaWxsPSIjQjQxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjEzIiBmaWxsPSIjQjQxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iMTMiIGZpbGw9IiNDNTE0MjEiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSIxMyIgZmlsbD0iI0REMTcyNSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjEzIiBmaWxsPSIjREQxNzI1IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iMTMiIGZpbGw9IiNERDE3MjUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSIxMyIgZmlsbD0iI0I0MTMxRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjEzIiBmaWxsPSIjQjQxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMyIgeT0iMTMiIGZpbGw9IiM5RTExMUEiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMTQsMTIiLz4KCQk8cG9seWxpbmUgZmlsbD0iI0ZGRkZGRiIgcG9pbnRzPSIxNCwxMiAxMywxMiAxMywxMyAJCSIvPgoJCTxyZWN0IHg9IjEyIiB5PSIxMiIgZmlsbD0iIzU0MDkwRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjExIiB5PSIxMiIgZmlsbD0iI0I0MTMxRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEwIiB5PSIxMiIgZmlsbD0iI0M1MTQyMSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjkiIHk9IjEyIiBmaWxsPSIjREQxNzI1IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iMTIiIGZpbGw9IiNGMDE5MjgiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI3IiB5PSIxMiIgZmlsbD0iI0YwMTkyOCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjYiIHk9IjEyIiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNSIgeT0iMTIiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI0IiB5PSIxMiIgZmlsbD0iI0REMTcyNSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjMiIHk9IjEyIiBmaWxsPSIjOUUxMTFBIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTIsMTIiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMTQiIHkxPSIxMSIgeDI9IjE0IiB5Mj0iMTIiLz4KCQk8cmVjdCB4PSIxMyIgeT0iMTEiIGZpbGw9IiM1NDA5MEUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMiIgeT0iMTEiIGZpbGw9IiNCNDEzMUUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMSIgeT0iMTEiIGZpbGw9IiNDNTE0MjEiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMCIgeT0iMTEiIGZpbGw9IiNERDE3MjUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSIxMSIgZmlsbD0iI0YwMTkyOCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjgiIHk9IjExIiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNyIgeT0iMTEiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSIxMSIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjUiIHk9IjExIiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNCIgeT0iMTEiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIzIiB5PSIxMSIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjIiIHk9IjExIiBmaWxsPSIjQjIxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjIiIHkxPSIxMiIgeDI9IjIiIHkyPSIxMSIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIxNCIgeTE9IjEwIiB4Mj0iMTQiIHkyPSIxMSIvPgoJCTxyZWN0IHg9IjEzIiB5PSIxMCIgZmlsbD0iIzU0MDkwRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEyIiB5PSIxMCIgZmlsbD0iI0I0MTMxRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjExIiB5PSIxMCIgZmlsbD0iI0REMTcyNSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEwIiB5PSIxMCIgZmlsbD0iI0YwMTkyOCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjkiIHk9IjEwIiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iMTAiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI3IiB5PSIxMCIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjYiIHk9IjEwIiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNSIgeT0iMTAiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI0IiB5PSIxMCIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjMiIHk9IjEwIiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMiIgeT0iMTAiIGZpbGw9IiNEMzE2MjMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMiIgeTE9IjExIiB4Mj0iMiIgeTI9IjEwIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjE0IiB5MT0iOSIgeDI9IjE0IiB5Mj0iMTAiLz4KCQk8cmVjdCB4PSIxMyIgeT0iOSIgZmlsbD0iIzU0MDkwRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEyIiB5PSI5IiBmaWxsPSIjQjQxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjkiIGZpbGw9IiNERDE3MjUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMCIgeT0iOSIgZmlsbD0iI0YwMTkyOCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjkiIHk9IjkiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSI5IiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNyIgeT0iOSIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjYiIHk9IjkiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSI5IiBmaWxsPSIjRkY5NjlEIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNCIgeT0iOSIgZmlsbD0iI0ZGNTM1RSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjMiIHk9IjkiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIyIiB5PSI5IiBmaWxsPSIjRDMxNjIzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjIiIHkxPSIxMCIgeDI9IjIiIHkyPSI5Ii8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjE0IiB5MT0iOCIgeDI9IjE0IiB5Mj0iOSIvPgoJCTxyZWN0IHg9IjEzIiB5PSI4IiBmaWxsPSIjN0QwRDE0IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTIiIHk9IjgiIGZpbGw9IiNCNDEzMUUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMSIgeT0iOCIgZmlsbD0iI0REMTcyNSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEwIiB5PSI4IiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iOCIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjgiIHk9IjgiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI3IiB5PSI4IiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iOCIgZmlsbD0iI0ZGNUU2OSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjUiIHk9IjgiIGZpbGw9IiNGRjk2OUQiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI0IiB5PSI4IiBmaWxsPSIjRkZBMEE2IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMyIgeT0iOCIgZmlsbD0iI0YwMTkyOCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjIiIHk9IjgiIGZpbGw9IiNEMzE2MjMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMiIgeTE9IjkiIHgyPSIyIiB5Mj0iOCIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xNCw4Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTMsNyAxMyw4IDE0LDggCQkiLz4KCQk8cmVjdCB4PSIxMiIgeT0iNyIgZmlsbD0iIzdEMEQxNCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjExIiB5PSI3IiBmaWxsPSIjQjQxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjciIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSI3IiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iNyIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjciIGZpbGw9IiNGRjVFNjkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSI3IiBmaWxsPSIjRkY5NjlEIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNSIgeT0iNyIgZmlsbD0iI0ZGQUFBRiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjciIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIzIiB5PSI3IiBmaWxsPSIjRDMxNjIzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMiw4IDMsOCAzLDcgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMiw4Ii8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjEzIiB5MT0iNiIgeDI9IjEzIiB5Mj0iNyIvPgoJCTxyZWN0IHg9IjEyIiB5PSI2IiBmaWxsPSIjN0QwRDE0IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjYiIGZpbGw9IiNCNDEzMUUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMCIgeT0iNiIgZmlsbD0iI0I0MTMxRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjkiIHk9IjYiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSI2IiBmaWxsPSIjQjQxMzFFIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNyIgeT0iNiIgZmlsbD0iI0ZGMUMyQiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjYiIHk9IjYiIGZpbGw9IiNGRjFDMkIiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSI2IiBmaWxsPSIjRkYxQzJCIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNCIgeT0iNiIgZmlsbD0iI0YwMTkyOCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjMiIHk9IjYiIGZpbGw9IiNEMzE2MjMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMyIgeTE9IjciIHgyPSIzIiB5Mj0iNiIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xMyw2Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTIsNSAxMiw2IDEzLDYgCQkiLz4KCQk8cmVjdCB4PSIxMSIgeT0iNSIgZmlsbD0iIzdEMEQxNCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEwIiB5PSI1IiBmaWxsPSIjN0QwRDE0IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iNSIgZmlsbD0iI0I0MTMxRSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjgiIHk9IjUiIGZpbGw9IiM1NDI0MDkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI3IiB5PSI1IiBmaWxsPSIjNTQyNDA5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iNSIgZmlsbD0iI0YwMTkyOCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjUiIHk9IjUiIGZpbGw9IiNEMzE2MjMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI0IiB5PSI1IiBmaWxsPSIjRDMxNjIzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMyw2IDQsNiA0LDUgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMyw2Ii8+CgkJPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTEyLDUiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMTEiIHkxPSI1IiB4Mj0iMTIiIHkyPSI1Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTAsNCAxMCw1IDExLDUgCQkiLz4KCQk8cmVjdCB4PSI5IiB5PSI0IiBmaWxsPSIjNDQxRTA3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iNCIgZmlsbD0iIzMwMTUwNCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjQiIGZpbGw9IiNCMjEzMUUiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSI0IiBmaWxsPSIjRDMxNjIzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNSw1IDYsNSA2LDQgCQkiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iNCIgeTE9IjUiIHgyPSI1IiB5Mj0iNSIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik00LDUiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMTAsNCIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xMCwzIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTAsMyA5LDMgOSw0IDEwLDQgCQkiLz4KCQk8cmVjdCB4PSI4IiB5PSIzIiBmaWxsPSIjNzczNDBEIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNyw0IDgsNCA4LDMgCQkiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iNiIgeTE9IjQiIHgyPSI3IiB5Mj0iNCIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik02LDQiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMTAiIHkxPSIyIiB4Mj0iMTAiIHkyPSIzIi8+CgkJPHJlY3QgeD0iOSIgeT0iMiIgZmlsbD0iIzQ0MUUwNyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjgiIHk9IjIiIGZpbGw9IiM3NzM0MEQiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iOCIgeTE9IjMiIHgyPSI4IiB5Mj0iMiIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIxMCIgeTE9IjEiIHgyPSIxMCIgeTI9IjIiLz4KCQk8cmVjdCB4PSI5IiB5PSIxIiBmaWxsPSIjNzczNDBEIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iOCwyIDksMiA5LDEgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNOCwyIi8+CgkJPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTEwLDEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iOSIgeTE9IjEiIHgyPSIxMCIgeTI9IjEiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNOSwxIi8+Cgk8L2c+CjwvZz4KPGcgaWQ9IkxheWVyXzIiPgoJPGc+CgkJPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTEyLDE1Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTIsMTUgMTEsMTUgMTEsMTYgCQkiLz4KCQk8cmVjdCB4PSIxMCIgeT0iMTUiIGZpbGw9IiMxOTE5MTkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSIxNSIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxwb2x5bGluZSBmaWxsPSIjRkZGRkZGIiBwb2ludHM9IjksMTYgOSwxNSA4LDE1IAkJIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iOCwxNSA3LDE1IDcsMTYgCQkiLz4KCQk8cmVjdCB4PSI2IiB5PSIxNSIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjUiIHk9IjE1IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNSwxNiA1LDE1IDQsMTUgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNNCwxNSIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xMywxNCIvPgoJCTxwb2x5bGluZSBmaWxsPSIjRkZGRkZGIiBwb2ludHM9IjEzLDE0IDEyLDE0IDEyLDE1IAkJIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjE0IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjE0IiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iMTQiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSIxNCIgZmlsbD0iIzM1MTcwNSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjE0IiBmaWxsPSIjMzUxNzA1IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iMTQiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSIxNCIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjE0IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNCwxNSA0LDE0IDMsMTQgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMywxNCIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIxMyIgeTE9IjEzIiB4Mj0iMTMiIHkyPSIxNCIvPgoJCTxyZWN0IHg9IjEyIiB5PSIxMyIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjExIiB5PSIxMyIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEwIiB5PSIxMyIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjkiIHk9IjEzIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iMTMiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI3IiB5PSIxMyIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjYiIHk9IjEzIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNSIgeT0iMTMiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI0IiB5PSIxMyIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjMiIHk9IjEzIiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjMiIHkxPSIxNCIgeDI9IjMiIHkyPSIxMyIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xNCwxMiIvPgoJCTxwb2x5bGluZSBmaWxsPSIjRkZGRkZGIiBwb2ludHM9IjE0LDEyIDEzLDEyIDEzLDEzIAkJIi8+CgkJPHJlY3QgeD0iMTIiIHk9IjEyIiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjEyIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjEyIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iMTIiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSIxMiIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjEyIiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iMTIiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSIxMiIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjEyIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMyIgeT0iMTIiIGZpbGw9IiMxOTE5MTkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cG9seWxpbmUgZmlsbD0iI0ZGRkZGRiIgcG9pbnRzPSIzLDEzIDMsMTIgMiwxMiAJCSIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0yLDEyIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjE0IiB5MT0iMTEiIHgyPSIxNCIgeTI9IjEyIi8+CgkJPHJlY3QgeD0iMTMiIHk9IjExIiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTIiIHk9IjExIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjExIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjExIiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iMTEiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSIxMSIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjExIiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iMTEiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSIxMSIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjExIiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMyIgeT0iMTEiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIyIiB5PSIxMSIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIyIiB5MT0iMTIiIHgyPSIyIiB5Mj0iMTEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMTQiIHkxPSIxMCIgeDI9IjE0IiB5Mj0iMTEiLz4KCQk8cmVjdCB4PSIxMyIgeT0iMTAiIGZpbGw9IiMxOTE5MTkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMiIgeT0iMTAiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMSIgeT0iMTAiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMCIgeT0iMTAiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSIxMCIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjgiIHk9IjEwIiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNyIgeT0iMTAiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSIxMCIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjUiIHk9IjEwIiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNCIgeT0iMTAiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIzIiB5PSIxMCIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjIiIHk9IjEwIiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjIiIHkxPSIxMSIgeDI9IjIiIHkyPSIxMCIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIxNCIgeTE9IjkiIHgyPSIxNCIgeTI9IjEwIi8+CgkJPHJlY3QgeD0iMTMiIHk9IjkiIGZpbGw9IiMxOTE5MTkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMiIgeT0iOSIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjExIiB5PSI5IiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjkiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSI5IiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iOSIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjkiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSI5IiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNSIgeT0iOSIgZmlsbD0iI0ZGRkZGRiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjkiIGZpbGw9IiNGRkZGRkYiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIzIiB5PSI5IiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMiIgeT0iOSIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIyIiB5MT0iMTAiIHgyPSIyIiB5Mj0iOSIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIxNCIgeTE9IjgiIHgyPSIxNCIgeTI9IjkiLz4KCQk8cmVjdCB4PSIxMyIgeT0iOCIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEyIiB5PSI4IiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTEiIHk9IjgiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIxMCIgeT0iOCIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjkiIHk9IjgiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI4IiB5PSI4IiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNyIgeT0iOCIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjYiIHk9IjgiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI1IiB5PSI4IiBmaWxsPSIjRkZGRkZGIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNCIgeT0iOCIgZmlsbD0iI0ZGRkZGRiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjMiIHk9IjgiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIyIiB5PSI4IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjIiIHkxPSI5IiB4Mj0iMiIgeTI9IjgiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMTYsNyIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xNCw4Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTMsNyAxMyw4IDE0LDggCQkiLz4KCQk8cmVjdCB4PSIxMiIgeT0iNyIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjExIiB5PSI3IiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjciIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSI3IiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iNyIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjciIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSI3IiBmaWxsPSIjRkZGRkZGIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNSIgeT0iNyIgZmlsbD0iI0ZGRkZGRiIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjciIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIzIiB5PSI3IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMiw4IDMsOCAzLDcgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMiw4Ii8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjE2IiB5MT0iNyIgeDI9IjE2IiB5Mj0iNiIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIxMyIgeTE9IjYiIHgyPSIxMyIgeTI9IjciLz4KCQk8cmVjdCB4PSIxMiIgeT0iNiIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjExIiB5PSI2IiBmaWxsPSIjREJBMjEzIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iMTAiIHk9IjYiIGZpbGw9IiNEQkEyMTMiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI5IiB5PSI2IiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iNiIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjYiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSI2IiBmaWxsPSIjRUFFRTU3IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNSIgeT0iNiIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjQiIHk9IjYiIGZpbGw9IiNFQUVFNTciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSIzIiB5PSI2IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPGxpbmUgZmlsbD0iI0ZGRkZGRiIgeDE9IjMiIHkxPSI3IiB4Mj0iMyIgeTI9IjYiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMTYsNiIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xMyw2Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTIsNSAxMiw2IDEzLDYgCQkiLz4KCQk8cmVjdCB4PSIxMSIgeT0iNSIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjEwIiB5PSI1IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOSIgeT0iNSIgZmlsbD0iI0RCQTIxMyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjgiIHk9IjUiIGZpbGw9IiM1NDI0MDkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI3IiB5PSI1IiBmaWxsPSIjNTQyNDA5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iNiIgeT0iNSIgZmlsbD0iI0VBRUU1NyIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjUiIHk9IjUiIGZpbGw9IiMxOTE5MTkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI0IiB5PSI1IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMyw2IDQsNiA0LDUgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMyw2Ii8+CgkJPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTEyLDUiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMTEiIHkxPSI1IiB4Mj0iMTIiIHkyPSI1Ii8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTAsNCAxMCw1IDExLDUgCQkiLz4KCQk8cmVjdCB4PSI5IiB5PSI0IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHJlY3QgeD0iOCIgeT0iNCIgZmlsbD0iIzMwMTUwNCIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjciIHk9IjQiIGZpbGw9IiMxOTE5MTkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8cmVjdCB4PSI2IiB5PSI0IiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNSw1IDYsNSA2LDQgCQkiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iNCIgeTE9IjUiIHgyPSI1IiB5Mj0iNSIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik00LDUiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMTAsNCIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xMCwzIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iMTAsMyA5LDMgOSw0IDEwLDQgCQkiLz4KCQk8cmVjdCB4PSI4IiB5PSIzIiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNyw0IDgsNCA4LDMgCQkiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iNiIgeTE9IjQiIHgyPSI3IiB5Mj0iNCIvPgoJCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik02LDQiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iMTAiIHkxPSIyIiB4Mj0iMTAiIHkyPSIzIi8+CgkJPHJlY3QgeD0iOSIgeT0iMiIgZmlsbD0iIzE5MTkxOSIgd2lkdGg9IjEiIGhlaWdodD0iMSIvPgoJCTxyZWN0IHg9IjgiIHk9IjIiIGZpbGw9IiMxOTE5MTkiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iOCIgeTE9IjMiIHgyPSI4IiB5Mj0iMiIvPgoJCTxsaW5lIGZpbGw9IiNGRkZGRkYiIHgxPSIxMCIgeTE9IjEiIHgyPSIxMCIgeTI9IjIiLz4KCQk8cmVjdCB4PSI5IiB5PSIxIiBmaWxsPSIjMTkxOTE5IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIi8+CgkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iOCwyIDksMiA5LDEgCQkiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNOCwyIi8+CgkJPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTEwLDEiLz4KCQk8bGluZSBmaWxsPSIjRkZGRkZGIiB4MT0iOSIgeTE9IjEiIHgyPSIxMCIgeTI9IjEiLz4KCQk8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNOSwxIi8+Cgk8L2c+CjwvZz4KPC9zdmc+Cg==',      // favicon (путь относительно корня сайта) или Base64
    ];
}