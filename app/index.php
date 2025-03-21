<?php
require_once 'config.php';

// Buscar livros publicados
$sql = "SELECT e.*, u.username AS publisher_name 
        FROM ebooks e 
        JOIN users u ON e.publisher_id = u.id 
        WHERE e.published = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookShelf Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        .book-card {
            margin-bottom: 20px;
            height: 100%;
        }
    </style>
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
                                <?php echo $_SESSION['username']; ?>
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
        <div class="jumbotron bg-light p-5 rounded">
            <h1>Bem-vindo à BookShelf Online</h1>
            <p class="lead">A melhor plataforma de e-books para leitores, autores e editores.</p>
            <hr class="my-4">
            <p>Explore nossa biblioteca, publique seus trabalhos ou descubra novos autores.</p>
            <?php if (!isLoggedIn()): ?>
                <div class="mt-3">
                    <a href="register.php" class="btn btn-primary me-2">Cadastre-se</a>
                    <a href="login.php" class="btn btn-secondary">Fazer Login</a>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="my-4">E-books em Destaque</h2>
        
        <div class="row">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($book = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card book-card">
                            <img src="<?php echo $book['cover_url'] ?: 'assets/no-cover.jpg'; ?>" class="card-img-top" alt="Capa do livro">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $book['title']; ?></h5>
                                <p class="card-text">Por <?php echo $book['author']; ?></p>
                                <p class="card-text"><small class="text-muted">Publicado por: <?php echo $book['publisher_name']; ?></small></p>
                                <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-primary">Ver detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <p>Nenhum e-book encontrado.</p>
                </div>
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