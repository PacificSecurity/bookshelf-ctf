<?php
require_once '../config.php';

// Verificar se o usuário está logado e é um editor
if (!isLoggedIn() || !isEditor()) {
    redirect('../login.php');
}

$success = '';
$error = '';

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $author = sanitize_input($_POST['author']);
    $description = sanitize_input($_POST['description']);
    
    // Sanitizar e validar a URL da capa
    $cover_url = sanitize_input($_POST['cover_url']);
    
    // Validar URL (permitir apenas URLs externas seguras, não internas)
    if (!empty($cover_url)) {
        // Lista de domínios permitidos
        $allowed_domains = array('example.com', 'images.bookshelf.com', 'flickr.com', 'imgur.com');
        
        $url_parts = parse_url($cover_url);
        $is_valid_url = false;
        
        if (isset($url_parts['host'])) {
            foreach ($allowed_domains as $domain) {
                if (preg_match('/\b' . preg_quote($domain, '/') . '$/', $url_parts['host'])) {
                    $is_valid_url = true;
                    break;
                }
            }
        }
        
        // Verifica se não é uma URL interna (localhost, 127.0.0.1, etc)
        if (!$is_valid_url && (
            !isset($url_parts['host']) || 
            $url_parts['host'] == 'localhost' || 
            $url_parts['host'] == '127.0.0.1' || 
            $url_parts['host'] == 'internal-service' ||
            strpos($url_parts['host'], '.local') !== false || 
            strpos($url_parts['host'], '.internal') !== false
        )) {
            $error = 'URL de capa inválida. Use apenas URLs de serviços de hospedagem confiáveis.';
            $cover_url = ''; // Limpa a URL
        }
    }
    
    // Se não houver erro, continuar o processamento
    if (empty($error)) {
        $published = isset($_POST['published']) ? 1 : 0;
        $publisher_id = $_SESSION['user_id'];
        
        // Inserir o e-book
        $sql = "INSERT INTO ebooks (title, author, description, cover_url, publisher_id, published) 
                VALUES ('$title', '$author', '$description', '$cover_url', $publisher_id, $published)";
        
        if ($conn->query($sql) === TRUE) {
            $success = 'E-book adicionado com sucesso!';
        } else {
            $error = 'Erro ao adicionar e-book: ' . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar E-book - BookShelf Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
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
                        <a class="nav-link active" href="add_book.php">Adicionar E-book</a>
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
        <h1>Adicionar Novo E-book</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
                <p><a href="index.php" class="alert-link">Voltar ao Painel</a></p>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h4>Informações do E-book</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="add_book.php">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título*</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="author" class="form-label">Autor*</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cover_url" class="form-label">URL da Capa</label>
                        <input type="url" class="form-control" id="cover_url" name="cover_url" placeholder="https://example.com/images/cover.jpg">
                        <div class="form-text">Para validar URLs de imagens, use o <a href="cover_validator.php">Validador de Capas</a>. Apenas URLs de serviços confiáveis são permitidas.</div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="published" checked>
                        <label class="form-check-label" for="published">Publicar imediatamente</label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Adicionar E-book</button>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
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