<?php
declare(strict_types=1);

/**
 * Константы приложения.
 * Определяют основные параметры работы системы, пути к кэшу и логам.
 */
define('CACHE_DIR', __DIR__ . '/cache');
define('CACHE_MESSAGES_SUBDIR', 'messages');
define('LOG_DIR', 'logs');
define('RATE_LIMIT_FILE', LOG_DIR . '/rate_limits.json');

/**
 * Вспомогательная функция для создания директорий, если они не существуют.
 * Используется для директорий кэша и логов, гарантируя их доступность для записи.
 *
 * Использует статическую переменную для однократного создания директорий.
 *
 * @param string $subdir Имя поддиректории внутри основной директории кэша.
 */
function ensureDirectoryExists(string $subdir): void
{
    static $directoriesCreated = []; 

    $dir = CACHE_DIR . '/' . trim($subdir, '/');
    if (isset($directoriesCreated[$dir])) {
        error_log('Directory already checked and (possibly) created earlier: ' . $dir);
        return; 
    }

    $directoriesCreated[$dir] = true; 

    if (!is_dir($dir)) {
        error_log('Directory does not exist, attempting to create: ' . $dir);
        $oldUmask = umask(0);
        if (!mkdir($dir, 0755, true)) {
            $error = error_get_last();
            error_log('Failed to create directory ' . $dir . '. Error: ' . ($error ? $error['message'] : 'Unknown error'));
        } else {
            error_log('Directory successfully created: ' . $dir);
        }
        umask($oldUmask);
    } else {
        error_log('Directory already exists: ' . $dir);
    }
}

/**
 * Функция для получения экземпляра Redis клиента.
 */
function getRedisClient(): ?Redis
{
    if (!class_exists('Redis')) {
        error_log('Redis class not found. Using file cache.');
        return null;
    }

    $redis = new Redis();
    try {
        $redis->connect(Config::CACHE['redis_host'], Config::CACHE['redis_port'], Config::CACHE['redis_timeout']);
        error_log('Connected to Redis.');
        return $redis;
    } catch (RedisException $e) {
        error_log('Failed to connect to Redis: ' . $e->getMessage());
        return null;
    }
}

/**
 * Получение сообщения из кэша или базы данных.
 */
function getMessageWithCache(mysqli $mysqli, string $messageId): ?array
{
    if (!Config::CACHE['enable_cache']) {
        return getMessageFromDatabase($mysqli, $messageId);
    }

    $cacheKey = 'message:' . $messageId;
    $cacheTime = Config::CACHE['cache_time'];

    if (Config::CACHE['cache_driver'] === 'redis') {
        $redis = getRedisClient();
        if ($redis) {
            $cachedData = $redis->get($cacheKey);
            if ($cachedData) {
                error_log('Message retrieved from Redis cache.');
                return unserialize($cachedData);
            }
        }
        error_log('Redis unavailable or cache not found, switching to file cache.');
    }

    if (Config::CACHE['cache_driver'] !== 'redis' || !isset($redis) || !$redis) {
        // ensureDirectoryExists(CACHE_MESSAGES_SUBDIR); // Теперь директория создается в начале скрипта
        $cacheFile = CACHE_DIR . '/' . CACHE_MESSAGES_SUBDIR . '/' . hash('sha256', $messageId) . '.cache';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            error_log('Message retrieved from file cache.');
            return unserialize(file_get_contents($cacheFile));
        }
    }


    $messageData = getMessageFromDatabase($mysqli, $messageId);
    if ($messageData) {
        $serializedData = serialize($messageData);
        if (Config::CACHE['cache_driver'] === 'redis' && isset($redis) && $redis) {
            $redis->setex($cacheKey, $cacheTime, $serializedData);
            error_log('Message saved to Redis cache.');
        } else {
            // ensureDirectoryExists(CACHE_MESSAGES_SUBDIR); 
            $cacheFile = CACHE_DIR . '/' . CACHE_MESSAGES_SUBDIR . '/' . hash('sha256', $messageId) . '.cache';
            file_put_contents($cacheFile, $serializedData);
            error_log('Message saved to file cache.');
        }
    }
    return $messageData;
}

