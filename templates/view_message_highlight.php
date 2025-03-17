<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <link rel="shortcut icon" href="<?= Config::APP['favicon'] ?>" type="image/x-icon">
    <link rel="stylesheet" href="./styles/highlight_style.css">
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/ace.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-textmate.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-xcode.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-javascript.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-python.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-java.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-php.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-html.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-css.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-sql.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-ruby.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-csharp.js"></script>
    <script data-cfasync="false" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-golang.js"></script>
    <link rel="stylesheet" href="./styles/highlight_style.css">
</head>
<body>
    <select id="themeSelector" class="theme-selector">
        <option value="monokai">Monokai</option>
        <option value="textmate">TextMate</option>
        <option value="xcode">xcode</option>
    </select>
    <div id="editor" class="code-editor"><?php echo htmlspecialchars($message); ?></div>
    <script src="./scripts/highlight.js"></script>
</body>
</html>