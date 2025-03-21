<?php
require_once 'config.php';

// Verificar se o ID do livro foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('index.php');
}

$book_id = (int)$_GET['id'];

// Obter detalhes do livro
$sql = "SELECT e.*, u.username AS publisher_name 
        FROM ebooks e 
        JOIN users u ON e.publisher_id = u.id 
        WHERE e.id = $book_id AND e.published = 1";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    redirect('index.php');
}

$book = $result->fetch_assoc();

// Obter comentários do livro
$sql = "SELECT c.*, u.username 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.ebook_id = $book_id 
        ORDER BY c.created_at DESC";
$comments_result = $conn->query($sql);

// Processar envio de comentário
$comment_success = '';
$comment_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isLoggedIn()) {
    $comment_text = sanitize_input($_POST['comment']);
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO comments (ebook_id, user_id, comment) VALUES ($book_id, $user_id, '$comment_text')";
    
    if ($conn->query($sql) === TRUE) {
        $comment_success = 'Comentário enviado com sucesso!';
        // Recarregar os comentários
        $sql = "SELECT c.*, u.username 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.ebook_id = $book_id 
                ORDER BY c.created_at DESC";
        $comments_result = $conn->query($sql);
    } else {
        $comment_error = 'Erro ao enviar comentário: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - BookShelf Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">BookShelf Online</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                </ul>
                <form class="d-flex me-2" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Buscar livros ou autores" aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Buscar</button>
                </form>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if (isEditor()): ?>
                                    <li><a class="dropdown-item" href="publisher/">Painel do Editor</a></li>
                                <?php endif; ?>
                                <?php if (isAdmin()): ?>
                                    <li><a class="dropdown-item" href="admin/">Painel Admin</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Cadastrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-4">
                <img src="<?php echo htmlspecialchars($book['cover_url'] ?: 'assets/no-cover.jpg'); ?>" class="img-fluid rounded" alt="Capa do livro">
            </div>
            <div class="col-md-8">
                <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                <p class="lead">Por <?php echo htmlspecialchars($book['author']); ?></p>
                <p><small class="text-muted">Publicado por: <?php echo htmlspecialchars($book['publisher_name']); ?></small></p>
                
                <div class="my-4">
                    <h4>Descrição</h4>
                    <p><?php echo htmlspecialchars($book['description']); ?></p>
                </div>
                
                <div class="d-grid gap-2 d-md-block">
                    <a href="#" class="btn btn-primary">Ver amostra</a>
                    <a href="#" class="btn btn-success">Comprar</a>
                </div>
            </div>
        </div>
        
        <div class="mt-5">
            <h3>Comentários</h3>
            
            <?php if ($comment_success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($comment_success); ?></div>
            <?php endif; ?>
            
            <?php if ($comment_error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($comment_error); ?></div>
            <?php endif; ?>
            
            <?php if (isLoggedIn()): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Adicionar comentário</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <a href="login.php">Faça login</a> para comentar neste e-book.
                </div>
            <?php endif; ?>
            
            <?php if ($comments_result && $comments_result->num_rows > 0): ?>
                <div class="list-group">
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?php echo htmlspecialchars($comment['username']); ?></h5>
                                <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($comment['comment']); ?></p>
                            
                            <?php if ($comment['publisher_response']): ?>
                                <div class="border-top mt-2 pt-2">
                                    <small class="text-muted">Resposta do editor:</small>
                                    <p class="mb-0"><?php echo htmlspecialchars($comment['publisher_response']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Nenhum comentário ainda. Seja o primeiro a comentar!</p>
            <?php endif; ?>
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