/**
 * Получение сообщения непосредственно из базы данных.
 */
function getMessageFromDatabase(mysqli $mysqli, string $messageId): ?array
{
    $stmt = $mysqli->prepare('SELECT message, encrypted FROM messages WHERE BINARY random_id = ? LIMIT 1');
    if (!$stmt) {
        error_log('Ошибка подготовки запроса к базе данных: ' . $mysqli->error);
        return null;
    }
    $stmt->bind_param('s', $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $messageData = $result->fetch_assoc();
    $stmt->close();
    return $messageData;
}

/**
 * Конфигурация приложения.
 */
require_once 'config.php';

/**
 * Класс обработки ошибок и исключений.
 * Инкапсулирует логику обработки критических ситуаций,
 * обеспечивая единообразный и информативный вывод ошибок.
 */
final class ErrorHandler
{
    private const ERROR_TYPES = [
        E_ERROR => 'Fatal Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Strict Standards',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'User Deprecated',
    ];

    public static function initialize(): void
    {
        if (Config::SECURITY['debug_mode']) {
            self::enableDebugMode(Config::SECURITY['error_reporting']);
        } else {
            self::enableProductionMode();
        }
    }

    private static function enableProductionMode(): void
    {
        error_reporting(0);
        ini_set('display_errors', '0');
        
        if (Config::SECURITY['enable_error_logs']) {
            ini_set('log_errors', '1');
            ini_set('error_log', CACHE_DIR . '/' . LOG_DIR . '/php_errors.log');
            ensureDirectoryExists(LOG_DIR);
        } else {
            ini_set('log_errors', '0');
        }
        
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleFatalError']);
    }


    private static function enableDebugMode(int $errorReportingLevel): void
    {
        error_reporting($errorReportingLevel);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');

        register_shutdown_function([self::class, 'handleFatalError']);
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleFatalError(): void
    {
        $error = error_get_last();
        if ($error !== null && self::isFatalErrorType($error['type'])) {
            ob_clean();
            self::logError($error['type'], $error['message'], $error['file'], $error['line']);
            if (Config::SECURITY['debug_mode']) {
                self::displayErrorPage($error['type'], $error['message'], $error['file'], $error['line']);
            } else {
                self::displayGenericErrorPage();
            }
        }
    }

    private static function isFatalErrorType(int $errorType): bool
    {
        return in_array($errorType, [E_ERROR, E_PARSE, E_core_ERROR, E_COMPILE_ERROR], true);
    }

    public static function handleException(Throwable $e): void
    {
        self::logError(E_ERROR, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
        if (Config::SECURITY['debug_mode']) {
            self::displayErrorPage($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
        } else {
            self::displayGenericErrorPage();
        }
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        self::logError($errno, $errstr, $errfile, $errline, debug_backtrace());
        if (Config::SECURITY['debug_mode']) {
            self::displayErrorPage($errno, $errstr, $errfile, $errline, debug_backtrace());
        } else {
            self::displayGenericErrorPage();
        }
        return true;
    }

    private static function logError(int $type, string $message, string $file, int $line, ?array $trace = null): void
    {
        if (!Config::SECURITY['enable_error_logs']) {
            return;
        }
        
        $errorType = self::ERROR_TYPES[$type] ?? 'Unknown error';
        $logMessage = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $errorType,
            $message,
            $file,
            $line
        );
        error_log($logMessage);
    }


    private static function displayErrorPage(int $type, string $message, string $file, int $line, ?array $trace = null): void
    {
        $errorType = self::ERROR_TYPES[$type] ?? 'Unknown error';
        $context = self::getCodeContext($file, $line);
        $debugVars = self::getDebugVariables();

        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');

        echo self::getErrorPageHtml($errorType, $message, $file, $line, $context, $debugVars);
        exit(1);
    }

    private static function displayGenericErrorPage(): void
    {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Server Error</title></head><body><h1>Oops! An error occurred.</h1><p>Please try again later.</p></body></html>';
        exit(1);
    }

    private static function getErrorPageHtml(string $errorType, string $message, string $file, int $line, array $context, array $debugVars): string
    {
        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Server Error</title><style>body{font-family: monospace; background-color: #f4f4f4; color: #333; padding: 20px;} .error-container{background-color: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 5px;} .error-type{color: #c00;} .error-message{font-weight: bold;} .error-file{color: #777;} .code-context{background-color: #eee; padding: 10px; border-radius: 3px; overflow-x: auto;} .debug-vars{margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;} .debug-vars h3{margin-top: 0;} .var-name{font-weight: bold;}</style></head><body><div class="error-container"><h1 class="error-type">' . htmlspecialchars($errorType, ENT_QUOTES, 'UTF-8') . '</h1><p class="error-message">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><p class="error-file">File: ' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . ', line: ' . htmlspecialchars((string)$line, ENT_QUOTES, 'UTF-8') . '</p>';

        if (!empty($context)) {
            $html .= '<div class="code-context"><pre>';
            foreach ($context as $num => $codeLine) {
                $lineNum = $line - (count($context) / 2) + $num;
                $isCurrentLine = ($lineNum == $line);
                $html .= '<span style="display: block; padding: 2px; background-color: ' . ($isCurrentLine ? '#f0f0f0' : 'transparent') . ';"><span style="color: #999;">' . htmlspecialchars((string)$lineNum, ENT_QUOTES, 'UTF-8') . ': </span>' . htmlspecialchars($codeLine, ENT_QUOTES, 'UTF-8') . '</span>';
            }
            $html .= '</pre></div>';
        }

        if (!empty($debugVars)) {
            $html .= '<div class="debug-vars"><h3>Debug Variables:</h3><pre>';
            foreach ($debugVars as $varName => $varValue) {
                $html .= '<div style="margin-bottom: 10px;"><span class="var-name">' . htmlspecialchars($varName, ENT_QUOTES, 'UTF-8') . ':</span> ' . htmlspecialchars(print_r($varValue, true), ENT_QUOTES, 'UTF-8') . '</div>';
            }
            $html .= '</pre></div>';
        }

        $html .= '</div></body></html>';
        return $html;
    }


    private static function getCodeContext(string $file, int $line): array
    {
        return file_exists($file) ? array_slice(file($file), max(0, $line - 6), 10) : [];
    }

    private static function getDebugVariables(): array
    {
        return function_exists('get_defined_vars') ? self::filterDebugVariables(get_defined_vars()) : [];
    }

    private static function filterDebugVariables(array $vars): array
    {
        $excludedVars = ['GLOBALS', '_SERVER', '_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_REQUEST', '_ENV'];
        return array_diff_key($vars, array_flip($excludedVars));
    }
}

/**
 * Инициализация обработчика ошибок.
 */
ErrorHandler::initialize();

// Гарантируем однократное создание директорий при первом запросе
ensureDirectoryExists(LOG_DIR); // Логи
ensureDirectoryExists(CACHE_MESSAGES_SUBDIR); // Кэш сообщений
ensureDirectoryExists(CACHE_DIR); // Базовая директория cache - избыточно, но безопасно


/**
 * Функция для отладки переменных.

 */
function debug($var, string $label = ''): void
{
    if (!Config::SECURITY['debug_mode']) {
        return;
    }

    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $trace['file']);

    $output = sprintf(
        '<div style="background:#1e1f1c; margin:10px 0; padding:10px; border-radius:4px; font-family:monocraft;">
            <div style="color:#a6e22e; margin-bottom:5px;">%s:%d %s</div>
            <pre style="margin:0; color:#f8f8f2;">%s</pre>
        </div>',
        $file,
        $trace['line'],
        $label ? "[$label]" : '',
        print_r($var, true)
    );

    if (headers_sent() || php_sapi_name() === 'cli') {
        echo $output;
    } else {
        error_log(strip_tags($output));
    }
}


/**
 * Очистка входящих данных.
 */
function sanitizeInput(string $data): string
{
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}


/**
 * Логирование запросов сообщений.
 */
function logRequest(mysqli $mysqli, string $messageId, string $requestType): void
{
    if (!Config::SECURITY['enable_logging']) {
        return;
    }

    $logEntry = json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'message_id' => $messageId,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'request_type' => $requestType
    ]) . "\n";

    error_log('LOG_DIR value: ' . LOG_DIR);
    // ensureDirectoryExists(LOG_DIR); // Удален избыточный вызов

    $logFile = CACHE_DIR . '/' . LOG_DIR . '/access.log';
    $fp = fopen($logFile, 'a');
    if ($fp && flock($fp, LOCK_EX)) {
        fwrite($fp, $logEntry);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}


/**
 * Определение типа клиента.
 */
function getClientInfo(): array
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $patterns = [
        'curl' => '/curl/i',
        'wget' => '/wget/i',
        'powershell' => '/PowerShell|WindowsPowerShell/i',
        'httpie' => '/HTTPie/i',
        'postman' => '/Postman/i',
    ];

    foreach ($patterns as $type => $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return ['is_cli' => true, 'type' => 'cli', 'name' => $type];
        }
    }

    return ['is_cli' => false, 'type' => 'web', 'name' => 'browser'];
}


