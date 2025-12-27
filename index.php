     
      <?php
date_default_timezone_set('America/Bogota');

$baseDir = __DIR__ . '/aqui';
$relativePath = isset($_GET['path']) ? $_GET['path'] : '';

$relativePath = trim($relativePath, '/');
if ($relativePath === '.' || $relativePath === './') {
    $relativePath = '';
}


// Evita rutas que suban directorios (ataques con ../)
if (strpos($relativePath, '..') !== false) {
    die("Ruta no permitida.");
}

$currentDir = rtrim($baseDir . '/' . $relativePath, '/');

if (!is_dir($currentDir)) {
    mkdir($currentDir, 0777, true);
}

// Subida de archivos al directorio actual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $destination = $currentDir . '/' . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $destination);
    header("Location: ?path=" . urlencode($relativePath));
    exit;
}

$items = scandir($currentDir);
$items = array_diff($items, ['.', '..']);
$currentTime = date("d/m/Y H:i:s");
?>

<pre class="ascii-art">
      _  _____ _____  _    _ _____   ____  
     | |/ ____|  __ \| |  | |  __ \ / __ \ 
     | | |    | |  | | |  | | |__) | |  | |
 _   | | |    | |  | | |  | |  _  /| |  | |
| |__| | |____| |__| | |__| | | \ \| |__| |
 \____/ \_____|_____/ \____/|_|  \_\\____/ 
</pre>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="40">
    <title>aqui File Uploader</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="timestamp"><?= $currentTime ?> 🇨🇴</div>
    <h1>PROYECT FILE UPLOADER</h1>

<?php if ($relativePath === ''): ?>
    <!-- Formulario de subida solo en la raíz -->
    <form action="?path=" method="post" enctype="multipart/form-data">
        <label class="neon-button">
            SELECCIONAR ARCHIVO
            <input type="file" name="file" onchange="this.form.submit()">
        </label>
    </form>
<?php endif; ?>


    <p>TOTAL DE ELEMENTOS: <strong><?= count($items) ?></strong></p>

    <!-- Botón volver -->
    <?php if ($relativePath): ?>
        <p><a href="?path=<?= urlencode(dirname($relativePath)) ?>" class="neon-button">⬅️ VOLVER</a></p>
    <?php endif; ?>

    <ul class="file-list">
        <?php foreach ($items as $item):
            $itemPath = $relativePath ? $relativePath . '/' . $item : $item;
            $fullPath = $baseDir . '/' . $itemPath;
            $isDir = is_dir($fullPath);
        ?>
            <li class="file-item <?= $isDir ? 'folder' : 'file' ?>">
                <?php if ($isDir): ?>
                    <a href="?path=<?= urlencode($itemPath) ?>" class="folder"><?= strtoupper(htmlspecialchars($item)) ?> (CARPETA)</a>
                <?php else: ?>
                    <a href="aqui/<?= urlencode($itemPath) ?>" download><?= strtoupper(htmlspecialchars($item)) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>
