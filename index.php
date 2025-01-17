<?php
require_once('config.php');

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function logRequest($mysqli, $message_id, $request_type) {
    global $security_config;
    
    if (!$security_config['enable_logging']) {
        return;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $sql = "INSERT INTO message_logs (message_id, ip_address, user_agent, request_type) 
            VALUES (?, ?, ?, ?)";
            
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssss", $message_id, $ip, $user_agent, $request_type);
        $stmt->execute();
        $stmt->close();
    }
}

$is_curl = (strpos($_SERVER['HTTP_USER_AGENT'] ?? '', 'curl') !== false);

$mysqli = new mysqli($database_config['host'], $database_config['username'], 
                    $database_config['password'], $database_config['database'], 
                    $database_config['port']);
$mysqli->set_charset("utf8mb4");

if ($mysqli->connect_error) {
    error_log("Connection failed: " . $mysqli->connect_error);
    die(json_encode(['error' => 'Database connection failed']));
}

function checkRequestLimit($mysqli, $ip_address) {
    global $security_config;
    
    if (!$security_config['enable_rate_limit']) {
        return 0;
    }
    
    $limit = $security_config['rate_limit'];
    $timeframe = $security_config['rate_time'];
    
    $sql = "INSERT INTO request_limits (ip_address) 
            VALUES (?) 
            ON DUPLICATE KEY UPDATE 
            request_count = IF(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(last_request) > ?, 1, request_count + 1),
            last_request = IF(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(last_request) > ?, NOW(), last_request)";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("sii", $ip_address, $timeframe, $timeframe);
        $stmt->execute();
        $stmt->close();
    }

    $sql = "SELECT request_count, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(last_request) as time_passed 
            FROM request_limits WHERE ip_address = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && $row['request_count'] > $limit) {
            $wait_time = $timeframe - $row['time_passed'];
            return $wait_time > 0 ? $wait_time : 0;
        }
    }
    return 0;
}

function generateUniqueId($mysqli, $length = 4) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $maxAttempts = 10;
    
    while ($length <= 10) { 
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $random_id = '';
            for ($i = 0; $i < $length; $i++) {
                $random_id .= $chars[random_int(0, strlen($chars) - 1)];
            }
            
            $sql = "SELECT COUNT(*) as count FROM messages WHERE BINARY random_id = ?";
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("s", $random_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();
                
                if ($row['count'] == 0) {
                    return $random_id;
                }
            }
        }
        $length++;
    }
    throw new Exception('Could not generate unique ID');
}

define('ENCRYPTION_METHOD', 'aes-256-cbc');
define('ENCRYPTION_KEY_LENGTH', 32);
define('ENCRYPTION_IV_LENGTH', 16);

function generateSecureKey() {
    return openssl_random_pseudo_bytes(ENCRYPTION_KEY_LENGTH);
}

function generateSecureIV() {
    return openssl_random_pseudo_bytes(ENCRYPTION_IV_LENGTH);
}