/**
 * Отправка XML ответа об ошибке.
 */
function sendXmlError(string $message, int $code = 500): void
{
    http_response_code($code);
    header('Content-Type: application/xml; charset=utf-8');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<error><code>" . htmlspecialchars((string) $code) . "</code><message>" . htmlspecialchars($message) . "</message><timestamp>" . date('Y-m-d H:i:s') . "</timestamp></error>";
    exit;
}


/**
 * Проверка лимита запросов.
 */
function checkRequestLimit(mysqli $mysqli, string $ipAddress): int
{
    if (!Config::SECURITY['enable_rate_limit']) {
        return 0;
    }

    if (Config::CACHE['cache_driver'] === 'redis') {
        $redis = getRedisClient();
        if ($redis) {
            $rateLimitKey = 'rate_limit:' . $ipAddress;
            $requestCount = $redis->get($rateLimitKey);
            if ($requestCount === false) {
                $redis->setex($rateLimitKey, Config::SECURITY['rate_time'], 1);
                return 0;
            } else {
                if ((int)$requestCount >= Config::SECURITY['rate_limit']) {
                    $ttl = $redis->ttl($rateLimitKey);
                    return max(0, $ttl);
                } else {
                    $redis->incr($rateLimitKey);
                    return 0;
                }
            }
        }
        error_log('Redis unavailable, switching to file-based rate limiting.');
    }

    if (Config::CACHE['cache_driver'] !== 'redis' || !isset($redis) || !$redis) {
        return checkFileBasedRequestLimit($ipAddress);
    }
    return 0;
}


