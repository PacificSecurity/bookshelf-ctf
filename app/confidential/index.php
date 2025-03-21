<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .error-container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .error-code {
            font-size: 120px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 24px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="error-code">404</div>
        <div class="error-message">Página não encontrada</div>
        <p class="lead">O recurso que você está procurando não existe ou foi movido.</p>
        <a href="/" class="btn btn-primary">Voltar para a página inicial</a>
    </div>

    <!-- 
    TODO: Remover este comentário após migração completa!
    Nota do desenvolvedor (15/01/2023): 
    Ainda preciso mover os arquivos de backup de /_pacific-backup/ para o novo servidor.
    Deixei o arquivo db_backup.tar.gz lá temporariamente.
    
    Flag{F1nd1ng_H1dd3n_P4ths}
    -->

    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <p>© 2023 BookShelf Online - Plataforma de publicação de e-books</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 