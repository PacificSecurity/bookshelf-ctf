<?php
require_once '../config.php';

// Verificar se o usuário está logado e é um editor
if (!isLoggedIn() || !isEditor()) {
    redirect('../login.php');
}

// Buscar e-books do editor
$publisher_id = $_SESSION['user_id'];
$sql = "SELECT * FROM ebooks WHERE publisher_id = $publisher_id ORDER BY created_at DESC";
$books_result = $conn->query($sql);

// Buscar comentários para responder
$sql = "SELECT c.*, e.title, u.username 
        FROM comments c 
        JOIN ebooks e ON c.ebook_id = e.id 
        JOIN users u ON c.user_id = u.id
        WHERE e.publisher_id = $publisher_id AND c.publisher_response IS NULL";
$comments_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Editor - BookShelf Online</title>
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
                        <a class="nav-link active" href="index.php">Painel do Editor</a>
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

    <div class="container my-4">
        <h1>Painel do Editor</h1>
        <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Seus E-books</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($books_result && $books_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Autor</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($book = $books_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($book['id']); ?></td>
                                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                                <td>
                                                    <?php if ($book['published']): ?>
                                                        <span class="badge bg-success">Publicado</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Rascunho</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="edit_book.php?id=<?php echo (int)$book['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>Você ainda não tem e-books cadastrados.</p>
                            <a href="add_book.php" class="btn btn-primary">Adicionar E-book</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Comentários para Responder</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($comments_result && $comments_result->num_rows > 0): ?>
                            <?php while ($comment = $comments_result->fetch_assoc()): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Comentário em "<?php echo htmlspecialchars($comment['title']); ?>"</h5>
                                        <p class="card-text"><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
                                        <form action="respond_comment.php" method="POST">
                                            <input type="hidden" name="comment_id" value="<?php echo (int)$comment['id']; ?>">
                                            <div class="mb-3">
                                                <label for="response_<?php echo (int)$comment['id']; ?>" class="form-label">Sua resposta:</label>
                                                <textarea class="form-control" id="response_<?php echo (int)$comment['id']; ?>" name="response" rows="2" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Responder</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Não há comentários para responder.</p>
                        <?php endif; ?>
                    </div>
                </div>
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