/**
 * Проверка лимита запросов, файловая реализация.
 */
function checkFileBasedRequestLimit(string $ipAddress): int
{
    // ensureDirectoryExists(LOG_DIR); // Удален избыточный вызов директория создается в начале скрипта

    $limits = [];
    $rateLimitFilePath = CACHE_DIR . '/' . RATE_LIMIT_FILE;

    if (file_exists($rateLimitFilePath)) {
        $fp = fopen($rateLimitFilePath, 'r+');
        if ($fp && flock($fp, LOCK_EX)) {
            $limits = json_decode(file_get_contents($rateLimitFilePath), true) ?: [];

            // Записываем обновленные данные
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($limits));
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    } else {
        // Создаем первую запись
        file_put_contents($rateLimitFilePath, json_encode([
            $ipAddress => ['count' => 1, 'time' => time()]
        ]));
    }
    return 0;
}


/**
 * Подсчет доступных комбинаций ID.
 */
function countAvailableCombinations(mysqli $mysqli, int $length): int
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $totalCombinations = pow(strlen($chars), $length);

    $stmt = $mysqli->prepare("SELECT COUNT(*) as used FROM messages WHERE LENGTH(random_id) = ?");
    if (!$stmt) {
        error_log('Ошибка подготовки запроса к базе данных: ' . $mysqli->error);
        return $totalCombinations;
    }
    $stmt->bind_param('i', $length);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $totalCombinations - (int) $row['used'];
}


