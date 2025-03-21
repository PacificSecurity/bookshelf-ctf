<?php
require_once '../config.php';

// Verificar se o usuário está logado e é um editor
if (!isLoggedIn() || !isEditor()) {
    redirect('../login.php');
}

// Verificar se o parâmetro user_id=1337 está presente
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Verificação especial - APENAS user_id=1337 pode acessar esta página
if ($user_id !== 1337) {
    // Bloquear acesso mesmo para editores se não tiver o ID correto
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acesso Negado - BookShelf Online</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
        <!-- 
        TODO: Remover esse comentário após atualização do sistema
        Nota do desenvolvedor: Criar validação de acesso mais segura
        para substituir o sistema atual baseado em IDs de usuário.
        Referência do publisher_dev no sistema: cover_validator.php?user_id=3468
        -->
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="../index.php">BookShelf Online</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Painel do Editor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_book.php">Adicionar E-book</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../logout.php">Sair</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h4><i class="bi bi-exclamation-triangle-fill"></i> Acesso Negado</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Você não tem permissão para acessar este recurso. Este recurso é restrito a usuários com privilégios administrativos.</p>
                            <input type="hidden" id="auth-check" value="admin-only" data-required="user_id_auth">
                            <a href="index.php" class="btn btn-primary">Voltar ao Painel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="bg-dark text-white py-3 mt-5 fixed-bottom">
            <div class="container text-center">
                <p>© 2023 BookShelf Online - Plataforma de publicação de e-books</p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

// Continuar apenas se for editor logado E user_id=1337
$url_result = '';
$error = '';
$preview_url = '';

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = sanitize_input($_POST['url']);
    
    if (empty($url)) {
        $error = 'Por favor, forneça uma URL válida.';
    } else {
        // Tentar buscar a URL
        try {
            $ch = curl_init();
            
            // Configurações do cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            // Executar a requisição
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            
            if ($response === false) {
                $error = 'Erro ao acessar a URL: ' . curl_error($ch);
            } else {
                $content_type = isset($info['content_type']) ? $info['content_type'] : '';
                
                // Verificar se é uma imagem ou conteúdo de texto
                if (strpos($content_type, 'image/') === 0) {
                    $preview_url = $url;
                    $url_result = 'URL validada com sucesso. É uma imagem do tipo: ' . $content_type;
                } else {
                    // Para conteúdo que não é imagem, mostrar os primeiros bytes
                    $url_result = 'Conteúdo recuperado da URL (não é uma imagem):<br><pre>' . htmlspecialchars($response) . '</pre>';
                }
            }
            
            curl_close($ch);
        } catch (Exception $e) {
            $error = 'Exceção ao processar a URL: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validador de Capas - BookShelf Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        .preview-container {
            margin-top: 20px;
            text-align: center;
        }
        .preview-container img {
            max-width: 100%;
            max-height: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        pre {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            max-height: 400px;
            overflow: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">BookShelf Online</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Painel do Editor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_book.php">Adicionar E-book</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cover_validator.php?user_id=<?php echo $user_id; ?>">Validador de Capas</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../logout.php">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1>Validador de Capas</h1>
        
        <div class="alert alert-success">
            <strong>Flag{Adm1n_Acc3ss_Grant3d}</strong>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4>Valide URLs de Capas</h4>
            </div>
            <div class="card-body">
                <p>Esta ferramenta permite validar URLs de capas para seus e-books, garantindo que elas sejam acessíveis e exibidas corretamente.</p>
                
                <form method="POST" action="cover_validator.php?user_id=<?php echo $user_id; ?>">
                    <div class="mb-3">
                        <label for="url" class="form-label">URL da Capa</label>
                        <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com/images/cover.jpg" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Validar URL</button>
                </form>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($url_result): ?>
                    <div class="alert alert-info mt-3">
                        <?php echo $url_result; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($preview_url): ?>
                    <div class="preview-container">
                        <h5>Pré-visualização:</h5>
                        <img src="<?php echo htmlspecialchars($preview_url); ?>" alt="Pré-visualização da capa">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <p>© 2023 BookShelf Online - Plataforma de publicação de e-books</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 