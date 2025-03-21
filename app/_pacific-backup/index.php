<?php
// Um simples listador de arquivos para o diretório _backup

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Files</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .file-list {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .file-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .file-item:last-child {
            border-bottom: none;
        }
        .file-item a {
            color: #007bff;
            text-decoration: none;
            display: block;
        }
        .file-item a:hover {
            text-decoration: underline;
        }
        .file-size {
            color: #777;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>Arquivos de Backup</h1>
    
    <div class="file-list">
        <?php
        $files = scandir('.');
        $backupFiles = [];
        
        // Filtrar apenas arquivos reais (ignorar . e ..) e arquivos .htaccess
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.php') {
                $backupFiles[] = $file;
            }
        }
        
        if (count($backupFiles) > 0) {
            foreach ($backupFiles as $file) {
                $fileSize = filesize($file);
                // Formatar tamanho do arquivo
                if ($fileSize < 1024) {
                    $formattedSize = $fileSize . ' B';
                } elseif ($fileSize < 1024 * 1024) {
                    $formattedSize = round($fileSize / 1024, 1) . ' KB';
                } else {
                    $formattedSize = round($fileSize / (1024 * 1024), 1) . ' MB';
                }
                
                echo '<div class="file-item">';
                echo '<a href="' . htmlspecialchars($file) . '">' . htmlspecialchars($file) . '</a>';
                echo '<span class="file-size"> - ' . $formattedSize . '</span>';
                echo '</div>';
            }
        } else {
            echo '<p>Nenhum arquivo de backup encontrado.</p>';
        }
        ?>
    </div>
</body>
</html> 