/**
 * Генерация уникального ID сообщения.
 */
function generateUniqueId(mysqli $mysqli, int $length = 4): string
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxLength = Config::SECURITY['max_id_length'];
    $maxAttempts = 10;

    while ($length <= $maxLength) {
        if (countAvailableCombinations($mysqli, $length) < pow(strlen($chars), $length) * 0.1) {
            error_log("Warning: running out of {$length}-character IDs.");
            $length++;
            continue;
        }

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $randomId = '';
            for ($i = 0; $i < $length; $i++) {
                $randomId .= $chars[random_int(0, strlen($chars) - 1)];
            }
            if (!messageIdExists($mysqli, $randomId)) {
                if ($length > 4) {
                    error_log("Notice: generated ID of length $length: $randomId");
                }
                return $randomId;
            }
        }
        $length++;
    }

    throw new Exception('Failed to generate unique ID.');
}

/**
 * Проверка существования ID сообщения в БД.
 */
function messageIdExists(mysqli $mysqli, string $messageId): bool
{
    $stmt = $mysqli->prepare("SELECT 1 FROM messages WHERE BINARY random_id = ?");
    if (!$stmt) {
        error_log('Ошибка подготовки запроса к базе данных: ' . $mysqli->error);
        return false;
    }
    $stmt->bind_param('s', $messageId);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}


/** Шифрование AES-256-CBC */
const ENCRYPTION_METHOD = 'aes-256-cbc';
const ENCRYPTION_KEY_LENGTH = 32;
const ENCRYPTION_IV_LENGTH = 16;


/**
 * Генерация безопасного ключа шифрования.
 */
function generateSecureKey(): string
{
    return openssl_random_pseudo_bytes(ENCRYPTION_KEY_LENGTH);
}


/**
 * Генерация безопасного вектора инициализации (IV).
 */
function generateSecureIV(): string
{
    return openssl_random_pseudo_bytes(ENCRYPTION_IV_LENGTH);
}


/**
 * Шифрование сообщения.
 */