function encryptMessageWithId($message, $id) {
    $iv = generateSecureIV();
    
    $key = hash('sha256', $id, true);
    
    // Шифруем сообщение
    $encrypted = openssl_encrypt(
        $message,
        ENCRYPTION_METHOD,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
    
    if ($encrypted === false) {
        throw new Exception('Encryption failed');
    }
    
    return base64_encode($iv . $encrypted);
}

function decryptMessageWithId($encrypted_data, $id) {

    $decoded = base64_decode($encrypted_data);
    
    $iv = substr($decoded, 0, ENCRYPTION_IV_LENGTH);
    
    $encrypted = substr($decoded, ENCRYPTION_IV_LENGTH);
    
    $key = hash('sha256', $id, true);
    
    $decrypted = openssl_decrypt(
        $encrypted,
        ENCRYPTION_METHOD,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
    
    if ($decrypted === false) {
        throw new Exception('Decryption failed');
    }
    
    return $decrypted;
}

$path = cleanInput(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

if (!empty($path) && strlen($path) >= 3 && strlen($path) <= 10 && preg_match('/^[a-zA-Z0-9]+$/', $path)) {
    $sql = "SELECT message, source, user_agent, encrypted FROM messages WHERE BINARY random_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $path);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            logRequest($mysqli, $path, 'read');
            $message = $row['message'];
            
            if ($row['encrypted']) {
                $message = decryptMessageWithId($message, $path);
            }
            
            $source = $row['source'];
            $user_agent = $row['user_agent'];
            
            if ($is_curl) {
                header('Content-Type: text/plain');
                echo "Message:\n$message\n";
            } else {
                $highlight = isset($_GET['hl']);
                
                if ($highlight) {
                    ?>
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>View Message</title>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
                        <style>
                            body {
                                margin: 0;
                                padding: 20px;
                                background-color: #272822;
                            }
                            #editor {
                                width: 100%;
                                height: calc(100vh - 40px);
                                font-size: 14px;
                            }
                        </style>
                    </head>
                    <body>
                        <div id="editor"><?php echo htmlspecialchars($message); ?></div>
                        <script>
                            var editor = ace.edit("editor");
                            editor.setTheme("ace/theme/monokai");
                            editor.session.setMode("ace/mode/text");
                            editor.setReadOnly(true);
                            editor.setShowPrintMargin(false);
                            editor.renderer.setShowGutter(true);
                        </script>
                    </body>
                    </html>
                    <?php
                } else {
                    header('Content-Type: text/plain');
                    echo $message;
                    exit;
                }
            }
            exit;
        } else {
            if ($is_curl) {
                header('Content-Type: text/plain');
                echo "Message not found\n";
            } else {
                echo "Message not found";
            }
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $wait_time = checkRequestLimit($mysqli, $ip_address);
    
    if ($wait_time > 0) {
        if ($is_curl) {
            header('Content-Type: text/plain');
            echo "Too many requests. Please wait {$wait_time} seconds.\n";
        } else {
            echo json_encode([
                'error' => true,
                'message' => "Too many requests. Please wait {$wait_time} seconds."
            ]);
        }
        exit;
    }
    
    try {
        $raw_message = file_get_contents("php://input");
        
        if (empty($raw_message)) {
            $raw_message = $_POST['message'] ?? '';
        }
        
        if (!empty($raw_message)) {
            try {
                $random_id = generateUniqueId($mysqli);
                
                $encrypted = $security_config['enable_encryption'];
                $final_message = $encrypted ? encryptMessageWithId($raw_message, $random_id) : $raw_message;
                
                $source = $is_curl ? 'cli' : 'web';
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

                $sql = "INSERT INTO messages (message, random_id, source, user_agent, encrypted) 
                        VALUES (?, ?, ?, ?, ?)";
                if ($stmt = $mysqli->prepare($sql)) {
                    $stmt->bind_param("ssssi", $final_message, $random_id, $source, $user_agent, $encrypted);
                    
                    if ($stmt->execute()) {
                        if ($is_curl) {
                            header('Content-Type: text/plain');
                            echo $example_link . '/' . $random_id . "\n";
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => true,
                                'id' => $random_id,
                                'url' => $example_link . '/' . $random_id
                            ]);
                        }
                    } else {
                        error_log("Execute failed: " . $stmt->error);
                        die(json_encode(['error' => 'Failed to save message']));
                    }
                    $stmt->close();
                } else {
                    error_log("Prepare failed: " . $mysqli->error);
                    die(json_encode(['error' => 'Failed to prepare statement']));
                }
            } catch (Exception $e) {
                error_log("Exception: " . $e->getMessage());
                die(json_encode(['error' => 'Internal server error']));
            }
        } else {
            die(json_encode(['error' => 'Empty message']));
        }
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        die(json_encode(['error' => 'Internal server error']));
    }
    exit;
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Create Message</title>
    <style>
        @font-face {
            font-family: monocraft;
            src: url(./style/Monocraft.ttf);
        }

        @font-face {
            font-family: SFP;
            src: url(./style/SFProText-Light.ttf);
        }

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
            box-sizing: border-box;
        }

        textarea {
            width: 100%;
            font-size: 16px;
            font-weight: 700;
            border-radius: 4px;
            resize: vertical;
            background-color: #3c3c3a;
            color: #f8f8f2;
            border: 2px solid #ffffff;
            max-height: 250px;
            min-height: 250px;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            font-family: "monocraft";
            padding: 10px 20px;
            margin: 20px 10px 10px 0px;
            font-size: 16px;
            background-color: #0f33ff;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 0px;
        }

        button:hover {
            background-color: #0f28b4;
        }

        #response {
            margin-top: 20px;
            padding: 10px;
            background-color: #3c3c3a;
            border-radius: 4px;
            color: #f8f8f2;
            display: none;
        }

        a {
            color: #0f33ff !important;
            text-decoration: none;
            cursor: pointer;
        }

        a:hover {
            text-decoration: underline;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 4px;
            z-index: 1000;
        }

        .notification.error {
            background: #f44336;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
            }

            textarea {
                font-size: 14px;
                max-height: 200px;
                min-height: 200px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Message</h1>
        <form id="messageForm" method="post">
            <textarea id="message" name="message" placeholder="Enter your message..." required></textarea>
            <button type="submit">Send</button>
        </form>
        <div id="response"></div>
    </div>

    <script>
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData.get('message')
            })
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('response');
                responseDiv.style.display = 'block';
                
                if (data.success) {
                    const url = data.url;
                    responseDiv.innerHTML = `
                        Message successfully saved. 
                        <a href="#" onclick="copyToClipboard('${url}'); return false;" 
                           title="Click to copy">${url}</a>`;
                } else {
                    responseDiv.innerHTML = 'Error saving message';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('response').innerHTML = 'An error occurred while sending';
            });
        });

        document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
            if (this.disabled) {
                showNotification('Form has already been submitted', 'error');
            }
        });

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showNotification('URL copied to clipboard');
            });
        }

        function showNotification(message, type = '') {
            const notification = document.createElement('div');
            notification.className = 'notification' + (type ? ' ' + type : '');
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 2000);
        }
    </script>
</body>
</html>