function encryptMessageWithId(string $message, string $id): string
{
    $iv = generateSecureIV();
    $key = hash('sha256', $id, true);
    $encrypted = openssl_encrypt($message, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($encrypted === false) {
        throw new Exception('Шифрование не удалось');
    }
    return base64_encode($iv . $encrypted);
}


/**
 * Дешифрование сообщения.
 */
function decryptMessageWithId(string $encryptedData, string $id): string
{
    $decoded = base64_decode($encryptedData);
    $iv = substr($decoded, 0, ENCRYPTION_IV_LENGTH);
    $encrypted = substr($decoded, ENCRYPTION_IV_LENGTH);
    $key = hash('sha256', $id, true);
    $decrypted = openssl_decrypt($encrypted, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($decrypted === false) {
        throw new Exception('Дешифрование не удалось');
    }
    return $decrypted;
}


/**
 * Измерение пинга до сервера.
 */
function measurePing(): mixed
{
    $results = [];
    $errors = 0;
    $pingTests = Config::SECURITY['ping_tests'];
    $pingTimeout = Config::SECURITY['ping_timeout'];
    $targetUrl = Config::APP['base_url'] . '/api/ping';

    for ($i = 0; $i < $pingTests; $i++) {
        $startTime = microtime(true);
        $ch = curl_init($targetUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => $pingTimeout,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if (curl_exec($ch) !== false) {
            $time = round((microtime(true) - $startTime) * 1000, 2);
            $results[] = $time;
        } else {
            $errors++;
        }
        curl_close($ch);
        usleep(100000);
    }

    if (empty($results)) {
        return false;
    }

    return [
        'min' => round(min($results), 2),
        'max' => round(max($results), 2),
        'avg' => round(array_sum($results) / count($results), 2),
        'success_rate' => round((count($results) / $pingTests) * 100),
        'results_ms' => $results,
    ];
}


// --- Роутинг ---
$path = sanitizeInput(rtrim(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), '/'));
$clientInfo = getClientInfo();
$mysqli = new mysqli(
    Config::DATABASE['host'],
    Config::DATABASE['username'],
    Config::DATABASE['password'],
    Config::DATABASE['database'],
    Config::DATABASE['port']
);

if ($mysqli->connect_error) {
    error_log('Database connection error: ' . $mysqli->connect_error);
    sendXmlError('Database connection error', 503);
}
$mysqli->set_charset('utf8mb4');


// Вывод версии приложения и репозитория
if ($clientInfo['is_cli']) {
    echo Config::APP['name'] . ' ' . Config::APP['version'] . "\n";
    echo "Repository: " . Config::APP['repository'] . "\n";
} elseif (empty($path)) {
    // Добавляем вывод в консоль браузера
    echo '<script>
        console.log("' . Config::APP['name'] . ' ' . Config::APP['version'] . '");
        console.log("Repository: ' . Config::APP['repository'] . '");
    </script>';
}

$segments = explode('/', $path);
$basePath = $segments[0];
$subPath = $segments[1] ?? null;

if ($basePath === 'ping') {
    if (!Config::SECURITY['enable_ping']) {
        sendXmlError('Ping service disabled', 403);
    }

    $pingData = measurePing();
    if ($pingData === false) {
        sendXmlError('Ping measurement error.', 500);
    }

    header('Content-Type: text/plain');
    header('Cache-Control: no-cache, no-store, must-revalidate');

    echo "Testing connection to " . Config::APP['base_url'] . "...\n";
    echo "Client: {$clientInfo['name']}\n\n";

    if (isset($pingData['results_ms'])) {
        foreach ($pingData['results_ms'] as $i => $time) {
            echo "Test " . ($i + 1) . ": " . $time . " ms\n";
        }
    }

    echo "\nResults:\n";
    echo "Min: " . $pingData['min'] . " ms\n";
    echo "Max: " . $pingData['max'] . " ms\n";
    echo "Avg: " . $pingData['avg'] . " ms\n";
    echo "Total time: " . round(array_sum($pingData['results_ms']), 2) . " ms\n";
    echo "Success rate: " . $pingData['success_rate'] . "%\n";
    echo "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    echo "Protocol: " . (isset($_SERVER['HTTPS']) ? 'HTTPS' : 'HTTP') . "\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n";

    exit;
}

if ($basePath === 'ip') {
    if (!Config::SECURITY['enable_ip_check']) {
        sendXmlError('IP address check disabled', 403);
    }
    header('Content-Type: text/plain');
    die($_SERVER['REMOTE_ADDR'] . "\n");
}

if (preg_match('/^[a-zA-Z0-9]{3,10}$/', $basePath)) {
    $messageId = $basePath;

    $ipAddress = $_SERVER['REMOTE_ADDR'];
    if (($waitTime = checkRequestLimit($mysqli, $ipAddress)) > 0) {
        sendXmlError("Too many requests. Please wait {$waitTime} seconds.", 429);
    }

    $messageData = getMessageWithCache($mysqli, $messageId);

    if (!$messageData) {
        sendXmlError('Message not found', 404);
    }

    // Логируем запрос асинхронно если возможно
    if (Config::SECURITY['enable_logging']) {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
            logRequest($mysqli, $messageId, 'read');
        } else {
            logRequest($mysqli, $messageId, 'read');
        }
    }

    $message = $messageData['message'];
    if ($messageData['encrypted']) {
        try {
            $message = decryptMessageWithId($message, $messageId);
        } catch (Exception $e) {
            sendXmlError('Message decryption error.', 500);
        }
    }

    if ($clientInfo['is_cli']) {
        header('Content-Type: text/plain');
        echo "Message:\n$message\n";
    } else {
        if (isset($_GET['hl'])) {
            include 'templates/view_message_highlight.php';
            exit;
        }
        // raw сообщения
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="en"><head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="color-scheme" content="light dark">';
        echo '<link rel="shortcut icon" href="' . htmlspecialchars(Config::APP['favicon']) . '" type="image/x-icon">';
        echo '</head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">';
        echo htmlspecialchars($message);
        echo '</pre></body></html>';
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    if (($waitTime = checkRequestLimit($mysqli, $ipAddress)) > 0) {
        sendXmlError("Too many requests. Please wait {$waitTime} seconds.", 429);
    }

    $rawMessage = file_get_contents('php://input') ?: ($_POST['kmi'] ?? $_POST['message'] ?? '');
    if (strlen($rawMessage) > Config::SECURITY['max_message_size']) {
        sendXmlError('Message size exceeds limit.', 413);
    }
    if (empty($rawMessage)) {
        sendXmlError('Empty message.', 400);
    }

    try {
        $randomId = generateUniqueId($mysqli);
        $encrypted = Config::SECURITY['enable_encryption'];
        $finalMessage = $encrypted ? encryptMessageWithId($rawMessage, $randomId) : $rawMessage;
        $source = $clientInfo['is_cli'] ? 'cli' : 'web';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $stmt = $mysqli->prepare('INSERT INTO messages (message, random_id, source, user_agent, encrypted) VALUES (?, ?, ?, ?, ?)');
        if (!$stmt) {
            sendXmlError('Database error while saving message.', 500);
        }
        $stmt->bind_param('ssssi', $finalMessage, $randomId, $source, $userAgent, $encrypted);
        if (!$stmt->execute()) {
            error_log('Query execution error: ' . $stmt->error);
            sendXmlError('Failed to save message.', 500);
        }
        $stmt->close();

        if ($clientInfo['is_cli']) {
            // Убираем дублирующийся вывод версии и репозитория
            echo json_encode([
                'success' => true, 
                'id' => $randomId, 
                'url' => Config::APP['base_url'] . '/' . $randomId
            ], JSON_UNESCAPED_SLASHES) . "\n\n";
            echo Config::APP['base_url'] . '/' . $randomId . "\n";
        } else {
            // Web output - only JSON, no console logs
            header('Content-Type: application/json');
            header('X-App-Version: ' . Config::APP['name'] . ' v' . Config::APP['version']);
            header('X-App-Repository: ' . Config::APP['repository']);
            echo json_encode([
                'success' => true, 
                'id' => $randomId, 
                'url' => Config::APP['base_url'] . '/' . $randomId
            ], JSON_UNESCAPED_SLASHES);
        }
        exit;

    } catch (Exception $e) {
        error_log('Exception while creating message: ' . $e->getMessage());
        sendXmlError('Failed to save message.', 500);
    }
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST' && empty($path)) {
    include 'templates/create_message_form.php';
    exit;
}


http_response_code(404);
include __DIR__ . '/errors/404.html';
exit;
$mysqli->close();